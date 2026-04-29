<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user) return;

        $supplier = \App\Models\Supplier::first(); // Assumes at least one supplier exists, otherwise fillable is nullable
        $product = \App\Models\Product::first();

        if (!$product) return;

        // Transaction Code
        $code = 'PO-' . date('Ymd') . '-SEED';

        // Item Details
        $qty = 10;
        $cost = $product->purchase_price > 0 ? $product->purchase_price : 10000;
        $total = $qty * $cost;

        // Create Purchase
        $purchase = \App\Models\Purchase::create([
            'supplier_id' => $supplier ? $supplier->id : null,
            'transaction_code' => $code,
            'date' => now()->subDays(1),
            'total_amount' => $total,
            'note' => 'Dummy Purchase Seeder',
            'user_id' => $user->id,
        ]);

        // Create Item
        \App\Models\PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => $qty,
            'unit_cost' => $cost,
            'total_cost' => $total,
        ]);

        // Update Product
        $product->stock += $qty;
        $product->purchase_price = $cost; // Updates to latest cost
        $product->save();

        // Log Movement
        \App\Models\StockMovement::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $qty,
            'reference_type' => 'purchase',
            'reference_id' => $purchase->id,
            'notes' => 'Pembelian Stok: ' . $code,
        ]);
    }
}
