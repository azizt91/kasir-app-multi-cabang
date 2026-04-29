@extends('layouts.app')

@section('content')
<div class="bg-gray-100 h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto shadow-md mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white">Buka Shift Kasir</h2>
            <p class="text-indigo-100 mt-1">Masukkan modal awal laci sebelum berjualan</p>
        </div>

        <div class="p-8">
            <form action="{{ route('pos.shift.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="starting_cash" class="block text-sm font-medium text-gray-700 mb-2">Uang Modal Laci (Rp)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-lg">Rp</span>
                        </div>
                        <input type="number" name="starting_cash" id="starting_cash" 
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 pr-12 py-3 sm:text-lg border-gray-300 rounded-xl" 
                            placeholder="0" required min="0" value="{{ old('starting_cash') }}">
                    </div>
                    @error('starting_cash')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Uang fisik ini biasanya digunakan untuk uang kembalian pertama kali.</p>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        Mulai Jualan
                    </button>
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
