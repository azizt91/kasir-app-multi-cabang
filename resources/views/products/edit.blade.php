@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="productForm({{ ($product->productGroup && $product->productGroup->has_variants) ? 'true' : 'false' }}, {{ json_encode($variants) }})">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center pb-6 border-b-2 border-gray-200 mb-8">
            <a href="{{ route('products.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Produk</h1>
                <p class="text-gray-600 mt-1">Memperbarui: <span class="font-semibold">{{ $product->productGroup ? $product->productGroup->name : $product->name }}</span></p>
            </div>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" onsubmit="return handleSubmit(this)">
            @csrf
            @method('PUT')
            
            {{-- Hidden Input for has_variants --}}
            <input type="hidden" name="has_variants" x-model="hasVariants">
            {{-- Hidden Input for deleted variant IDs --}}
            <input type="hidden" name="deleted_variant_ids" x-model="deletedVariantIds">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Kolom Kiri - Gambar & Tipe Produk --}}
                <div class="lg:col-span-1 space-y-8">
                    {{-- Tipe Produk Card --}}
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Produk</label>
                        
                        <div x-show="!hasVariants && variants.length === 0">
                            {{-- Jika Single, hanya info statis (tidak bisa ubah ke varian dari edit sini untuk kesederhanaan, kecuali kita mau handle migrasinya) --}}
                            <div class="p-3 bg-blue-50 text-blue-700 rounded-lg text-sm">
                                Produk Satuan (Standar)
                            </div>
                        </div>

                        <div x-show="hasVariants">
                             <div class="p-3 bg-purple-50 text-purple-700 rounded-lg text-sm">
                                Produk Varian (Warna/Ukuran)
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                        <div class="aspect-square bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                            <div id="image-preview" class="{{ $product->image ? '' : 'hidden' }} w-full h-full"><img id="preview-img" src="{{ $product->image ? asset('storage/' . $product->image) : '' }}" alt="Preview" class="w-full h-full object-cover"></div>
                            <div id="upload-placeholder" class="{{ $product->image ? 'hidden' : '' }} text-center p-4">
                                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm text-gray-600 mt-2">Klik untuk ganti</p>
                            </div>
                        </div>
                        <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('image').click()" class="mt-4 w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">Ganti Gambar</button>
                        @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Kolom Kanan - Detail Produk --}}
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-3">Informasi Utama</h3>

                        <div class="space-y-6">
                            {{-- Nama Produk (Parent) --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk (Induk) <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->productGroup ? $product->productGroup->name : $product->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kategori --}}
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Pilih kategori</option>
                                        @foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach
                                    </select>
                                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>

                                {{-- Gudang untuk Edit Stok --}}
                                <div>
                                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Gudang (Untuk Update Stok) <span class="text-red-500">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required @change="updateStockDisplay($event.target.value)">
                                        <option value="">Pilih Gudang</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id || (auth()->user()->getActiveWarehouse() && auth()->user()->getActiveWarehouse()->id == $wh->id))>{{ $wh->name }} ({{ $wh->branch->name ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                             {{-- Deskripsi --}}
                             <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Logic for Single Product --}}
                    <div x-show="!hasVariants" class="bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all duration-300">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-3">Detail Produk Satuan</h3>
                        
                        <div class="space-y-6">
                            {{-- Barcode --}}
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode (SKU)</label>
                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('barcode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli <span class="text-red-500">*</span></label>
                                    <div class="relative"><span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span><input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', (int)$product->purchase_price) }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :required="!hasVariants"></div>
                                </div>
                                <div>
                                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
                                    <div class="relative"><span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span><input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', (int)$product->selling_price) }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :required="!hasVariants"></div>
                                </div>
                                <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini (Edit Manual)</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok</label>
                                <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            </div>
                        </div>
                    </div>

                    {{-- Logic for Variant Product --}}
                    <div x-show="hasVariants" class="bg-white p-6 rounded-xl shadow-md border border-gray-200 transition-all duration-300">
                         <div class="flex items-center justify-between mb-4 border-b pb-3">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Varian (Edit Massal)</h3>
                            <button type="button" @click="addVariant()" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors">
                                + Tambah Varian
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <template x-for="(variant, index) in variants" :key="variant.temp_id || variant.id">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 group">
                                    {{-- Hidden ID for existing variants --}}
                                    <input type="hidden" :name="'variants['+index+'][id]'" x-model="variant.id" x-if="variant.id">

                                    <div class="flex gap-4 mb-4">
                                         <div class="flex-grow">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Varian (Warna/Ukuran) <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'variants['+index+'][variant_name]'" x-model="variant.variant_name" placeholder="Contoh: Merah - XL" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" :required="hasVariants">
                                        </div>
                                         <div class="flex-shrink-0 pt-5">
                                            <button type="button" @click="removeVariant(index)" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Hapus Varian">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Beli</label>
                                            <input type="number" :name="'variants['+index+'][purchase_price]'" x-model="variant.purchase_price" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Jual</label>
                                            <input type="number" :name="'variants['+index+'][selling_price]'" x-model="variant.selling_price" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Stok</label>
                                            <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Min. Stok</label>
                                            <input type="number" :name="'variants['+index+'][minimum_stock]'" x-model="variant.minimum_stock" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Barcode (Opsional)</label>
                                            <input type="text" :name="'variants['+index+'][barcode]'" x-model="variant.barcode" placeholder="Auto" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="px-6 py-2 text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg font-medium transition-colors duration-200">Batal</a>
                <button type="submit" id="submit-btn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors duration-200 shadow-sm flex items-center">
                    <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function productForm(hasVariantsInit, variantsInit) {
        return {
            hasVariants: hasVariantsInit,
            variants: variantsInit || [],
            deletedVariantIds: [],
            
             init() {
                 // Ensure variants have necessary properties
                 this.variants = this.variants.map(v => {
                     return {
                         id: v.id,
                         temp_id: v.id,
                         variant_name: v.variant_name || '',
                         purchase_price: parseInt(v.purchase_price),
                         selling_price: parseInt(v.selling_price),
                         stock: parseInt(v.stock),
                         minimum_stock: v.minimum_stock ? parseInt(v.minimum_stock) : 10,
                         barcode: v.barcode
                     };
                 });
            },

            addVariant() {
                this.variants.push({
                    id: null, // New variant
                    temp_id: 'new_' + Date.now(),
                    variant_name: '',
                    purchase_price: '',
                    selling_price: '',
                    stock: 0,
                    minimum_stock: 10,
                    barcode: ''
                });
            },
            
            removeVariant(index) {
                const variant = this.variants[index];
                if (variant.id) {
                    // Mark for deletion
                    this.deletedVariantIds.push(variant.id);
                }
                this.variants.splice(index, 1);
            },

            updateStockDisplay(warehouseId) {
                // For a fully dynamic UI, we'd fetch stocks via AJAX.
                // For now, reloading the page or handling server-side validation is easier,
                // but we can just let the user know they are editing stock for the selected warehouse.
                console.log('Selected warehouse:', warehouseId);
            }
        }
    }

    function handleSubmit(form) {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const text = document.getElementById('btn-text');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        spinner.classList.remove('hidden');
        text.textContent = 'Menyimpan...';

        return true;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Script JS untuk preview gambar
function previewImage(input) {
    if (input.files && input.files[0]) {
        // Validasi ukuran file (max 2MB)
        if (input.files[0].size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimal ukuran gambar adalah 2MB. Silakan pilih gambar yang lebih kecil.'
            });
            input.value = ''; // Reset input
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
