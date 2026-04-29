<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['warehouses', 'users', 'transactions'])
            ->latest()
            ->paginate(10);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        Branch::create($request->only(['name', 'address', 'phone', 'is_active']));

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $branch->update($request->only(['name', 'address', 'phone', 'is_active']));

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Cabang tidak dapat dihapus karena masih memiliki user terdaftar.');
        }
        if ($branch->transactions()->count() > 0) {
            return back()->with('error', 'Cabang tidak dapat dihapus karena sudah memiliki transaksi.');
        }
        $branch->warehouses()->delete();
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
