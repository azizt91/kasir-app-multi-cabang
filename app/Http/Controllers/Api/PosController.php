<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,utang,card,ewallet,transfer,qris',
            'amount_paid' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string',
            'note' => 'nullable|string|max:1000',
            'created_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $warehouse = $user->getActiveWarehouse();
            if (!$warehouse) {
                throw new \Exception('Tidak ada gudang aktif untuk cabang Anda.');
            }
            $warehouseId = $warehouse->id;
            $branchId = $user->branch_id;

            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                // Strict warehouse stock check
                $whStock = $product->getStockInWarehouse($warehouseId);
                if ($whStock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} di gudang \"{$warehouse->name}\" tidak mencukupi. Tersedia: {$whStock}, Diminta: {$item['quantity']}");
                }

                $itemSubtotal = (float) $product->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->selling_price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = 0;
            $tax = 0;
            $totalAmount = round(max(0, $subtotal - $discount + $tax));
            $amountPaid = $request->amount_paid;
            $changeAmount = 0;

            if ($request->payment_method !== 'utang') {
                if ($amountPaid < $totalAmount) {
                    throw new \Exception("Nominal pembayaran kurang! Total: {$totalAmount}, Bayar: {$amountPaid}");
                }
                $changeAmount = $amountPaid - $totalAmount;
            }

            $transactionDate = $request->created_at ? \Carbon\Carbon::parse($request->created_at) : now();
            
            $transaction = Transaction::create([
                'transaction_code' => Transaction::generateTransactionCode(),
                'user_id' => auth()->id(),
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'status' => 'completed',
                'customer_name' => $request->customer_name ?? 'Umum',
                'note' => $request->note . ($request->created_at ? " (Offline Sync)" : ""),
                'created_at' => $transactionDate,
                'updated_at' => now(),
            ]);

            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => $transactionDate,
                    'updated_at' => now(),
                ]);

                DB::table('product_warehouse')
                    ->where('product_id', $item['product']->id)
                    ->where('warehouse_id', $warehouseId)
                    ->decrement('stock', $item['quantity']);

                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'warehouse_id' => $warehouseId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'App\Models\Transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "Penjualan Mobile - {$transaction->transaction_code}",
                    'created_at' => $transactionDate,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            try {
                $usersToNotify = \App\Models\User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get();
                if ($usersToNotify->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\OrderCreated($transaction));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("POS Notification Error: " . $e->getMessage());
            }

            $transaction->load(['items.product', 'user']);
            $transaction->items->each(function ($item) {
                $item->quantity = (float) $item->quantity;
                $item->price = (float) $item->price;
                $item->subtotal = (float) $item->subtotal;
                if ($item->product) {
                    $item->product->stock = (float) $item->product->getTotalStock();
                    $item->product->selling_price = (float) $item->product->selling_price;
                }
            });

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil disinkronisasi.', 'transaction' => $transaction]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
