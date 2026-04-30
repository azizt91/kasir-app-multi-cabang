<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\CashFlow;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\ProductReportExport;
use App\Exports\StockReportExport;
use App\Exports\CashFlowReportExport;
use App\Exports\ShiftReportExport;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    private function getActiveBranchId()
    {
        $user = auth()->user();
        if (!$user) return null;
        if ($user->isSuperAdmin()) {
            return session('admin_active_branch_id');
        }
        return $user->branch_id;
    }

    private function checkAdminAccess()
    {
        if (!auth()->check()) {
            abort(401, 'Silakan login terlebih dahulu.');
        }
        
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses laporan.');
        }
    }

    public function index()
    {
        $this->checkAdminAccess();
        $user = auth()->user();
        
        // Get summary statistics
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        $stats = [
            'today_sales' => Transaction::where('status', 'completed')->where('payment_method', '!=', 'utang')->whereDate('created_at', $today)->sum('total_amount'),
            'today_transactions' => Transaction::where('status', 'completed')->whereDate('created_at', $today)->count(),
            'month_sales' => Transaction::where('status', 'completed')->where('payment_method', '!=', 'utang')->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'month_transactions' => Transaction::where('status', 'completed')->where('created_at', '>=', $thisMonth)->count(),
            'year_sales' => Transaction::where('status', 'completed')->where('payment_method', '!=', 'utang')->where('created_at', '>=', $thisYear)->sum('total_amount'),
            'year_transactions' => Transaction::where('status', 'completed')->where('created_at', '>=', $thisYear)->count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::lowStock()->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];

        // Recent transactions
        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Top selling products this month
        $branchId = $this->getActiveBranchId();
        $topProductsQuery = Product::select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.category_id',
                'products.selling_price',
                'products.image'
            ])
            ->selectRaw('SUM(transaction_items.quantity) as total_sold')
            ->selectRaw('SUM(transaction_items.subtotal) as total_revenue')
            ->with('category')
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.created_at', '>=', $thisMonth);
        
        if ($branchId) {
            $topProductsQuery->where('transactions.branch_id', $branchId);
        }

        $topProducts = $topProductsQuery->groupBy([
                'products.id',
                'products.name',
                'products.barcode',
                'products.category_id',
                'products.selling_price',
                'products.image'
            ])
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::with('category')
            ->lowStock($branchId)
            ->take(10)
            ->get();
        
        $activeWarehouse = $user->getActiveWarehouse();
        $lowStockProducts->each(function ($p) use ($activeWarehouse) { 
            $p->total_stock = $activeWarehouse ? $p->getStockInWarehouse($activeWarehouse->id) : $p->getTotalStock(); 
        });

        // Calculate real-time cash balance (Saldo Kas Saat Ini)
        // EXCLUDE pending adjustments
        $whereBranchTrx = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchPur = $branchId ? " AND warehouse_id IN (SELECT id FROM warehouses WHERE branch_id = $branchId)" : "";
        $whereBranchExp = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchCF = $branchId ? " AND branch_id = $branchId" : "";
        
        // Status condition for cash_flows: only approved or non-adjustment
        $statusCond = " AND (status = 'approved' OR is_adjustment = 0 OR status IS NULL)";

        $currentBalance = DB::table(DB::raw("(
            SELECT total_amount as debit, 0 as kredit FROM transactions WHERE status = 'completed' AND payment_method != 'utang'$whereBranchTrx
            UNION ALL
            SELECT 0, total_amount FROM purchases WHERE 1=1$whereBranchPur
            UNION ALL
            SELECT 0, amount FROM expenses WHERE 1=1$whereBranchExp
            UNION ALL
            SELECT CASE WHEN type = 'in' THEN amount ELSE 0 END, CASE WHEN type = 'out' THEN amount ELSE 0 END FROM cash_flows WHERE 1=1$whereBranchCF$statusCond
        ) as prev"))->selectRaw("SUM(debit) - SUM(kredit) as balance")->first()->balance ?? 0;

        // Widget Saldo Per Cabang (for Superadmin)
        $branchBalances = [];
        if ($user->isSuperAdmin()) {
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $bId = $branch->id;
                $wTrx = " AND branch_id = $bId";
                $wPur = " AND warehouse_id IN (SELECT id FROM warehouses WHERE branch_id = $bId)";
                $wExp = " AND branch_id = $bId";
                $wCF = " AND branch_id = $bId";

                $bal = DB::table(DB::raw("(
                    SELECT total_amount as debit, 0 as kredit FROM transactions WHERE status = 'completed' AND payment_method != 'utang'$wTrx
                    UNION ALL
                    SELECT 0, total_amount FROM purchases WHERE 1=1$wPur
                    UNION ALL
                    SELECT 0, amount FROM expenses WHERE 1=1$wExp
                    UNION ALL
                    SELECT CASE WHEN type = 'in' THEN amount ELSE 0 END, CASE WHEN type = 'out' THEN amount ELSE 0 END FROM cash_flows WHERE 1=1$wCF$statusCond
                ) as prev"))->selectRaw("SUM(debit) - SUM(kredit) as balance")->first()->balance ?? 0;
                
                $branchBalances[] = [
                    'name' => $branch->name,
                    'balance' => $bal
                ];
            }
        }

        $storeSettings = \App\Models\Setting::getStoreSettings();

        return view('reports.index', compact('stats', 'recentTransactions', 'topProducts', 'lowStockProducts', 'currentBalance', 'branchBalances', 'storeSettings'));
    }

    public function sales(Request $request)
    {
        $this->checkAdminAccess();
        
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'view');

        $transactionsQuery = Transaction::with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc');

        $expensesQuery = \App\Models\Expense::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        $purchasesQuery = \App\Models\Purchase::with(['supplier', 'user', 'items.product'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        // Calculate Totals (Before Pagination)
        $activeTransactionsQuery = $transactionsQuery->clone()->where('status', 'completed');

        $totalSales = $activeTransactionsQuery->sum('total_amount');
        $totalReceivables = $activeTransactionsQuery->clone()->where('payment_method', 'utang')->sum('total_amount');
        $totalReceived = $totalSales - $totalReceivables;
        
        $totalExpenses = $expensesQuery->sum('amount');
        $totalPurchases = $purchasesQuery->sum('total_amount');
        
        $netIncome = $totalSales - ($totalExpenses + $totalPurchases);

        $activeCount = $activeTransactionsQuery->count();

        $summary = [
            'total_transactions' => $transactionsQuery->count(),
            'total_amount' => $totalSales,
            'total_received' => $totalReceived,
            'total_receivables' => $totalReceivables,
            'total_discount' => $activeTransactionsQuery->sum('discount'),
            'total_tax' => $activeTransactionsQuery->sum('tax'),
            'average_transaction' => $activeCount > 0 ? $totalSales / $activeCount : 0,
            'total_expenses' => $totalExpenses,
            'total_purchases' => $totalPurchases,
            'net_income' => $netIncome,
        ];

        if ($format === 'pdf') {
            $transactions = $transactionsQuery->get();
            $expenses = $expensesQuery->get();
            $purchases = $purchasesQuery->get();

            $pdf = Pdf::loadView('reports.sales-pdf', compact('transactions', 'expenses', 'purchases', 'summary', 'startDate', 'endDate'));
            return $pdf->download('laporan-laba-rugi-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        if ($format === 'excel') {
            $transactions = $transactionsQuery->get();
            $expenses = $expensesQuery->get();
            $purchases = $purchasesQuery->get();

            return Excel::download(new SalesReportExport($transactions, $expenses, $purchases, $summary, $startDate, $endDate), 
                'laporan-laba-rugi-' . $startDate . '-to-' . $endDate . '.xlsx');
        }

        $transactions = $transactionsQuery->paginate(10, ['*'], 'trans_page');
        $expenses = $expensesQuery->paginate(10, ['*'], 'exp_page');
        $purchases = $purchasesQuery->paginate(10, ['*'], 'purch_page');

        return view('reports.sales', compact('transactions', 'expenses', 'purchases', 'summary', 'startDate', 'endDate'));
    }

    public function products(Request $request)
    {
        $this->checkAdminAccess();
        
        $category = $request->get('category');
        $format = $request->get('format', 'view');

        $query = Product::with('category');
        
        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $user = auth()->user();
        $activeWarehouse = $user->getActiveWarehouse();

        $products->each(function ($product) use ($activeWarehouse) {
            $product->total_stock = $activeWarehouse ? $product->getStockInWarehouse($activeWarehouse->id) : $product->getTotalStock();
        });

        $summary = [
            'total_products' => $products->count(),
            'total_stock_value' => $products->sum(function($product) {
                return $product->total_stock * $product->purchase_price;
            }),
            'total_selling_value' => $products->sum(function($product) {
                return $product->total_stock * $product->selling_price;
            }),
            'low_stock_count' => $products->filter(function($product) {
                return $product->total_stock <= $product->minimum_stock;
            })->count(),
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.products-pdf', compact('products', 'summary', 'category'));
            return $pdf->download('laporan-produk-' . date('Y-m-d') . '.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new ProductReportExport($products, $summary), 
                'laporan-produk-' . date('Y-m-d') . '.xlsx');
        }

        return view('reports.products', compact('products', 'categories', 'summary', 'category'));
    }

    public function stock(Request $request)
    {
        $this->checkAdminAccess();
        
        $status = $request->get('status', 'all');
        $format = $request->get('format', 'view');

        $branchId = $this->getActiveBranchId();
        $stockSubquery = '(SELECT COALESCE(SUM(pw.stock), 0) FROM product_warehouse pw';
        if ($branchId) {
            $stockSubquery .= ' JOIN warehouses w ON pw.warehouse_id = w.id WHERE w.branch_id = ' . (int)$branchId . ' AND pw.product_id = products.id)';
        } else {
            $stockSubquery .= ' WHERE pw.product_id = products.id)';
        }

        $query = Product::query();
        switch ($status) {
            case 'low':
                $query->whereRaw("$stockSubquery <= products.minimum_stock AND $stockSubquery > 0");
                break;
            case 'out':
                $query->whereRaw("$stockSubquery = 0");
                break;
        }

        $products = $query->selectRaw("products.*, $stockSubquery as total_stock")->orderBy('total_stock', 'asc')->get();

        $summary = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereRaw("$stockSubquery <= products.minimum_stock AND $stockSubquery > 0")->count(),
            'out_of_stock_products' => Product::whereRaw("$stockSubquery = 0")->count(),
            'normal_stock_products' => Product::whereRaw("$stockSubquery > products.minimum_stock")->count(),
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.stock-pdf', compact('products', 'summary', 'status'));
            return $pdf->download('laporan-stok-' . date('Y-m-d') . '.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new StockReportExport($products, $summary), 
                'laporan-stok-' . date('Y-m-d') . '.xlsx');
        }

        return view('reports.stock', compact('products', 'summary', 'status'));
    }

    public function receivables(Request $request)
    {
        $this->checkAdminAccess();
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::with(['user', 'items.product'])
            ->where('payment_method', 'utang')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $summary = [
            'total_receivables' => $query->sum('total_amount'),
            'total_transactions' => $query->count(),
        ];

        $transactions = $query->paginate(10);

        return view('reports.receivables', compact('transactions', 'summary', 'startDate', 'endDate'));
    }

    public function markAsPaid(Transaction $transaction)
    {
        $this->checkAdminAccess();

        if ($transaction->payment_method !== 'utang') {
            return back()->with('error', 'Transaksi ini bukan piutang.');
        }

        $transaction->update([
            'payment_method' => 'cash',
        ]);

        return back()->with('success', 'Piutang berhasil ditandai sebagai lunas.');
    }

    private function getCashFlowData($startDate, $endDate)
    {
        $branchId = $this->getActiveBranchId();
        $whereBranchTrx = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchPur = $branchId ? " AND warehouse_id IN (SELECT id FROM warehouses WHERE branch_id = $branchId)" : "";
        $whereBranchExp = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchCF = $branchId ? " AND branch_id = $branchId" : "";
        
        // Status condition for cash_flows: only approved or non-adjustment
        $statusCond = " AND (status = 'approved' OR is_adjustment = 0 OR status IS NULL)";

        // 1. Calculate Opening Balance (before start_date)
        $openingBalance = DB::table(DB::raw("(
            SELECT total_amount as debit, 0 as kredit FROM transactions WHERE status = 'completed' AND payment_method != 'utang' AND DATE(created_at) < '$startDate'$whereBranchTrx
            UNION ALL
            SELECT 0, total_amount FROM purchases WHERE date < '$startDate'$whereBranchPur
            UNION ALL
            SELECT 0, amount FROM expenses WHERE date < '$startDate'$whereBranchExp
            UNION ALL
            SELECT CASE WHEN type = 'in' THEN amount ELSE 0 END, CASE WHEN type = 'out' THEN amount ELSE 0 END FROM cash_flows WHERE date < '$startDate'$whereBranchCF$statusCond
        ) as prev"))->selectRaw("SUM(debit) - SUM(kredit) as balance")->first()->balance ?? 0;

        // 2. Fetch Transactions within Date Range
        $query = "
            (SELECT DATE(created_at) as date, '[TRX] Penjualan' as category, transaction_code as note, total_amount as debit, 0 as kredit, created_at as exact_time, branch_id FROM transactions WHERE status = 'completed' AND payment_method != 'utang' AND DATE(created_at) BETWEEN ? AND ?$whereBranchTrx)
            UNION ALL
            (SELECT date, '[OUT] Pembelian Stok' as category, transaction_code as note, 0 as debit, total_amount as kredit, created_at as exact_time, (SELECT branch_id FROM warehouses WHERE id = purchases.warehouse_id) as branch_id FROM purchases WHERE date BETWEEN ? AND ?$whereBranchPur)
            UNION ALL
            (SELECT date, '[OUT] Pengeluaran Operasional' as category, name as note, 0 as debit, amount as kredit, created_at as exact_time, branch_id FROM expenses WHERE date BETWEEN ? AND ?$whereBranchExp)
            UNION ALL
            (SELECT date, CONCAT('[KAS] ', category) as category, note, CASE WHEN type = 'in' THEN amount ELSE 0 END as debit, CASE WHEN type = 'out' THEN amount ELSE 0 END as kredit, created_at as exact_time, branch_id FROM cash_flows WHERE date BETWEEN ? AND ?$whereBranchCF$statusCond)
            ORDER BY date ASC, exact_time ASC
        ";

        $results = DB::select($query, [
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate
        ]);

        // 3. Process results for Running Balance
        $currentBalance = $openingBalance;
        foreach ($results as $row) {
            $currentBalance += ($row->debit - $row->kredit);
            $row->balance = $currentBalance;
            // Get branch name
            if ($row->branch_id) {
                $row->branch_name = Branch::find($row->branch_id)->name ?? '-';
            } else {
                $row->branch_name = 'Pusat';
            }
        }

        return [
            'results' => $results,
            'openingBalance' => $openingBalance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    public function cashFlowReport(Request $request)
    {
        $this->checkAdminAccess();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $data = $this->getCashFlowData($startDate, $endDate);
        return view('reports.cash-flow', $data);
    }

    public function exportCashFlowPdf(Request $request)
    {
        $this->checkAdminAccess();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $data = $this->getCashFlowData($startDate, $endDate);
        $pdf = Pdf::loadView('reports.cash-flow-pdf', $data);
        return $pdf->download('buku-kas-umum-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    public function exportCashFlowExcel(Request $request)
    {
        $this->checkAdminAccess();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $data = $this->getCashFlowData($startDate, $endDate);
        return Excel::download(new CashFlowReportExport($data['results'], $data['openingBalance'], $startDate, $endDate), 
            'buku-kas-umum-' . $startDate . '-to-' . $endDate . '.xlsx');
    }

    public function shifts(Request $request)
    {
        $this->checkAdminAccess();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $format = $request->get('format', 'view');

        $query = \App\Models\CashierShift::with(['user', 'user.branch'])
            ->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('start_time', 'desc');

        if ($format === 'pdf') {
            $shifts = $query->get();
            $pdf = Pdf::loadView('reports.shifts-pdf', compact('shifts', 'startDate', 'endDate'));
            return $pdf->download('laporan-shift-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        if ($format === 'excel') {
            $shifts = $query->get();
            return Excel::download(new ShiftReportExport($shifts, $startDate, $endDate), 
                'laporan-shift-' . $startDate . '-to-' . $endDate . '.xlsx');
        }

        $shifts = $query->paginate(15);
        $storeSettings = \App\Models\Setting::getStoreSettings();
        return view('reports.shifts', compact('shifts', 'startDate', 'endDate', 'storeSettings'));
    }

    // [NEW] Approval Methods for Discrepancies
    public function pendingAdjustments()
    {
        $this->checkAdminAccess();
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang dapat menyetujui selisih kas.');
        }

        $pendingAdjustments = CashFlow::with(['user', 'branch', 'shift'])
            ->where('status', 'pending')
            ->where('is_adjustment', true)
            ->latest()
            ->get();

        return view('reports.approvals', compact('pendingAdjustments'));
    }

    public function approveAdjustment(CashFlow $cashFlow)
    {
        $this->checkAdminAccess();
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $cashFlow->update(['status' => 'approved']);
        
        \App\Helpers\ActivityLogger::log(
            'Approve Adjustment', 
            "Menyetujui selisih kas (ID: {$cashFlow->id}) sebesar Rp " . number_format($cashFlow->amount, 0, ',', '.') . " untuk cabang ID: {$cashFlow->branch_id}.", 
            $cashFlow
        );

        return back()->with('success', 'Selisih kas berhasil disetujui dan telah mempengaruhi saldo kas.');
    }

    public function rejectAdjustment(Request $request, CashFlow $cashFlow)
    {
        $this->checkAdminAccess();
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate(['reason' => 'required|string|max:255']);

        $cashFlow->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);
        
        \App\Helpers\ActivityLogger::log(
            'Reject Adjustment', 
            "Menolak selisih kas (ID: {$cashFlow->id}). Alasan: {$request->reason}", 
            $cashFlow
        );

        return back()->with('success', 'Selisih kas ditolak.');
    }
}
