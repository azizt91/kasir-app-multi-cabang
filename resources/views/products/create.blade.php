@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="productForm()">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center pb-6 border-b-2 border-gray-200 mb-8">
            <a href="{{ route('products.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Produk Baru</h1>
                <p class="text-gray-600 mt-1">Lengkapi detail produk di bawah ini.</p>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="handleSubmit($event)">
            @csrf
            
            {{-- Hidden Input for Variant Mode --}}
            <input type="hidden" name="has_variants" :value="hasVariants ? 1 : 0">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Image --}}
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 sticky top-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                        <div class="aspect-square bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group">
                            <div id="image-preview" class="hidden w-full h-full"><img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover"></div>
                            <div id="upload-placeholder" class="text-center p-4 transition-opacity group-hover:opacity-75">
                                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm text-gray-600 mt-2">Klik untuk upload</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 2MB</p>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                        </div>
                        @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Right Column: Details --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Basic Information --}}
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-3">Informasi Utama</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Kemeja Flannel" required>
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Pilih kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                
                                {{-- Product Type Toggle --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk</label>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" class="form-radio text-indigo-600" name="product_type" value="single" @click="setSingle()" :checked="!hasVariants">
                                            <span class="ml-2 text-gray-700">Satuan (Single)</span>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" class="form-radio text-indigo-600" name="product_type" value="variant" @click="setVariant()" :checked="hasVariants">
                                            <span class="ml-2 text-gray-700">Varian (Warna/Ukuran)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SINGLE MODE FIELDS --}}
                    <div x-show="!hasVariants" x-transition class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-3">Detail Harga & Stok</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode / SKU</label>
                                    <div class="flex w-full min-w-0">
                                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" class="flex-grow min-w-0 border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 rounded-r-none text-sm" placeholder="Scan/Ketik Manual...">
                                        <button type="button" onclick="openScannerModal()" class="flex-shrink-0 inline-flex items-center px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-200 transition-colors duration-200">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span class="ml-2 text-sm font-medium text-gray-700 hidden sm:inline">Scan</span>
                                        </button>
                                    </div>
                                @error('barcode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli <span class="text-red-500">*</span></label>
                                <div class="relative"><span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span><input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :required="!hasVariants"></div>
                            </div>
                            
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
                                <div class="relative"><span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span><input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :required="!hasVariants"></div>
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok</label>
                                <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 5) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    {{-- VARIANT MODE FIELDS --}}
                    <div x-show="hasVariants" x-transition class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                        <div class="flex items-center justify-between mb-4 border-b pb-3">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Varian</h3>
                            <button type="button" @click="addVariant()" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors border border-indigo-200">
                                + Tambah Varian
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(variant, index) in variants" :key="index">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 group">
                                    <div class="flex gap-4 mb-4">
                                        <div class="flex-grow">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Varian <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'variants['+index+'][variant_name]'" x-model="variant.variant_name" placeholder="Contoh: Merah - XL" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" :required="hasVariants">
                                        </div>
                                        <div class="flex-shrink-0 pt-5">
                                             <button type="button" @click="removeVariant(index)" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Hapus Varian">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Beli</label>
                                            <input type="number" :name="'variants['+index+'][purchase_price]'" x-model="variant.purchase_price" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" :required="hasVariants">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Jual</label>
                                            <input type="number" :name="'variants['+index+'][selling_price]'" x-model="variant.selling_price" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" :required="hasVariants">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Stok</label>
                                            <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" :required="hasVariants">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Min. Stok</label>
                                            <input type="number" :name="'variants['+index+'][minimum_stock]'" x-model="variant.minimum_stock" step="0.01" placeholder="10" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Barcode (Opsional)</label>
                                            <input type="text" :name="'variants['+index+'][barcode]'" x-model="variant.barcode" placeholder="Auto" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="variants.length === 0" class="text-center py-8 bg-dashed border-2 border-gray-200 rounded-lg text-gray-500 text-sm">
                                Belum ada varian. Klik tombol "+ Tambah Varian" untuk memulai.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="px-6 py-2 text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg font-medium transition-colors duration-200">Batal</a>
                <button type="submit" id="submit-btn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors duration-200 shadow-sm flex items-center">
                    <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text">Simpan Produk</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal HTML untuk Barcode Scanner --}}
