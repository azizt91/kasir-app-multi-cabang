<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // Filter by Date Range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter by Status
        if ($request->status) {
            if ($request->status == 'utang') {
                $query->where('payment_method', 'utang');
            } else {
                $query->where('status', $request->status);
            }
        }

        $transactions = $query->latest()->paginate(10);
        
        return view('transactions.index', [
            'transactions' => $transactions,
            'filters' => $request->only(['start_date', 'end_date', 'status'])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['items.product', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Print the transaction receipt.
     */
    public function print(Transaction $transaction)
    {
        $transaction->load(['items.product', 'user']);
        $storeSettings = \App\Models\Setting::getStoreSettings();
        return view('transactions.print', compact('transaction', 'storeSettings'));
    }

    /**
     * Void (Cancel) the transaction.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status === 'void') {
            return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                // 1. Loop items to restore stock
                foreach ($transaction->items as $item) {
                    $product = $item->product;
                    
                    // Increment product stock
                    $product->increment('stock', $item->quantity);

                    // Record Stock Movement (IN)
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reference_type' => 'App\Models\Transaction',
                        'reference_id' => $transaction->id,
                        'notes' => "Void Transaksi #{$transaction->transaction_code}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 2. Update Transaction Status
                $transaction->update(['status' => 'void']);
            });

            return back()->with('success', 'Transaksi berhasil dibatalkan. Stok telah dikembalikan sesuai permintaan pengguna.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
