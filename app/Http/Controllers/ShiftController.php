<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\CashFlow;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Show the form for opening a shift.
     */
    public function create()
    {
        $storeSettings = Setting::getStoreSettings();
        if (!$storeSettings->enable_shift) {
            return redirect()->route('pos.index');
        }

        // Check if user already has an open shift
        $activeShift = CashierShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return redirect()->route('pos.index')->with('success', 'Anda sudah memiliki shift yang aktif.');
        }

        return view('pos.shift.create');
    }

    /**
     * Store a newly created shift.
     */
    public function store(Request $request)
    {
        $storeSettings = Setting::getStoreSettings();
        if (!$storeSettings->enable_shift) {
            return redirect()->route('pos.index');
        }

        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        // Check again to prevent double opening
        $activeShift = CashierShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return redirect()->route('pos.index');
        }

        CashierShift::create([
            'user_id' => auth()->id(),
            'start_time' => Carbon::now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift kasir berhasil dibuka. Selamat bertugas!');
    }

    /**
     * Show the form for closing the shift.
     */
    public function edit()
    {
        $storeSettings = Setting::getStoreSettings();
        if (!$storeSettings->enable_shift) {
            return redirect()->route('pos.index');
        }

        $shift = CashierShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()->route('pos.index')->with('error', 'Anda tidak memiliki shift yang aktif.');
        }

        // Hitung total penjualan tunai selama shift ini
        $cashSales = Transaction::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total_amount');

        $expectedCash = $shift->starting_cash + $cashSales;

        return view('pos.shift.close', compact('shift', 'cashSales', 'expectedCash'));
    }

    /**
     * Update (Close) the specified shift.
     */
    public function update(Request $request)
    {
        $storeSettings = Setting::getStoreSettings();
        if (!$storeSettings->enable_shift) {
            return redirect()->route('pos.index');
        }

        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shift = CashierShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()->route('pos.index')->with('error', 'Shift tidak ditemukan atau sudah ditutup.');
        }

        // Recalculate expected cash
        $cashSales = Transaction::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total_amount');

        $expectedCash = $shift->starting_cash + $cashSales;
        $difference = $request->actual_cash - $expectedCash;

        $shift->update([
            'end_time' => Carbon::now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $request->actual_cash,
            'difference' => $difference,
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        // LOGIKA BARU: Jika ada selisih, buat record di cash_flows (PENDING)
        if ($difference != 0) {
            CashFlow::create([
                'date' => Carbon::today(),
                'type' => $difference > 0 ? 'in' : 'out',
                'category' => 'Selisih Kas',
                'amount' => abs($difference),
                'note' => "Selisih closing shift #{$shift->id} oleh " . auth()->user()->name . ". Ekspektasi: " . number_format($expectedCash) . ", Aktual: " . number_format($request->actual_cash),
                'user_id' => auth()->id(),
                'branch_id' => auth()->user()->branch_id,
                'shift_id' => $shift->id,
                'is_adjustment' => true,
                'status' => 'pending', // Menunggu approval Superadmin
            ]);
        }

        $msg = 'Shift berhasil ditutup.';
        if ($difference != 0) {
            $msg .= ' Terdapat selisih kas sebesar Rp ' . number_format(abs($difference), 0, ',', '.') . ' yang menunggu persetujuan Superadmin.';
        }

        return redirect()->route('dashboard')->with('success', $msg);
    }
}
