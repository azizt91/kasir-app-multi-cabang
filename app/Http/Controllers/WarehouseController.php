<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('branch')
            ->withCount('products')
            ->latest()
            ->paginate(10);
            
        $authUser = Auth::user();
        if ($authUser->isSuperAdmin()) {
            $branches = Branch::active()->get();
        } else {
            $branches = Branch::where('id', $authUser->branch_id)->get();
        }
        
        return view('warehouses.index', compact('warehouses', 'branches'));
    }

    public function create()
    {
        $authUser = Auth::user();
        if ($authUser->isSuperAdmin()) {
            $branches = Branch::active()->get();
        } else {
            $branches = Branch::where('id', $authUser->branch_id)->get();
        }
        
        return view('warehouses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string',
        ]);

        $branchId = $authUser->isSuperAdmin() ? $request->branch_id : $authUser->branch_id;

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'branch_id' => $branchId,
            'location' => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);

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
        $authUser = Auth::user();
        
        // Security check
        if (!$authUser->isSuperAdmin() && $warehouse->branch_id !== $authUser->branch_id) {
            abort(403, 'Anda tidak diizinkan mengelola gudang di cabang lain.');
        }

        if ($authUser->isSuperAdmin()) {
            $branches = Branch::active()->get();
        } else {
            $branches = Branch::where('id', $authUser->branch_id)->get();
        }
        
        return view('warehouses.edit', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $authUser = Auth::user();
        
        // Security check
        if (!$authUser->isSuperAdmin() && $warehouse->branch_id !== $authUser->branch_id) {
            abort(403, 'Anda tidak diizinkan mengelola gudang di cabang lain.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string',
        ]);

        $branchId = $authUser->isSuperAdmin() ? $request->branch_id : $authUser->branch_id;

        $warehouse->update([
            'name' => $request->name,
            'branch_id' => $branchId,
            'location' => $request->location,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $authUser = Auth::user();
        
        // Security check
        if (!$authUser->isSuperAdmin() && $warehouse->branch_id !== $authUser->branch_id) {
            abort(403, 'Anda tidak diizinkan mengelola gudang di cabang lain.');
        }

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
