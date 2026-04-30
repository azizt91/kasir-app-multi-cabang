<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use App\Models\Branch;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    public function index(Request $request)
    {
        $user = $request->user();
        $branchId = $this->getActiveBranchId();
        
        // Get basic statistics (only completed transactions)
        // BranchScope auto-filters for kasir
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::lowStock($branchId)->count(),
            'total_transactions' => Transaction::where('status', 'completed')->whereDate('created_at', today())->count(),
            'daily_sales' => Transaction::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
        ];

        // Get recent transactions (last 10)
        $recent_transactions = Transaction::with(['user', 'items.product', 'branch'])
            ->latest()
            ->take(10)
            ->get();

        // Get low stock products
        $low_stock_products = Product::with('category')
            ->lowStock($branchId)
            ->take(10)
            ->get();

        // Append total stock for display
        $low_stock_products->each(function ($product) {
            $product->total_stock = $product->getTotalStock();
        });

        // Get top selling products (this week) — only from completed transactions
        $top_products = Product::withSum(['transactionItems as total_sold' => function ($query) {
            $query->whereHas('transaction', function ($q) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            });
        }], 'quantity')
        ->whereHas('transactionItems.transaction', function ($q) {
            $q->where('status', 'completed')
              ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        })
        ->orderByDesc('total_sold')
        ->take(5)
        ->get();

        // Sales chart data (last 7 days) — only completed
        $sales_chart = [];
        $daily_sales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Transaction::where('status', 'completed')->whereDate('created_at', $date)->sum('total_amount');
            $sales_chart[] = [
                'date' => $date->format('Y-m-d'),
                'total' => $total,
            ];
            $daily_sales[] = $total;
        }

        // Category distribution for pie chart
        $category_distribution = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderByDesc('products_count')
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'product_count' => $category->products_count
                ];
            });

        // ==========================================================
        // LOGIKA BARU: Perhitungan Saldo Kas Real-Time (Filtered)
        // ==========================================================
        $whereBranchTrx = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchPur = $branchId ? " AND warehouse_id IN (SELECT id FROM warehouses WHERE branch_id = $branchId)" : "";
        $whereBranchExp = $branchId ? " AND branch_id = $branchId" : "";
        $whereBranchCF = $branchId ? " AND branch_id = $branchId" : "";
        
        // Status condition: only approved or non-adjustment
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

        // Branch info for display
        $branch = $user->branch;

        return view('dashboard', [
            'stats' => $stats,
            'recent_transactions' => $recent_transactions,
            'low_stock_products' => $low_stock_products,
            'top_products' => $top_products,
            'sales_chart' => $sales_chart,
            'daily_sales' => $daily_sales,
            'category_distribution' => $category_distribution,
            'user_role' => $user->role,
            'branch' => $branch,
            'currentBalance' => $currentBalance,
            'branchBalances' => $branchBalances,
        ]);
    }
}