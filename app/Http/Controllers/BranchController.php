<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'receipt_footer' => 'nullable|string',
            'paper_size' => 'nullable|in:58,80',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'is_active', 'receipt_footer', 'paper_size']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('branches', 'public');
        }

        Branch::create($data);

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
            'receipt_footer' => 'nullable|string',
            'paper_size' => 'nullable|in:58,80',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'is_active', 'receipt_footer', 'paper_size']);

        if ($request->hasFile('logo')) {
            if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
                Storage::disk('public')->delete($branch->logo);
            }
            $data['logo'] = $request->file('logo')->store('branches', 'public');
        } elseif ($request->input('remove_logo') == '1') {
            if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
                Storage::disk('public')->delete($branch->logo);
            }
            $data['logo'] = null;
        }

        $branch->update($data);

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
        if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
            Storage::disk('public')->delete($branch->logo);
        }
        $branch->warehouses()->delete();
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
