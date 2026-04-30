@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 border-b-2 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">✏️ Edit Cabang</h1>
            <p class="text-gray-600 mt-1">Perbarui informasi dan pengaturan struk cabang "{{ $branch->name }}".</p>
        </div>
        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        @endif
        
        <div x-data="{
            name: '{{ addslashes(old('name', $branch->name)) }}',
            address: '{{ addslashes(old('address', $branch->address)) }}',
            phone: '{{ addslashes(old('phone', $branch->phone)) }}',
            footer: '{{ addslashes(old('receipt_footer', $branch->receipt_footer)) }}',
            paper_size: '{{ old('paper_size', $branch->paper_size ?? '58') }}',
            globalName: '{{ addslashes(\App\Models\Setting::getStoreSettings()->store_name ?? 'Toko Minimarket') }}',
            globalAddress: '{{ addslashes(\App\Models\Setting::getStoreSettings()->store_address ?? 'Alamat Pusat') }}',
            globalPhone: '{{ addslashes(\App\Models\Setting::getStoreSettings()->store_phone ?? '08123456789') }}',
            globalFooter: '{{ addslashes(\App\Models\Setting::getStoreSettings()->store_description ?? 'Terima Kasih') }}',
            hasLogo: {{ $branch->logo ? 'true' : 'false' }},
            logoPreview: '{{ $branch->logo ? asset('storage/'.$branch->logo) : '' }}',
            globalLogo: '{{ \App\Models\Setting::getStoreSettings()->store_logo ? asset('storage/'.\App\Models\Setting::getStoreSettings()->store_logo) : '' }}',
            handleLogoChange(e) {
                const file = e.target.files[0];
                if (file) {
                    this.logoPreview = URL.createObjectURL(file);
                } else {
                    this.logoPreview = '{{ $branch->logo ? asset('storage/'.$branch->logo) : '' }}';
                }
            }
        }" class="mt-8 lg:grid lg:grid-cols-3 lg:gap-8 items-start">
            
            <div class="lg:col-span-2">
                <form action="{{ route('branches.update', $branch) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-6">
                    @csrf @method('PUT')
                    
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Dasar</h3>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Cabang <span class="text-red-500">*</span></label>
                        <input x-model="name" type="text" name="name" id="name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Cabang Aktif</label>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 pt-4">Identitas & Pengaturan Struk</h3>
                    <p class="text-xs text-gray-500 mb-4">Kosongkan kolom di bawah ini jika ingin menggunakan pengaturan identitas pusat.</p>
                    
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">Logo Cabang</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <input @change="handleLogoChange" type="file" name="logo" id="logo" accept="image/png, image/jpeg, image/jpg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <template x-if="hasLogo">
                                <label class="inline-flex items-center text-sm text-red-600 hover:text-red-800 cursor-pointer">
                                    <input type="checkbox" name="remove_logo" value="1" class="mr-2" @change="logoPreview = $el.checked ? '' : '{{ $branch->logo ? asset('storage/'.$branch->logo) : '' }}'"> Hapus Logo Lama
                                </label>
                            </template>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Maks. 2MB. Format: PNG, JPG, JPEG.</p>
                        @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Cabang</label>
                            <textarea x-model="address" name="address" id="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Kosongkan untuk menggunakan alamat pusat"></textarea>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telepon Cabang</label>
                            <input x-model="phone" type="text" name="phone" id="phone" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Kosongkan untuk telp pusat">
                        </div>
                        <div>
                            <label for="paper_size" class="block text-sm font-medium text-gray-700">Ukuran Kertas Printer</label>
                            <select x-model="paper_size" name="paper_size" id="paper_size" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="58">58mm</option>
                                <option value="80">80mm</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="receipt_footer" class="block text-sm font-medium text-gray-700">Footer Struk</label>
                            <textarea x-model="footer" name="receipt_footer" id="receipt_footer" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Terima kasih atas kunjungan Anda"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('branches.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- Preview Struk -->
            <div class="mt-8 lg:mt-0 lg:col-span-1 sticky top-8">
                <div class="bg-gray-800 rounded-t-xl p-3 text-center text-gray-100 font-semibold text-sm">
                    Live Preview Struk
                </div>
                <div class="bg-white p-6 shadow-xl rounded-b-xl border border-gray-200 flex justify-center overflow-x-auto bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNlNWU3ZWIiLz48L3N2Zz4=')]">
                    
                    <!-- Paper container -->
                    <div class="bg-white shadow-md font-mono text-gray-900 relative transition-all duration-300 mx-auto"
                         :style="paper_size == '80' ? 'width: 300px; padding: 20px;' : 'width: 220px; padding: 10px;'">
                        
                        <div class="text-center pb-3 border-b-2 border-dashed border-gray-400 mb-3">
                            <!-- Logo -->
                            <template x-if="logoPreview">
                                <img :src="logoPreview" alt="Logo" class="mx-auto mb-2" :class="paper_size == '80' ? 'h-16' : 'h-12'">
                            </template>
                            <template x-if="!logoPreview && globalLogo">
                                <img :src="globalLogo" alt="Logo" class="mx-auto mb-2 opacity-60" :class="paper_size == '80' ? 'h-16' : 'h-12'">
                            </template>
                            
                            <!-- Name -->
                            <h2 class="font-bold uppercase" :class="paper_size == '80' ? 'text-xl' : 'text-sm'" x-text="globalName"></h2>
                            
                            <!-- Address -->
                            <template x-if="address">
                                <p class="whitespace-pre-line mt-1" :class="paper_size == '80' ? 'text-sm' : 'text-xs'" x-text="address"></p>
                            </template>
                            <template x-if="!address">
                                <p class="whitespace-pre-line mt-1 opacity-50 italic" :class="paper_size == '80' ? 'text-sm' : 'text-xs'">[Menggunakan Pengaturan Pusat]<br><span x-text="globalAddress"></span></p>
                            </template>

                            <!-- Phone -->
                            <template x-if="phone">
                                <p class="mt-1" :class="paper_size == '80' ? 'text-sm' : 'text-xs'">Telp: <span x-text="phone"></span></p>
                            </template>
                            <template x-if="!phone">
                                <p class="mt-1 opacity-50 italic" :class="paper_size == '80' ? 'text-sm' : 'text-xs'">[Pusat] Telp: <span x-text="globalPhone"></span></p>
                            </template>
                        </div>

                        <!-- Dummy Content -->
                        <div :class="paper_size == '80' ? 'text-sm' : 'text-xs'">
                            <p class="mb-2">Tgl: 12/12/2026 14:00</p>
                            <div class="flex justify-between border-b border-dashed border-gray-300 pb-1 mb-1">
                                <span>Produk A x2</span>
                                <span>10.000</span>
                            </div>
                            <div class="flex justify-between font-bold mt-2">
                                <span>TOTAL</span>
                                <span>10.000</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 text-center border-t-2 border-dashed border-gray-400 pt-3">
                            <template x-if="footer">
                                <p class="whitespace-pre-line" :class="paper_size == '80' ? 'text-sm' : 'text-xs'" x-text="footer"></p>
                            </template>
                            <template x-if="!footer">
                                <p class="whitespace-pre-line opacity-50 italic" :class="paper_size == '80' ? 'text-sm' : 'text-xs'">[Menggunakan Pengaturan Pusat]<br><span x-text="globalFooter"></span></p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
