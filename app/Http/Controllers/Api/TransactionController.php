<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get transaction history.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['items.product', 'user']);

        // Filter by Date
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Pagination
        // $transactions = $query->latest()->simplePaginate(10);
        $transactions = $query->latest()->get(); // Get ALL transactions as requested

        return response()->json($transactions);
    }

    /**
     * Get transaction detail.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'user'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }
    /**
     * Void transaction.
     */
    public function destroy($id)
    {
        $transaction = Transaction::with('items')->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($transaction) {
            // Restore Stock
            foreach ($transaction->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                    
                    // Record Movement
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in', // Void is 'in' (restock)
                        'quantity' => $item->quantity,
                        'reference_type' => 'Void Transaction',
                        'reference_id' => $transaction->id,
                        'notes' => 'Void Transaction #' . $transaction->transaction_code,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Create Void Record or Delete?
            // Usually we mark as voided instead of deleting to keep trace.
            // But user said "DELETE endpoint".
            // Let's delete for now or SoftDelete if model supports it.
            // Assuming Hard Delete based on "destroy".
            
            // Do NOT delete. Update status to 'void'.
            $transaction->update([
                'status' => 'void', 
                'note' => $transaction->note . ' [VOIDED]',
            ]);
        });

        return response()->json(['message' => 'Transaction voided successfully']);
    }
    /**
     * Get receivables (Unpaid transactions).
     */
    public function receivables(Request $request)
    {
        try {
            $query = Transaction::with(['items.product', 'user'])
                        ->where('payment_method', 'utang');

            if ($request->has('search')) {
                $query->where('customer_name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $transactions = $query->latest()->simplePaginate(50);

            return response()->json($transactions);
        } catch (\Throwable $e) {
            \Log::error('Receivables Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark transaction as paid.
     */
    public function markAsPaid(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->payment_method !== 'utang') {
            return response()->json(['message' => 'Transaction is not a receivable'], 400);
        }

        $transaction->update([
            'payment_method' => $request->payment_method ?? 'cash', // Default to cash or from request
            'amount_paid' => $transaction->total_amount, // Assume full payment
            'change_amount' => 0,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Transaction marked as paid', 'data' => $transaction]);
    }
}
