@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center pb-6 border-b-2 border-gray-200 mb-8">
            <a href="{{ route('users.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">👥 Edit User</h1>
                <p class="text-gray-600 mt-1">Memperbarui: <span class="font-semibold">{{ $user->name }}</span></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200">
             <div class="p-8">
                <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div x-data="{ role: '{{ old('role', $user->role) }}' }">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="role" x-model="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror">
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                            </select>
                            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Permissions Section (Only for Kasir) -->
                        <div x-show="role === 'kasir'" x-transition class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Hak Akses Fitur & Menu (Khusus Kasir)</h3>
                            
                            <div class="space-y-3">
                                <!-- Fitur Khusus -->
                                <div class="pb-3 border-b border-gray-200">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Fitur POS</h4>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="permissions[can_backdate_sales]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @checked(old('permissions.can_backdate_sales', $user->hasPermission('can_backdate_sales')))>
                                        <span class="text-sm text-gray-700">Izinkan Input Penjualan Transaksi Lama (Backdate)</span>
                                    </label>
                                </div>

                                <!-- Akses Menu Sidebar -->
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Akses Menu Sidebar</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_products]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_products', $user->hasPermission('view_products')))>
                                            <span class="text-sm text-gray-700">Produk</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_suppliers]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_suppliers', $user->hasPermission('view_suppliers')))>
                                            <span class="text-sm text-gray-700">Suppliers</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_customers]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_customers', $user->hasPermission('view_customers')))>
                                            <span class="text-sm text-gray-700">Pelanggan</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_categories]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_categories', $user->hasPermission('view_categories')))>
                                            <span class="text-sm text-gray-700">Kategori</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_expenses]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_expenses', $user->hasPermission('view_expenses')))>
                                            <span class="text-sm text-gray-700">Pengeluaran</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_purchases]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_purchases', $user->hasPermission('view_purchases')))>
                                            <span class="text-sm text-gray-700">Pembelian</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_transactions]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_transactions', $user->hasPermission('view_transactions')))>
                                            <span class="text-sm text-gray-700">Riwayat Transaksi</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_reports]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_reports', $user->hasPermission('view_reports')))>
                                            <span class="text-sm text-gray-700">Laporan</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[view_receivables]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('permissions.view_receivables', $user->hasPermission('view_receivables')))>
                                            <span class="text-sm text-gray-700">Piutang</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                         <p class="text-sm text-gray-600 mb-4">Ubah Password <span class="text-gray-400">(Kosongkan jika tidak ingin mengubah)</span></p>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg transition-colors duration-200">Batal</a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors duration-200">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

