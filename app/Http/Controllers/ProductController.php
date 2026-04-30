<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $allowedPerPages = [10, 20, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPages)) $perPage = 10;

        $query = Product::with('category');

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

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->whereRaw('(SELECT COALESCE(SUM(pw.stock), 0) FROM product_warehouse pw WHERE pw.product_id = products.id) = 0');
            }
        }

        $user = auth()->user();
        $activeWarehouse = $user->getActiveWarehouse();

        $products = $query->latest()->paginate($perPage);
        $products->getCollection()->transform(function ($product) use ($activeWarehouse) {
            $product->total_stock = $product->getTotalStock();
            $product->branch_stock = $activeWarehouse ? $product->getStockInWarehouse($activeWarehouse->id) : null;
            return $product;
        });

        $categories = Category::all();
        
        $branchId = auth()->user()->branch_id ?? session('admin_active_branch_id');
        $warehousesQuery = Warehouse::active();
        if ($branchId) {
            $warehousesQuery->where('branch_id', $branchId);
        }
        $warehouses = $warehousesQuery->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search', 'category', 'stock_status', 'per_page', 'warehouse'])
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        
        $branchId = auth()->user()->branch_id ?? session('admin_active_branch_id');
        $warehousesQuery = Warehouse::active()->with('branch');
        if ($branchId) {
            $warehousesQuery->where('branch_id', $branchId);
        }
        $warehouses = $warehousesQuery->get();

        return view('products.create', compact('categories', 'warehouses'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $hasVariants = $request->boolean('has_variants', false);
            $warehouseId = $request->input('warehouse_id');
            if (!$warehouseId) {
                $wh = auth()->user()->getActiveWarehouse();
                $warehouseId = $wh ? $wh->id : null;
            }

            $productGroup = \App\Models\ProductGroup::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'has_variants' => $hasVariants,
            ]);

            if ($hasVariants) {
                foreach ($request->input('variants', []) as $variant) {
                    $barcode = $variant['barcode'] ?? 'BRC-' . time() . '-' . rand(100, 999);
                    $product = Product::create([
                        'product_group_id' => $productGroup->id,
                        'name' => $validated['name'] . ' (' . $variant['variant_name'] . ')',
                        'variant_name' => $variant['variant_name'],
                        'barcode' => $barcode,
                        'category_id' => $validated['category_id'],
                        'purchase_price' => $variant['purchase_price'] ?? 0,
                        'selling_price' => $variant['selling_price'] ?? 0,
                        'stock' => 0,
                        'minimum_stock' => $validated['minimum_stock'] ?? 10,
                        'description' => $validated['description'],
                        'image' => null,
                    ]);
                    $initStock = $variant['stock'] ?? 0;
                    $this->syncProductWarehouse($product->id, $warehouseId, $initStock);
                    if ($initStock > 0) {
                        StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $warehouseId, 'type' => 'in', 'quantity' => $initStock, 'notes' => 'Stok awal varian']);
                    }
                }
            } else {
                if (empty($validated['barcode'])) $validated['barcode'] = 'BRC-' . time() . '-' . rand(100, 999);
                if ($request->hasFile('image')) $validated['image'] = $request->file('image')->store('products', 'public');
                $validated['product_group_id'] = $productGroup->id;
                $initStock = $validated['stock'] ?? 0;
                $validated['stock'] = 0;
                $product = Product::create($validated);
                $this->syncProductWarehouse($product->id, $warehouseId, $initStock);
                if ($initStock > 0) {
                    StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $warehouseId, 'type' => 'in', 'quantity' => $initStock, 'notes' => 'Stok awal produk']);
                }
            }
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage())->withInput();
        }
    }

    private function syncProductWarehouse(int $productId, ?int $targetWarehouseId, float $stock = 0): void
    {
        $warehouses = Warehouse::active()->get();
        foreach ($warehouses as $wh) {
            $whStock = ($wh->id == $targetWarehouseId) ? $stock : 0;
            DB::table('product_warehouse')->insert([
                'product_id' => $productId, 'warehouse_id' => $wh->id,
                'stock' => $whStock, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements' => function ($query) {
            $query->with('warehouse')->latest()->take(10);
        }, 'warehouses']);
        $product->total_stock = $product->getTotalStock();
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        
        $branchId = auth()->user()->branch_id ?? session('admin_active_branch_id');
        $warehousesQuery = Warehouse::active()->with('branch');
        if ($branchId) {
            $warehousesQuery->where('branch_id', $branchId);
        }
        $warehouses = $warehousesQuery->get();

        $variants = [];
        if ($product->product_group_id) {
            $variants = Product::where('product_group_id', $product->product_group_id)->get();
        }
        $warehouseStocks = DB::table('product_warehouse')->where('product_id', $product->id)->get()->keyBy('warehouse_id');
        return view('products.edit', compact('product', 'categories', 'variants', 'warehouses', 'warehouseStocks'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $hasVariants = $request->boolean('has_variants', false);
        $warehouseId = $request->input('warehouse_id');
        if (!$warehouseId) { $wh = auth()->user()->getActiveWarehouse(); $warehouseId = $wh ? $wh->id : null; }

        DB::beginTransaction();
        try {
            if ($product->product_group_id) {
                $product->productGroup->update(['name' => $data['name'], 'category_id' => $data['category_id'], 'description' => $data['description'] ?? null]);
            }

            if ($hasVariants && $product->product_group_id) {
                foreach ($request->input('variants', []) as $vd) {
                    if (isset($vd['id'])) {
                        $ev = Product::find($vd['id']);
                        if ($ev && $ev->product_group_id == $product->product_group_id) {
                            $oldS = $ev->getStockInWarehouse($warehouseId);
                            $ev->update(['name' => $data['name'] . ' (' . $vd['variant_name'] . ')', 'variant_name' => $vd['variant_name'], 'category_id' => $data['category_id'], 'purchase_price' => $vd['purchase_price'], 'selling_price' => $vd['selling_price'], 'barcode' => $vd['barcode'] ?? $ev->barcode]);
                            $newS = $vd['stock'] ?? $oldS;
                            if ($oldS != $newS) {
                                $diff = $newS - $oldS;
                                DB::table('product_warehouse')->where('product_id', $ev->id)->where('warehouse_id', $warehouseId)->update(['stock' => $newS, 'updated_at' => now()]);
                                StockMovement::create(['product_id' => $ev->id, 'warehouse_id' => $warehouseId, 'type' => $diff > 0 ? 'in' : 'out', 'quantity' => abs($diff), 'notes' => 'Penyesuaian stok manual (Edit Varian)']);
                            }
                        }
                    } else {
                        $barcode = $vd['barcode'] ?? 'BRC-' . time() . '-' . rand(100, 999);
                        $nv = Product::create(['product_group_id' => $product->product_group_id, 'name' => $data['name'] . ' (' . $vd['variant_name'] . ')', 'variant_name' => $vd['variant_name'], 'barcode' => $barcode, 'category_id' => $data['category_id'], 'purchase_price' => $vd['purchase_price'], 'selling_price' => $vd['selling_price'], 'stock' => 0, 'minimum_stock' => 10, 'image' => null]);
                        $is = $vd['stock'] ?? 0;
                        $this->syncProductWarehouse($nv->id, $warehouseId, $is);
                        if ($is > 0) StockMovement::create(['product_id' => $nv->id, 'warehouse_id' => $warehouseId, 'type' => 'in', 'quantity' => $is, 'notes' => 'Stok awal varian baru']);
                    }
                }
                if ($request->hasFile('image')) {
                    if ($product->image && Storage::disk('public')->exists($product->image)) Storage::disk('public')->delete($product->image);
                    $ip = $request->file('image')->store('products', 'public');
                    Product::where('product_group_id', $product->product_group_id)->update(['image' => $ip]);
                } elseif ($request->input('remove_image') == '1') {
                    if ($product->image && Storage::disk('public')->exists($product->image)) Storage::disk('public')->delete($product->image);
                    Product::where('product_group_id', $product->product_group_id)->update(['image' => null]);
                }
                if ($request->has('deleted_variant_ids')) {
                    foreach (explode(',', $request->input('deleted_variant_ids')) as $delId) {
                        $p = Product::find($delId);
                        if ($p && $p->product_group_id == $product->product_group_id && $p->transactionItems()->count() == 0) {
                            DB::table('product_warehouse')->where('product_id', $p->id)->delete();
                            $p->delete();
                        }
                    }
                }
            } else {
                if ($request->hasFile('image')) {
                    if ($product->image && Storage::disk('public')->exists($product->image)) Storage::disk('public')->delete($product->image);
                    $data['image'] = $request->file('image')->store('products', 'public');
                }
                if ($request->input('remove_image') == '1') {
                    if ($product->image && Storage::disk('public')->exists($product->image)) Storage::disk('public')->delete($product->image);
                    $data['image'] = null;
                }
                $oldStock = $product->getStockInWarehouse($warehouseId);
                $newStock = $data['stock'] ?? $oldStock;
                unset($data['stock']);
                $product->update($data);
                if ($oldStock != $newStock) {
                    $diff = $newStock - $oldStock;
                    DB::table('product_warehouse')->updateOrInsert(
                        ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                        ['stock' => $newStock, 'updated_at' => now()]
                    );
                    StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $warehouseId, 'type' => $diff > 0 ? 'in' : 'out', 'quantity' => abs($diff), 'notes' => 'Penyesuaian stok manual']);
                }
            }
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        if ($product->transactionItems()->count() > 0) {
            return redirect()->back()->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi penjualan.');
        }
        try {
            DB::beginTransaction();
            $groupId = $product->product_group_id;
            $product->stockMovements()->delete();
            DB::table('product_warehouse')->where('product_id', $product->id)->delete();
            if ($product->image && Storage::disk('public')->exists($product->image)) Storage::disk('public')->delete($product->image);
            $product->delete();
            if ($groupId && Product::where('product_group_id', $groupId)->count() === 0) {
                \App\Models\ProductGroup::where('id', $groupId)->delete();
            }
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function printBarcodes(Request $request)
    {
        $productIds = $request->input('selected_ids');
        $productsToPrint = ($productIds && is_array($productIds)) ? Product::whereIn('id', $productIds)->get() : Product::all();
        if ($productsToPrint->isEmpty()) return redirect()->back()->with('error', 'Tidak ada produk untuk dicetak.');
        return view('products.barcodes', ['products' => $productsToPrint]);
    }
}
