<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // POS Shift Management
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/shift/create', [ShiftController::class, 'create'])->name('shift.create');
        Route::post('/shift/store', [ShiftController::class, 'store'])->name('shift.store');
        Route::get('/shift/close', [ShiftController::class, 'edit'])->name('shift.close');
        Route::post('/shift/close', [ShiftController::class, 'update'])->name('shift.update');
    });

    // POS Interface (protected by shift check if enabled)
    Route::middleware([\App\Http\Middleware\CheckShift::class])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products/search', [PosController::class, 'searchProducts'])->name('pos.products.search');
        Route::get('/pos/categories', [PosController::class, 'getCategories'])->name('pos.categories');
        Route::post('/pos/search', [PosController::class, 'searchProducts'])->name('pos.search');
        Route::post('/pos/transaction', [PosController::class, 'store'])->name('pos.transaction');
    });

    // Protected Routes (Admin or Permission based)
    Route::resource('products', ProductController::class)->middleware('permission:view_products');
    Route::get('/products/barcodes/print', [App\Http\Controllers\ProductController::class, 'printBarcodes'])
        ->name('products.print_barcodes')
        ->middleware('permission:view_products');
        
    Route::resource('categories', CategoryController::class)->middleware('permission:view_categories');
    
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class)->middleware('permission:view_purchases');
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->middleware('permission:view_expenses');
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->middleware('permission:view_suppliers');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->middleware('permission:view_customers');
    
    // Cash Flow Management: View for authorized, but mutation only for Superadmin
    Route::resource('cash-flows', CashFlowController::class)->except(['edit', 'update', 'destroy'])->middleware('permission:view_cash_flows');
    
    Route::get('/transactions/{transaction}/print', [\App\Http\Controllers\TransactionController::class, 'print'])->name('transactions.print')->middleware('permission:view_transactions');
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class)->only(['index', 'show', 'destroy'])->middleware('permission:view_transactions');
    
    // Stock Transfers
    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show'])->middleware('permission:view_products');
    Route::get('/stock-transfers/get-stock', [StockTransferController::class, 'getStock'])->name('stock-transfers.get-stock')->middleware('permission:view_products');
    
    // Warehouse Management (Now accessible by Branch Admin via BranchScope)
    Route::resource('warehouses', WarehouseController::class)->middleware('permission:view_products');

    // Reports
    Route::middleware('permission:view_reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [ReportController::class, 'products'])->name('products');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/sales/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('sales.pdf');
        Route::get('/sales/export-excel', [ReportController::class, 'exportSalesExcel'])->name('sales.excel');
        Route::get('/products/export-pdf', [ReportController::class, 'exportProductsPdf'])->name('products.pdf');
        Route::get('/products/export-excel', [ReportController::class, 'exportProductsExcel'])->name('products.excel');
        Route::get('/stock/export-pdf', [ReportController::class, 'exportStockPdf'])->name('stock.pdf');
        Route::get('/stock/export-excel', [ReportController::class, 'exportStockExcel'])->name('stock.excel');
        Route::get('/receivables', [ReportController::class, 'receivables'])->name('receivables');
        Route::post('/receivables/{transaction}/paid', [ReportController::class, 'markAsPaid'])->name('receivables.paid');
        Route::get('/cash-flow', [ReportController::class, 'cashFlowReport'])->name('cash_flow');
        Route::get('/cash-flow/export-pdf', [ReportController::class, 'exportCashFlowPdf'])->name('cash_flow.pdf');
        Route::get('/cash-flow/export-excel', [ReportController::class, 'exportCashFlowExcel'])->name('cash_flow.excel');
        Route::get('/shifts', [ReportController::class, 'shifts'])->name('shifts');
        Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity_logs');
    });

    // Admin routes — accessible by all role=admin users (including Branch Admin)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Superadmin-only routes — accessible ONLY by admin users with branch_id=null
    Route::middleware('superadmin')->group(function () {
        // Global system configuration
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Branch Management
        Route::resource('branches', BranchController::class);
        
        // Hybrid Strategy Filter
        Route::post('/set-global-branch', [\App\Http\Controllers\BranchFilterController::class, 'setFilter'])->name('set-global-branch');

        // [NEW] Cash Flow Approval & Mutation Security
        Route::get('/reports/approvals', [ReportController::class, 'pendingAdjustments'])->name('reports.approvals');
        Route::post('/reports/approvals/{cashFlow}/approve', [ReportController::class, 'approveAdjustment'])->name('reports.approve_adjustment');
        Route::post('/reports/approvals/{cashFlow}/reject', [ReportController::class, 'rejectAdjustment'])->name('reports.reject_adjustment');
        
        // Stock Transfer Approvals
        Route::post('/stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
        Route::post('/stock-transfers/{stockTransfer}/reject', [StockTransferController::class, 'reject'])->name('stock-transfers.reject');

        // Secure Delete for Cash Flows (moved from auth to superadmin)
        Route::delete('/cash-flows/{cash_flow}', [CashFlowController::class, 'destroy'])->name('cash-flows.destroy');
        Route::get('/cash-flows/{cash_flow}/edit', [CashFlowController::class, 'edit'])->name('cash-flows.edit');
        Route::put('/cash-flows/{cash_flow}', [CashFlowController::class, 'update'])->name('cash-flows.update');
    });
});

// Safely serve storage files on Windows dev environments
Route::get('/asset-storage/{path}', function($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (file_exists($fullPath)) {
        $mime = mime_content_type($fullPath);
        return response()->file($fullPath, ['Content-Type' => $mime, 'Access-Control-Allow-Origin' => '*']);
    }
    return response("Path not found: " . $fullPath);
})->where('path', '.*')->name('asset.storage');

require __DIR__.'/auth.php';
