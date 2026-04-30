<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchFilterController extends Controller
{
    public function setFilter(Request $request)
    {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if (empty($request->branch_id)) {
            session()->forget('admin_active_branch_id');
        } else {
            session(['admin_active_branch_id' => $request->branch_id]);
        }

        return back()->with('success', 'Konteks cabang berhasil diubah.');
    }
}
