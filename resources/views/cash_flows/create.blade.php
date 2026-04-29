@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center mb-8">
            <a href="{{ route('cash-flows.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tambah Arus Kas</h1>
                <p class="text-gray-500 mt-1">Input data mutasi kas manual untuk menjaga akurasi laporan keuangan.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-1 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            <form action="{{ route('cash-flows.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Tanggal -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-600 mb-2">Tanggal Transaksi</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
                                       class="block w-full pl-10 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                            </div>
                            @error('date') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tipe -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-600 mb-2">Tipe Mutasi</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative border border-gray-300 rounded-lg cursor-pointer transition-colors hover:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700 flex items-center justify-center py-3">
                                    <input type="radio" name="type" value="in" checked class="sr-only">
                                    <span class="text-sm font-medium">Uang Masuk</span>
                                </label>
                                <label class="relative border border-gray-300 rounded-lg cursor-pointer transition-colors hover:bg-rose-50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-700 flex items-center justify-center py-3">
                                    <input type="radio" name="type" value="out" class="sr-only">
                                    <span class="text-sm font-medium">Uang Keluar</span>
                                </label>
                            </div>
                            <!-- Styled Radio Buttons Backup for accessibility -->
                            <div class="hidden">
                                <select name="type_select" id="type_select_hidden">
                                    <option value="in">Masuk</option>
                                    <option value="out">Keluar</option>
                                </select>
                            </div>
                            @error('type') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Kategori -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-600 mb-2">Kategori / Sumber Dana</label>
                            <div class="relative shadow-sm rounded-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01"></path></svg>
                                </div>
                                <input type="text" name="category" id="category" placeholder="e.g. Modal Owner, Kas Bon" required
                                       class="block w-full pl-10 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm">
                            </div>
                            @error('category') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-600 mb-2">Nominal Jumlah</label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none bg-gray-50 border-r border-gray-300 rounded-l-xl px-3 group-focus-within:border-indigo-500 group-focus-within:bg-indigo-50 transition-all">
                                    <span class="text-gray-600 font-bold sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="amount" id="amount" step="0.01" required placeholder="0"
                                       class="block w-full pl-16 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg font-bold transition-all">
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 italic">Masukkan angka tanpa titik atau koma kecuali untuk desimal.</p>
                            @error('amount') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Bottom Wide Column -->
                    <div class="md:col-span-2">
                        <label for="note" class="block text-sm font-medium text-gray-600 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="note" id="note" rows="4"
                                  class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm"
                                  placeholder="Tuliskan keterangan detail di sini..."></textarea>
                        @error('note') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-8 pt-4 border-t border-gray-200">
                    <a href="{{ route('cash-flows.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom input focus effects */
    label:has(input[value="in"]:checked) {
        border-color: #10B981;
        background-color: #ecfdf5;
    }
    label:has(input[value="out"]:checked) {
        border-color: #F43F5E;
        background-color: #fff1f2;
    }
    
    /* Custom input focus effects */
    input:focus, select:focus, textarea:focus {
        border-color: rgba(99, 102, 241, 0.4);
    }
</style>
@endsection
