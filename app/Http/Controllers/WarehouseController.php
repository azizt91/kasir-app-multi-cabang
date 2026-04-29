<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('branch')
            ->withCount('products')
            ->latest()
            ->paginate(10);
        $branches = Branch::active()->get();
        return view('warehouses.index', compact('warehouses', 'branches'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        return view('warehouses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string',
        ]);

        $warehouse = Warehouse::create($request->only(['name', 'branch_id', 'location', 'is_active']));

        // Auto-create product_warehouse records for all existing products
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            \Illuminate\Support\Facades\DB::table('product_warehouse')->insert([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::active()->get();
        return view('warehouses.edit', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string',
        ]);

        $warehouse->update($request->only(['name', 'branch_id', 'location', 'is_active']));

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has stock
        $hasStock = \Illuminate\Support\Facades\DB::table('product_warehouse')
            ->where('warehouse_id', $warehouse->id)
            ->where('stock', '>', 0)
            ->exists();

        if ($hasStock) {
            return back()->with('error', 'Gudang tidak dapat dihapus karena masih memiliki stok produk. Pindahkan stok terlebih dahulu.');
        }

        if ($warehouse->transactions()->count() > 0) {
            return back()->with('error', 'Gudang tidak dapat dihapus karena sudah memiliki transaksi.');
        }

        \Illuminate\Support\Facades\DB::table('product_warehouse')->where('warehouse_id', $warehouse->id)->delete();
        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
