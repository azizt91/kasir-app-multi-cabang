<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function index()
    {
        $cashFlows = CashFlow::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('cash_flows.index', compact('cashFlows'));
    }

    public function create()
    {
        return view('cash_flows.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        CashFlow::create([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('cash-flows.index')->with('success', 'Data arus kas berhasil disimpan.');
    }

    public function edit(CashFlow $cashFlow)
    {
        return view('cash_flows.edit', compact('cashFlow'));
    }

    public function update(Request $request, CashFlow $cashFlow)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $cashFlow->update([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
        ]);

        return redirect()->route('cash-flows.index')->with('success', 'Data arus kas berhasil diperbarui.');
    }

    public function destroy(CashFlow $cashFlow)
    {
        $cashFlow->delete();
        return redirect()->route('cash-flows.index')->with('success', 'Data arus kas berhasil dihapus.');
    }
}
