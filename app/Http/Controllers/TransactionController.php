<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'branch', 'warehouse']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        if ($request->status) {
            if ($request->status == 'utang') {
                $query->where('payment_method', 'utang');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Branch filtering is automatically handled by App\Models\Scopes\BranchScope
        // based on the Global Filter session (admin_active_branch_id).

        $transactions = $query->latest()->paginate(10);
        
        return view('transactions.index', [
            'transactions' => $transactions,
            'filters' => $request->only(['start_date', 'end_date', 'status'])
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.product', 'user', 'branch', 'warehouse']);
        return view('transactions.show', compact('transaction'));
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['items.product', 'user', 'branch', 'warehouse']);
        $storeSettings = \App\Models\Setting::getStoreSettings();
        return view('transactions.print', compact('transaction', 'storeSettings'));
    }

    /**
     * Void (Cancel) the transaction.
     * Restores stock to the CORRECT warehouse from the transaction.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status === 'void') {
            return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                $warehouseId = $transaction->warehouse_id;

                foreach ($transaction->items as $item) {
                    $product = $item->product;
                    
                    // Restore stock to product_warehouse
                    if ($warehouseId) {
                        DB::table('product_warehouse')
                            ->where('product_id', $product->id)
                            ->where('warehouse_id', $warehouseId)
                            ->increment('stock', $item->quantity);
                    }

                    // Record Stock Movement (IN) with warehouse_id
                    StockMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reference_type' => 'App\Models\Transaction',
                        'reference_id' => $transaction->id,
                        'notes' => "Void Transaksi #{$transaction->transaction_code}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $transaction->update(['status' => 'void']);
            });

            return back()->with('success', 'Transaksi berhasil dibatalkan. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
