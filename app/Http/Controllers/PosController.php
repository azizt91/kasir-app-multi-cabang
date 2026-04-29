<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index()
    {
        $categories = Category::all();
        $customers = \App\Models\Customer::orderBy('name')->get();
        $storeSettings = \App\Models\Setting::getStoreSettings(); // Tambahkan ini
        return view('pos.index', compact('categories', 'customers', 'storeSettings'));
    }

    /**
     * Search products for POS.
     */
    // public function searchProducts(Request $request)
    // {
    //     $query = $request->get('q');
    //     $category = $request->get('category');
    //     $page = $request->get('page', 1);
    //     $perPage = $request->get('per_page', 10);

    //     $productsQuery = Product::with('category')
    //         ->where('stock', '>', 0);

    //     // Filter by category if specified
    //     if ($category && $category !== 'all') {
    //         $productsQuery->whereHas('category', function($q) use ($category) {
    //             $q->where('id', $category);
    //         });
    //     }

    //     // Filter by search query if specified
    //     if (!empty($query) && strlen($query) >= 2) {
    //         $productsQuery->where(function ($q) use ($query) {
    //             $q->where('name', 'like', "%{$query}%")
    //               ->orWhere('barcode', 'like', "%{$query}%");
    //         });
    //     }

    //     // Paginate results
    //     $products = $productsQuery
    //         ->skip(($page - 1) * $perPage)
    //         ->take($perPage)
    //         ->get(['id', 'name', 'barcode', 'selling_price', 'stock', 'category_id', 'image']);

    //     $total = $productsQuery->count();

    //     return response()->json([
    //         'products' => $products,
    //         'total' => $total,
    //         'current_page' => $page,
    //         'per_page' => $perPage,
    //         'last_page' => ceil($total / $perPage)
    //     ]);
    // }

    public function searchProducts(Request $request)
    {
        // Mengambil parameter dari request
        $query = $request->get('q');
        $category = $request->get('category');
        $perPage = $request->get('per_page', 10);

        // Membangun query dasar ke ProductGroup
        $groupsQuery = \App\Models\ProductGroup::with('products');

        // Filter berdasarkan kategori
        if ($category && $category !== 'all') {
            $groupsQuery->where('category_id', $category);
        }

        // Filter berdasarkan query pencarian
        if (!empty($query)) {
            $groupsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('products', function($pq) use ($query) {
                      $pq->where('barcode', 'like', "%{$query}%")
                         ->orWhere('name', 'like', "%{$query}%");
                  });
            });
        }

        // Paginate results
        $groups = $groupsQuery->latest()->paginate($perPage);

        // Transform collection untuk format yang sesuai dengan Frontend POS
        $groups->getCollection()->transform(function ($group) {
            if ($group->has_variants) {
                // Logic untuk Produk Varian
                $minPrice = $group->products->min('selling_price');
                $maxPrice = $group->products->max('selling_price');
                $totalStock = $group->products->sum('stock');
                $firstProduct = $group->products->first(); // Ambil satu untuk gambar

                return [
                    'id' => $group->id, // Group ID
                    'name' => $group->name,
                    'is_group' => true,
                    'image' => $firstProduct ? $firstProduct->image : null,
                    'price_display' => ($minPrice == $maxPrice) ? $minPrice : "$minPrice - $maxPrice", // Raw numbers for formatter later or pre-format? Let's use raw for consistency if possible, or pre-formatted string. 
                    // Frontend 'formatRupiah' expects number. Let's send min price as 'selling_price' for sorting/display base.
                    'selling_price' => $minPrice, 
                    'stock' => $totalStock,
                    'variants' => $group->products->map(function($v) {
                        return [
                            'id' => $v->id,
                            'name' => $v->variant_name, // Just variant name "XL"
                            'full_name' => $v->name, // "Kaos (XL)"
                            'price' => $v->selling_price,
                            'stock' => $v->stock,
                            'image' => $v->image
                        ];
                    })
                ];
            } else {
                // Logic untuk Produk Satuan (Single)
                $product = $group->products->first();
                if (!$product) return null; // Should not happen

                return [
                    'id' => $product->id, // Product ID (Direct Add)
                    'name' => $product->name,
                    'is_group' => false,
                    'image' => $product->image,
                    'selling_price' => $product->selling_price,
                    'stock' => $product->stock,
                    'variants' => []
                ];
            }
        });

        // Filter out nulls (if any empty groups)
        // $groups->setCollection($groups->getCollection()->filter());

        return response()->json($groups);
    }

    public function getCategories()
    {
        $categories = \App\Models\Category::select('id', 'name')->get();
        return response()->json($categories);
    }

    /**
     * Show product search results for POS.
     */
    public function show(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::with('category')
            ->where(function ($q) use ($query) {
                $q->where('barcode', $query)
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->where('stock', '>', 0)
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'barcode' => $product->barcode,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'selling_price' => (float) $product->selling_price,
                    'stock' => $product->stock,
                    'is_low_stock' => $product->is_low_stock,
                ];
            });

        return response()->json($products);
    }

    /**
     * Process transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,utang,card,ewallet,transfer,qris',
            'amount_paid' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $items = [];

            // Process each item and calculate subtotal
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}");
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

            // Gunakan nilai diskon dan pajak dari request
            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            
            // Validasi: Total harus dihitung ulang di backend untuk keamanan
            // Total = (Subtotal - Diskon) + Pajak
            // Pastikan diskon tidak melebihi subtotal
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }
            
            $totalAmount = max(0, $subtotal - $discount + $tax);
            $amountPaid = $request->amount_paid;
            $changeAmount = 0;

            if ($request->payment_method === 'utang') {
                $changeAmount = 0; // Utang tidak ada kembalian di POS standar
            } else {
                 $changeAmount = $amountPaid - $totalAmount;
                 if ($changeAmount < 0) {
                     throw new \Exception('Jumlah pembayaran kurang dari total belanja.');
                 }
            }

            // Backdate Logic
            $transactionDate = now();
            if ($request->filled('transaction_date') && auth()->user()->hasPermission('can_backdate_sales')) {
                try {
                    $inputDate = \Carbon\Carbon::parse($request->transaction_date);
                    // Keep the current time, just change the date
                    $transactionDate = $inputDate->setTimeFrom(now());
                } catch (\Exception $e) {
                    // Ignore invalid date, use now()
                }
            }

            // Get active shift
            $storeSettings = \App\Models\Setting::getStoreSettings();
            $shiftId = null;
            if ($storeSettings->enable_shift) {
                $activeShift = \App\Models\CashierShift::where('user_id', auth()->id())
                    ->where('status', 'open')
                    ->first();
                if ($activeShift) {
                    $shiftId = $activeShift->id;
                }
            }

            // Create transaction
            $transaction = Transaction::create([
                'transaction_code' => Transaction::generateTransactionCode(),
                'user_id' => auth()->id(),
                'shift_id' => $shiftId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'status' => 'completed',
                'customer_name' => $request->customer_name ?? 'Umum',
                'note' => $request->note, // Simpan catatan
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);

            // Create transaction items and update stock
            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);

                // Update product stock
                $item['product']->decrement('stock', $item['quantity']);

                // Record stock movement
                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'App\Models\Transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "Penjualan - {$transaction->transaction_code}",
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);
            }

            DB::commit();

            // Kirim Notifikasi
            try {
                $user = auth()->user();
                if ($user) {
                    $user->notify(new \App\Notifications\OrderCreated($transaction));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi: ' . $e->getMessage());
            }

            // Load transaction with items for receipt
            $transaction->load(['items.product', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses.',
                'transaction' => $transaction,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $transaction->load(['items.product', 'user']);

        return response()->json($transaction);
    }
}
