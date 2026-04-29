@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-red-600 p-6 text-center">
            <h2 class="text-3xl font-bold text-white">Tutup Shift Kasir</h2>
            <p class="text-red-100 mt-1">Laporan akhir shift dan perhitungan uang laci</p>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Mulai Shift</p>
                    <p class="text-lg font-bold text-gray-900">{{ $shift->start_time->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Kasir Bertugas</p>
                    <p class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</p>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Ringkasan Sistem</h3>
            <div class="space-y-3 mb-8">
                <div class="flex justify-between items-center text-gray-600">
                    <span>Modal Laci Awal:</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-gray-600">
                    <span>Penjualan Tunai:</span>
                    <span class="font-medium text-green-600">+ Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-indigo-50 p-3 rounded-lg border border-indigo-100 mt-2">
                    <span class="font-bold text-indigo-900">Ekspektasi Uang Laci:</span>
                    <span class="font-bold text-indigo-700 text-xl">Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
                </div>
            </div>

            <form action="{{ route('pos.shift.update') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="actual_cash" class="block text-sm font-bold text-gray-900 mb-2">Uang Fisik Dihitung (Rp) <span class="text-red-500">*</span></label>
                    <p class="text-sm text-gray-500 mb-3">Silakan hitung uang fisik di laci Anda saat ini dan masukkan jumlah totalnya.</p>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-xl font-bold">Rp</span>
                        </div>
                        <input type="number" name="actual_cash" id="actual_cash" 
                            class="focus:ring-red-500 focus:border-red-500 block w-full pl-14 pr-4 py-4 sm:text-xl font-bold border-gray-300 rounded-xl bg-yellow-50" 
                            placeholder="0" required min="0">
                    </div>
                    @error('actual_cash')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Laporan <span class="text-gray-400">(Opsional)</span></label>
                    <textarea name="notes" id="notes" rows="3" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-xl p-3" placeholder="Jika ada selisih uang atau kejadian khusus, catat di sini..."></textarea>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('dashboard') }}" class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Tutup Shift & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
