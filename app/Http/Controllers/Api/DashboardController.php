<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function index(Request $request)
    {
        // Get basic statistics (only completed transactions)
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::lowStock()->count(),
            'today_transaction_count' => Transaction::where('status', 'completed')->whereDate('created_at', today())->count(),
            'today_sales_total' => (float) Transaction::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
            'yesterday_sales_total' => (float) Transaction::where('status', 'completed')->whereDate('created_at', today()->subDay())->sum('total_amount'),
        ];

        // Sales chart data (last 7 days) — only completed
        $sales_chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Transaction::where('status', 'completed')->whereDate('created_at', $date)->sum('total_amount');
            $sales_chart[] = [
                'date' => $date->format('Y-m-d'),
                'total' => (float) $total,
                'day_name' => $date->locale('id')->dayName,
            ];
        }

        // Top selling products (this week) — only from completed transactions
        $top_products = Product::join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereBetween('transactions.created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Low stock products
        $low_stock_items = Product::lowStock()
            ->select('id', 'name', 'stock', 'minimum_stock', 'image', 'barcode')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'sales_chart' => $sales_chart,
            'top_products' => $top_products,
            'low_stock_items' => $low_stock_items,
        ]);
    }
}
