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
        // Ganti menjadi paginate() untuk membagi data per halaman.
        $users = User::latest()->paginate(10); // Angka 10 bisa disesuaikan

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdminAccess();

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kasir',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'permissions' => $request->role === 'kasir' ? $request->input('permissions', []) : null,
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

        return view('users.edit', compact('user'));
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
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
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
