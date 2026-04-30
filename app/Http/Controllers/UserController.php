<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Check if user is admin before any action
     */
    private function checkAdminAccess()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $this->checkAdminAccess();

    //     $users = User::orderBy('created_at', 'desc')->get();

    //     return view('users.index', compact('users'));
    // }
    public function index()
    {
        $authUser = Auth::user();

        if ($authUser->isSuperAdmin()) {
            // Superadmin sees all users
            $users = User::with('branch')->latest()->paginate(10);
        } else {
            // Branch Admin only sees users from their own branch
            $users = User::with('branch')
                ->where('branch_id', $authUser->branch_id)
                ->latest()
                ->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdminAccess();
        $authUser = Auth::user();

        if ($authUser->isSuperAdmin()) {
            $branches = \App\Models\Branch::active()->get();
        } else {
            $branches = \App\Models\Branch::where('id', $authUser->branch_id)->get();
        }

        return view('users.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdminAccess();
        $authUser = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kasir',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Branch Admin can only create users in their own branch
        $branchId = $authUser->isSuperAdmin()
            ? $request->branch_id
            : $authUser->branch_id;

        // Branch Admin cannot create other admins with null branch_id (that would be a Superadmin)
        $role = $request->role;
        if (!$authUser->isSuperAdmin() && $role === 'admin') {
            $role = 'kasir'; // Downgrade to kasir for safety
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'branch_id' => $branchId,
            'permissions' => $role === 'kasir' ? $request->input('permissions', []) : null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->checkAdminAccess();

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->checkAdminAccess();
        $authUser = Auth::user();

        // Security: Branch Admin can only edit users from their own branch
        if (!$authUser->isSuperAdmin() && $user->branch_id !== $authUser->branch_id) {
            abort(403, 'Anda tidak diizinkan mengedit user dari cabang lain.');
        }

        if ($authUser->isSuperAdmin()) {
            $branches = \App\Models\Branch::active()->get();
        } else {
            $branches = \App\Models\Branch::where('id', $authUser->branch_id)->get();
        }

        return view('users.edit', compact('user', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,kasir',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $authUser = Auth::user();

        // Security: Branch Admin cannot move user to another branch or modify users from other branches
        if (!$authUser->isSuperAdmin()) {
            if ($user->branch_id !== $authUser->branch_id) {
                abort(403, 'Anda tidak diizinkan mengubah user dari cabang lain.');
            }
            $request->merge(['branch_id' => $authUser->branch_id]);
            
            // Branch Admin cannot promote to Superadmin (admin with null branch)
            if ($request->role === 'admin' && is_null($authUser->branch_id)) {
                 // this case should not happen based on logic but for safety:
                 $request->merge(['role' => 'kasir']);
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'branch_id' => $request->branch_id,
            'permissions' => $request->role === 'kasir' ? $request->input('permissions', []) : null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkAdminAccess();

        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            }

            $user->delete();

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if the exception is an integrity constraint violation (e.g. foreign key constraint)
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus pengguna ini karena masih memiliki riwayat transaksi atau terhubung dengan data lain.');
            }
            
            // Re-throw if it's a different query exception
            throw $e;
        }
    }
}
