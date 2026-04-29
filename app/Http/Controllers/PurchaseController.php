<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = \App\Models\Purchase::with(['supplier', 'items.product', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        $transactionCode = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
        return view('purchases.create', compact('suppliers', 'products', 'transactionCode'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'transaction_code' => 'required|unique:purchases,transaction_code',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            // Create Purchase Header
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $request->supplier_id, // Nullable
                'transaction_code' => $request->transaction_code,
                'date' => $request->date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
                'user_id' => auth()->id(),
            ]);

            // Process Items
            foreach ($request->items as $itemData) {
                $totalCost = $itemData['quantity'] * $itemData['unit_cost'];
                
                // Create Purchase Item
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $totalCost,
                ]);

                // Update Product Stock & Price
                $product = \App\Models\Product::find($itemData['product_id']);
                
                // Update Purchase Price (Using latest price)
                $product->purchase_price = $itemData['unit_cost'];
                
                // Increase Stock
                $product->stock += $itemData['quantity'];
                $product->save();

                // Log Movement
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'notes' => 'Pembelian Stok: ' . $purchase->transaction_code,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan dan stok telah diperbarui.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }


    public function edit(\App\Models\Purchase $purchase)
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Purchase $purchase)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. ROLLBACK OLD ITEMS (Stock & Logs)
            foreach ($purchase->items as $oldItem) {
                $product = \App\Models\Product::find($oldItem->product_id);
                if ($product) {
                    $product->stock -= $oldItem->quantity;
                    $product->save();

                    // Optional: Log reversal, or just rely on the fact that we edit.
                    // To keep history clean, we can log a "Correction Out"
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $oldItem->quantity,
                        'reference_type' => 'purchase_edit_rollback',
                        'reference_id' => $purchase->id,
                        'notes' => 'Koreksi Pembelian (Rollback): ' . $purchase->transaction_code,
                    ]);
                }
            }
            // Delete old items
            $purchase->items()->delete();

            // 2. UPDATE HEADER
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // 3. RE-APPLY NEW ITEMS
            foreach ($request->items as $itemData) {
                $totalCost = $itemData['quantity'] * $itemData['unit_cost'];
                
                \App\Models\PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $totalCost,
                ]);

                // Update Product Stock & Price
                $product = \App\Models\Product::find($itemData['product_id']);
                $product->purchase_price = $itemData['unit_cost'];
                $product->stock += $itemData['quantity'];
                $product->save();

                // Log Movement (New)
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'reference_type' => 'purchase_edit_new',
                    'reference_id' => $purchase->id,
                    'notes' => 'Koreksi Pembelian (Update): ' . $purchase->transaction_code,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diperbarui. Stok lama ditarik dan stok baru ditambahkan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(\App\Models\Purchase $purchase)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($purchase->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    // Reverse Stock
                    $product->stock -= $item->quantity;
                    $product->save();

                    // Log Movement (Adjustment/Void)
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reference_type' => 'purchase_void',
                        'reference_id' => $purchase->id,
                        'notes' => 'Hapus Pembelian: ' . $purchase->transaction_code,
                    ]);
                }
            }

            // Delete Items first (handled by cascade usually, but good to be explicit or if no cascade)
            $purchase->items()->delete();
            $purchase->delete();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dihapus dan stok telah ditarik kembali.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }
}
