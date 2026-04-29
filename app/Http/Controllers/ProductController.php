<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $query = Product::with('category');

    //     // Search functionality
    //     if ($request->has('search') && $request->search) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //               ->orWhere('barcode', 'like', "%{$search}%")
    //               ->orWhereHas('category', function ($cat) use ($search) {
    //                   $cat->where('name', 'like', "%{$search}%");
    //               });
    //         });
    //     }

    //     // Filter by category
    //     if ($request->has('category') && $request->category) {
    //         $query->where('category_id', $request->category);
    //     }

    //     // Filter by stock status
    //     if ($request->has('stock_status') && $request->stock_status) {
    //         if ($request->stock_status === 'low') {
    //             $query->lowStock();
    //         } elseif ($request->stock_status === 'out') {
    //             $query->where('stock', 0);
    //         }
    //     }

    //     $products = $query->latest()->paginate(10);
    //     $categories = Category::all();

    //     return view('products.index', [
    //         'products' => $products,
    //         'categories' => $categories,
    //         'filters' => $request->only(['search', 'category', 'stock_status'])
    //     ]);
    // }
    // Di dalam file: app/Http-Controllers/ProductController.php

    public function index(Request $request)
    {
        // [TAMBAHKAN INI] Ambil dan validasi nilai per_page dari URL
        $perPage = $request->query('per_page', 10);
        $allowedPerPages = [10, 20, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPages)) {
            $perPage = 10; // Set ke default 10 jika nilainya tidak valid
        }

        $query = Product::with('category');

        // Search functionality (tidak berubah)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->orWhereHas('category', function ($cat) use ($search) {
                    $cat->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by category (tidak berubah)
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by stock status (tidak berubah)
        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
            }
        }

        // [UBAH INI] Ganti angka 10 dengan variabel $perPage
        $products = $query->latest()->paginate($perPage);

        $categories = Category::all();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            // (Opsional) Anda bisa menambahkan per_page di sini jika perlu
            'filters' => $request->only(['search', 'category', 'stock_status', 'per_page'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // Start Database Transaction
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Check if it's a variant product (You'll need to add 'has_variants' to your form/request)
            $hasVariants = $request->boolean('has_variants', false);

            // 1. Create Product Group (Parent)
            $productGroup = \App\Models\ProductGroup::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'has_variants' => $hasVariants,
            ]);

            if ($hasVariants) {
                // Logic for Multiple Variants
                $variants = $request->input('variants', []);
                
                foreach ($variants as $variant) {
                    $barcode = $variant['barcode'] ?? 'BRC-' . time() . '-' . rand(100, 999);
                    
                    $product = Product::create([
                        'product_group_id' => $productGroup->id,
                        'name' => $validated['name'] . ' (' . $variant['variant_name'] . ')',
                        'variant_name' => $variant['variant_name'],
                        'barcode' => $barcode,
                        'category_id' => $validated['category_id'],
                        'purchase_price' => $variant['purchase_price'] ?? 0,
                        'selling_price' => $variant['selling_price'] ?? 0,
                        'stock' => $variant['stock'] ?? 0,
                        'minimum_stock' => $validated['minimum_stock'] ?? 10,
                        'description' => $validated['description'],
                        'image' => null, // Handle variant images if needed later
                    ]);

                    // Initial Stock for Variant
                    if ($product->stock > 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => 'in',
                            'quantity' => $product->stock,
                            'notes' => 'Stok awal varian'
                        ]);
                    }
                }

            } else {
                // Logic for Single Product (Standard)
                // Auto-generate barcode if empty
                if (empty($validated['barcode'])) {
                    $validated['barcode'] = 'BRC-' . time() . '-' . rand(100, 999);
                }

                // Handle image upload
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('products', 'public');
                    $validated['image'] = $imagePath;
                }

                $validated['product_group_id'] = $productGroup->id; 
                
                $product = Product::create($validated);

                // Record initial stock movement if stock > 0
                if ($product->stock > 0) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $product->stock,
                        'notes' => 'Stok awal produk'
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('products.show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $variants = [];

        if ($product->product_group_id) {
            // Load all siblings (variants in the same group)
            $variants = Product::where('product_group_id', $product->product_group_id)->get();
        }

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'variants' => $variants
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // Check if we are updating a Product Group (Variant Mode)
        $hasVariants = $request->boolean('has_variants', false);

        // Start Transaction
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Update Parent Group Info (Always update group if it exists)
            if ($product->product_group_id) {
                $product->productGroup->update([
                    'name' => $data['name'], 
                    'category_id' => $data['category_id'],
                    'description' => $data['description'] ?? null,
                ]);
            }

            if ($hasVariants && $product->product_group_id) {
                // Bulk Update/Create Variants logic
                $variants = $request->input('variants', []);
                $existingVariantIds = [];

                foreach ($variants as $variantData) {
                    if (isset($variantData['id'])) {
                        // Update Existing Variant
                        $existingVariant = Product::find($variantData['id']);
                        if ($existingVariant && $existingVariant->product_group_id == $product->product_group_id) {
                            $oldStock = $existingVariant->stock;
                            
                            $existingVariant->update([
                                'name' => $data['name'] . ' (' . $variantData['variant_name'] . ')',
                                'variant_name' => $variantData['variant_name'],
                                'category_id' => $data['category_id'],
                                'purchase_price' => $variantData['purchase_price'],
                                'selling_price' => $variantData['selling_price'],
                                'stock' => $variantData['stock'],
                                'barcode' => $variantData['barcode'] ?? $existingVariant->barcode,
                            ]);
                            
                            $existingVariantIds[] = $existingVariant->id;

                            // Stock Movement
                            if ($oldStock != $variantData['stock']) {
                                $diff = $variantData['stock'] - $oldStock;
                                StockMovement::create([
                                    'product_id' => $existingVariant->id,
                                    'type' => $diff > 0 ? 'in' : 'out',
                                    'quantity' => abs($diff),
                                    'notes' => 'Penyesuaian stok manual (Edit Varian)'
                                ]);
                            }
                        }
                    } else {
                        // Create New Variant
                        $barcode = $variantData['barcode'] ?? 'BRC-' . time() . '-' . rand(100, 999);
                        $newVariant = Product::create([
                            'product_group_id' => $product->product_group_id,
                            'name' => $data['name'] . ' (' . $variantData['variant_name'] . ')',
                            'variant_name' => $variantData['variant_name'],
                            'barcode' => $barcode,
                            'category_id' => $data['category_id'],
                            'purchase_price' => $variantData['purchase_price'],
                            'selling_price' => $variantData['selling_price'],
                            'stock' => $variantData['stock'],
                            'minimum_stock' => 10, // Default
                            'image' => null,
                        ]);
                        
                        $existingVariantIds[] = $newVariant->id;

                        // Initial Stock
                        if ($newVariant->stock > 0) {
                            StockMovement::create([
                                'product_id' => $newVariant->id,
                                'type' => 'in',
                                'quantity' => $newVariant->stock,
                                'notes' => 'Stok awal varian baru'
                            ]);
                        }
                    }
                }
                
                // Handle Image Upload (Applied to Parent/Current Product for now, or we can apply to all? Keep simple: variants don't have separate images in form yet)
                // If we want to update the "Main" product image being edited:
                 if ($request->hasFile('image')) {
                    if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $imagePath = $request->file('image')->store('products', 'public');
                    // Update ALL variants with this image? Or just one?
                    // Typically variants might share image. Let's update all for consistency if Grouped.
                    Product::where('product_group_id', $product->product_group_id)->update(['image' => $imagePath]);
                } elseif ($request->input('remove_image') == '1') {
                     if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                    Product::where('product_group_id', $product->product_group_id)->update(['image' => null]);
                }

                // Optional: Delete variants not in the list? 
                // Dangerous if user just wanted to edit one. 
                // Let's NOT delete missing variants for safety unless explicitly requested. 
                // Form 'variants' list should include ALL variants if we act as a manager.
                // If we implement 'remove' in frontend, we should probably send a 'deleted_variants' array.
                // For now, let's assume 'remove' in frontend just means "Don't update/include", but real deletion needs separate action or 'delete_ids'.
                if ($request->has('deleted_variant_ids')) {
                    $deletedIds = explode(',', $request->input('deleted_variant_ids'));
                    foreach($deletedIds as $delId) {
                         $p = Product::find($delId);
                         if ($p && $p->product_group_id == $product->product_group_id) {
                            // Check transactions...
                            if ($p->transactionItems()->count() == 0) {
                                $p->delete();
                            }
                         }
                    }
                }

            } else {
                // Standard Single Update
                // Handle image upload
                if ($request->hasFile('image')) {
                    if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $imagePath = $request->file('image')->store('products', 'public');
                    $data['image'] = $imagePath;
                }

                // Handle image removal
                if ($request->input('remove_image') == '1') {
                    if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $data['image'] = null;
                }

                $product->update($data);

                // Record stock movement if stock changed
                if (isset($data['stock']) && $product->getOriginal('stock') != $data['stock']) {
                    $oldStock = $product->getOriginal('stock'); // Re-fetch or use saved
                    // Actually $product->stock is already updated. Need to compare with stored oldStock.
                    // But in this block I didn't save oldStock variable well. 
                    // Let's use $product->wasChanged() logic or passed $oldStock (not available here easily without query).
                    // simpler:
                    $product->refresh(); // just to be sure
                    // logic is tricky without capturing old stock. 
                    // But wait, standard update logic was:
                    // $oldStock = $product->stock; -> $product->update(); -> compare.
                    // I will Copy-Paste previous logic for Single Mode.
                }
                 // Fix: Single Mode Stock Movement
                 // Need to capture old stock before update. 
                 // $product passed to method is fresh.
                 // Wait, $product argument is bound model. 
                 // I need to use $product->getOriginal('stock') BEFORE update? No, $product is not dirty yet.
                 // Correct logic:
                 $currentStock = $product->stock; // This is DB value
                 // after update...
                 // $product->update($data);
                 // compare $currentStock vs $data['stock']
                 
                 // Let's defer to the standard logic I wrote before, but adapted.
                 
                 // Using the logic from previous block:
                 /* 
                 $oldStock = $product->stock;
                 ...
                 $product->update($data);
                 if ($oldStock !== $product->stock) ...
                 */
                 // I need to restore that flow.
                 
                 // But wait, I am rewriting the Whole method.
                 
                 // Re-implementing Single Update stock logic:
                 $singleOldStock = $product->stock;
                 
                 // Image handling (already done above in if/else?) No, I need to do it here for Single.
                 
                 $product->update($data);
                 
                 if ($singleOldStock !== $product->stock) {
                    $diff = $product->stock - $singleOldStock;
                     StockMovement::create([
                        'product_id' => $product->id,
                        'type' => $diff > 0 ? 'in' : 'out',
                        'quantity' => abs($diff),
                        'notes' => 'Penyesuaian stok manual'
                    ]);
                 }
            }
            
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if product is used in any transactions
        if ($product->transactionItems()->count() > 0) {
            return redirect()->back()->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi penjualan. Untuk menghentikan penjualan produk ini, ubah stok menjadi 0.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $groupId = $product->product_group_id;

            // Delete related stock movements first (Standard)
            $product->stockMovements()->delete();

            // Delete product image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            // Check if Group is empty or has only this product (if using before delete count)
            // Since we already deleted the product, we check if group has remaining products
            if ($groupId) {
                $remainingProducts = Product::where('product_group_id', $groupId)->count();
                if ($remainingProducts === 0) {
                    \App\Models\ProductGroup::where('id', $groupId)->delete();
                }
            }
            
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function printBarcodes(Request $request)
    {
        $productIds = $request->input('selected_ids');

        // Cek apakah ada produk yang dipilih, jika tidak, ambil semua produk
        if ($productIds && is_array($productIds)) {
            $productsToPrint = Product::whereIn('id', $productIds)->get();
        } else {
            $productsToPrint = Product::all();
        }

        // Jika tidak ada produk sama sekali, kembali
        if ($productsToPrint->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada produk untuk dicetak.');
        }

        // Kirim data produk ke view khusus untuk cetak barcode
        return view('products.barcodes', ['products' => $productsToPrint]);
    }


}
