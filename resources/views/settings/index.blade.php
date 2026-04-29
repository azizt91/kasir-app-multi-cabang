@php
use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 border-b-2 border-gray-200 mb-8">
            <h1 class="text-3xl font-bold text-gray-900">⚙️ Pengaturan Toko</h1>
            <p class="text-gray-600 mt-1">Kelola informasi umum dan branding toko Anda.</p>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-6 sm:p-8 space-y-8">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 mb-6">Informasi Utama</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                                <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $settings->store_name) }}" required
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Contoh: Toko Barokah Jaya">
                                @error('store_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <input type="text" id="store_phone" name="store_phone" value="{{ old('store_phone', $settings->store_phone) }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="(021) 123-4567">
                                @error('store_phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="store_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                                <textarea id="store_address" name="store_address" rows="3"
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                          placeholder="Jl. Pahlawan No. 123, Kota Bahagia">{{ old('store_address', $settings->store_address) }}</textarea>
                                @error('store_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="store_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi/Footer Struk <span class="text-gray-500">(Opsional)</span></label>
                                <textarea id="store_description" name="store_description" rows="3"
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                          placeholder="Contoh: Terima kasih telah berbelanja! Barang yang sudah dibeli tidak dapat dikembalikan.">{{ old('store_description', $settings->store_description) }}</textarea>
                                @error('store_description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Pajak Default (%)</label>
                                <div class="relative">
                                    <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate ?? 0) }}" step="0.1" min="0" max="100"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-8"
                                           placeholder="0">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Nilai ini akan menjadi default pajak di halaman kasir.</p>
                                @error('tax_rate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 mb-6">Sistem Kasir</h2>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-4 flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                                <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-indigo-800">Fitur Shift Kasir (Pertanggungjawaban Laci)</h3>
                                <p class="mt-1 text-sm text-indigo-700">
                                    Jika diaktifkan, kasir wajib memasukkan <strong>Modal Laci (Uang Kembalian)</strong> sebelum mulai berjualan, dan harus melakukan <strong>Tutup Kasir</strong> untuk mencocokkan uang fisik dengan pendapatan sistem. Cocok untuk toko yang dijaga oleh karyawan.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <div>
                                <label for="enable_shift" class="block text-sm font-medium text-gray-900">Aktifkan Sistem Shift Kasir</label>
                                <p class="text-xs text-gray-500">Gunakan fitur Buka/Tutup laci kasir</p>
                            </div>
                            <!-- Toggle Button -->
                            <label for="enable_shift" class="relative inline-flex items-center cursor-pointer">
                                <!-- Hidden checkbox that stores 0 or 1 -->
                                <input type="hidden" name="enable_shift" value="0">
                                <input type="checkbox" id="enable_shift" name="enable_shift" value="1" class="sr-only peer" {{ old('enable_shift', $settings->enable_shift ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 mb-6">Branding Toko</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                            <div class="md:col-span-1">
                                <p class="block text-sm font-medium text-gray-700 mb-2">Logo Saat Ini</p>
                                <div class="aspect-square w-32 h-32 bg-gray-100 rounded-lg border flex items-center justify-center">
                                    @if($settings->store_logo && Storage::disk('public')->exists($settings->store_logo))
                                        <img src="{{ Storage::url($settings->store_logo) }}" alt="Logo Toko" class="w-full h-full object-contain p-2 rounded-lg">
                                    @else
                                        <div class="text-center text-gray-500 text-xs p-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="mt-1">Belum ada logo</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label for="store_logo" class="block text-sm font-medium text-gray-700 mb-2">Upload Logo Baru <span class="text-gray-500">(Opsional)</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="store_logo_input" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload file</span>
                                                <input id="store_logo_input" name="store_logo" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">atau tarik dan lepas</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                    </div>
                                </div>
                                @error('store_logo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 mb-6">Printer Struk</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Paper Size Select --}}
                            <div class="md:col-span-1">
                                <label for="paper_size" class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas Printer</label>
                                <select id="paper_size" name="paper_size"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="58mm" {{ old('paper_size', $settings->paper_size ?? '58mm') == '58mm' ? 'selected' : '' }}>58mm (Mini / Kecil)</option>
                                    <option value="80mm" {{ old('paper_size', $settings->paper_size ?? '58mm') == '80mm' ? 'selected' : '' }}>80mm (Standar / Besar)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih ukuran kertas sesuai printer thermal Anda.</p>
                                @error('paper_size')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Default Printer Select --}}
                            <div class="md:col-span-1">
                                <label for="default_printer" class="block text-sm font-medium text-gray-700 mb-1">Default Printer Cetak Otomatis</label>
                                <select id="default_printer" name="default_printer"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="browser" {{ old('default_printer', $settings->default_printer ?? 'browser') == 'browser' ? 'selected' : '' }}>📄 Browser Print (Gunakan Driver OS)</option>
                                    <option value="bluetooth" {{ old('default_printer', $settings->default_printer ?? 'browser') == 'bluetooth' ? 'selected' : '' }}>📶 Bluetooth (Web Bluetooth API)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Metode cetak otomatis saat transaksi selesai.</p>
                                @error('default_printer')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                            {{-- Removed unused printer test cards to keep UI clean --}}
                        </div>
                    </div>

                    </div>
                </div>

                <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-md transition-transform transform hover:scale-105 duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div id="success-toast" class="fixed bottom-5 right-5 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg" role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" onclick="document.getElementById('success-toast').style.display='none'">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('success-toast');
                    if(toast) toast.style.display = 'none';
                }, 5000);
            </script>
        @endif

    </div>
</div>
@endsection