<div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-auto overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-center">Arahkan Kamera ke Barcode</h3>
        </div>
        <div class="p-4 bg-gray-900">
            <div id="reader" style="width: 100%; border-radius: 0.5rem; overflow: hidden;"></div>
        </div>
        <div class="p-4 bg-gray-50 border-t">
            <button onclick="closeScannerModal()" class="w-full py-3 text-center font-bold text-white bg-red-500 hover:bg-red-600 rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function productForm() {
        return {
            hasVariants: false, // Default Single
            variants: [],
            
            init() {
                // Bisa load old data disini jika ada
            },

            setSingle() {
                this.hasVariants = false;
            },
            
            setVariant() {
                this.hasVariants = true;
                if (this.variants.length === 0) {
                    this.addVariant();
                }
            },
            
            addVariant() {
                this.variants.push({
                    variant_name: '',
                    purchase_price: document.getElementById('purchase_price')?.value || 0,
                    selling_price: document.getElementById('selling_price')?.value || 0,
                    stock: 0,
                    minimum_stock: 10,
                    barcode: ''
                });
            },
            
            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        }
    }

    function handleSubmit(e) {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const text = document.getElementById('btn-text');

        // Simple validation check (custom) can go here
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        spinner.classList.remove('hidden');
        text.textContent = 'Menyimpan...';
        
        // Form submits normally
        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- ZXing Library is usually loaded in Layout or needing a CDN here if not present --}}
<script src="https://unpkg.com/@zxing/library@latest"></script> 

<script>
// Logic Preview Image
function previewImage(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimal ukuran gambar adalah 2MB.'
            });
            input.value = ''; 
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

// Logic Barcode Scanner
let html5QrcodeScanner = null;

function openScannerModal() {
    document.getElementById('scanner-modal').classList.remove('hidden');

    // Cek HTTPS
    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
         Swal.fire({
            icon: 'warning',
            title: 'Koneksi Tidak Aman',
            text: 'Fitur kamera mungkin tidak berfungsi jika tidak menggunakan HTTPS.',
            timer: 3000
        });
    }

    if (html5QrcodeScanner) return;

    html5QrcodeScanner = new Html5Qrcode("reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.QR_CODE
        ]
    };
    
    // Attempt Back Camera
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        (decodedText, decodedResult) => {
            // Success
             playBeep();
             html5QrcodeScanner.pause();
             
             document.getElementById('barcode').value = decodedText;
             closeScannerModal();
             
             Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Barcode Berhasil Discanned',
                timer: 1500,
                showConfirmButton: false
            });
        },
        (error) => {
            // Ignore ongoing errors
        }
    ).catch(err => {
        console.warn("Back camera failed, trying front...", err);
        html5QrcodeScanner.start(
            { facingMode: "user" },
            config,
            (decodedText) => {
                 playBeep();
                 html5QrcodeScanner.pause();
                 document.getElementById('barcode').value = decodedText;
                 closeScannerModal();
                 Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Barcode Berhasil Discanned', timer: 1500, showConfirmButton: false });
            },
            () => {}
        ).catch(err2 => {
            console.error(err2);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuka Scanner',
                text: 'Pastikan izin kamera diberikan.'
            });
            closeScannerModal();
        });
    });
}

function closeScannerModal() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner = null;
            document.getElementById('scanner-modal').classList.add('hidden');
        }).catch(err => {
            html5QrcodeScanner = null;
            document.getElementById('scanner-modal').classList.add('hidden');
        });
    } else {
        document.getElementById('scanner-modal').classList.add('hidden');
    }
}

function playBeep() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        oscillator.frequency.value = 1000;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;
        oscillator.start();
        setTimeout(() => oscillator.stop(), 100);
    } catch (e) {
        console.log('Audio not supported');
    }
}
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endpush
