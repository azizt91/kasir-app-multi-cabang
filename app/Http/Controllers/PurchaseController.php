<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = \App\Models\Purchase::with(['supplier', 'items.product', 'user', 'warehouse'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        $warehouses = \App\Models\Warehouse::active()->with('branch')->get();
        $transactionCode = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
        return view('purchases.create', compact('suppliers', 'products', 'warehouses', 'transactionCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'transaction_code' => 'required|unique:purchases,transaction_code',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'transaction_code' => $request->transaction_code,
                'date' => $request->date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $itemData) {
                $totalCost = $itemData['quantity'] * $itemData['unit_cost'];
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $totalCost,
                ]);

                $product = \App\Models\Product::find($itemData['product_id']);
                $product->purchase_price = $itemData['unit_cost'];
                $product->save();

                // Add stock to product_warehouse
                DB::table('product_warehouse')->updateOrInsert(
                    ['product_id' => $product->id, 'warehouse_id' => $request->warehouse_id],
                    ['stock' => DB::raw('stock + ' . $itemData['quantity']), 'updated_at' => now()]
                );

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $request->warehouse_id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'notes' => 'Pembelian Stok: ' . $purchase->transaction_code,
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan dan stok telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(\App\Models\Purchase $purchase)
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        $warehouses = \App\Models\Warehouse::active()->with('branch')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'warehouses'));
    }

    public function update(Request $request, \App\Models\Purchase $purchase)
    {
        $request->validate([
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $oldWarehouseId = $purchase->warehouse_id;

            // Rollback old items from old warehouse
            foreach ($purchase->items as $oldItem) {
                $product = \App\Models\Product::find($oldItem->product_id);
                if ($product) {
                    DB::table('product_warehouse')
                        ->where('product_id', $product->id)
                        ->where('warehouse_id', $oldWarehouseId)
                        ->decrement('stock', $oldItem->quantity);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $oldWarehouseId,
                        'type' => 'out',
                        'quantity' => $oldItem->quantity,
                        'reference_type' => 'purchase_edit_rollback',
                        'reference_id' => $purchase->id,
                        'notes' => 'Koreksi Pembelian (Rollback): ' . $purchase->transaction_code,
                    ]);
                }
            }
            $purchase->items()->delete();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'date' => $request->date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            foreach ($request->items as $itemData) {
                $totalCost = $itemData['quantity'] * $itemData['unit_cost'];
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $totalCost,
                ]);

                $product = \App\Models\Product::find($itemData['product_id']);
                $product->purchase_price = $itemData['unit_cost'];
                $product->save();

                DB::table('product_warehouse')->updateOrInsert(
                    ['product_id' => $product->id, 'warehouse_id' => $request->warehouse_id],
                    ['stock' => DB::raw('stock + ' . $itemData['quantity']), 'updated_at' => now()]
                );

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $request->warehouse_id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'reference_type' => 'purchase_edit_new',
                    'reference_id' => $purchase->id,
                    'notes' => 'Koreksi Pembelian (Update): ' . $purchase->transaction_code,
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(\App\Models\Purchase $purchase)
    {
        try {
            DB::beginTransaction();
            $warehouseId = $purchase->warehouse_id;
            foreach ($purchase->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    DB::table('product_warehouse')
                        ->where('product_id', $product->id)
                        ->where('warehouse_id', $warehouseId)
                        ->decrement('stock', $item->quantity);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reference_type' => 'purchase_void',
                        'reference_id' => $purchase->id,
                        'notes' => 'Hapus Pembelian: ' . $purchase->transaction_code,
                    ]);
                }
            }
            $purchase->items()->delete();
            $purchase->delete();
            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }
}
