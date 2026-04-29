@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 border-b-2 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">🔄 Transfer Stok Baru</h1>
            <p class="text-gray-600 mt-1">Pindahkan stok produk dari satu gudang ke gudang lain.</p>
        </div>

        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        @endif

        <form action="{{ route('stock-transfers.store') }}" method="POST" class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-6">
            @csrf

            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">Produk <span class="text-red-500">*</span></label>
                <select name="product_id" id="product_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="checkStock()">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (Total Stok: {{ number_format($product->total_stock, 0) }})
                        </option>
                    @endforeach
                </select>
                @error('product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="from_warehouse_id" class="block text-sm font-medium text-gray-700">Dari Gudang <span class="text-red-500">*</span></label>
                    <select name="from_warehouse_id" id="from_warehouse_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="checkStock()">
                        <option value="">-- Gudang Asal --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->branch->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('from_warehouse_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p id="source-stock-info" class="mt-1 text-sm text-gray-500"></p>
                </div>

                <div>
                    <label for="to_warehouse_id" class="block text-sm font-medium text-gray-700">Ke Gudang <span class="text-red-500">*</span></label>
                    <select name="to_warehouse_id" id="to_warehouse_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Gudang Tujuan --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->branch->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('to_warehouse_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="0.01" step="0.01" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('stock-transfers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">Transfer Stok</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkStock() {
    const productId = document.getElementById('product_id').value;
    const warehouseId = document.getElementById('from_warehouse_id').value;
    const info = document.getElementById('source-stock-info');
    if (!productId || !warehouseId) { info.textContent = ''; return; }
    fetch(`{{ route('stock-transfers.get-stock') }}?product_id=${productId}&warehouse_id=${warehouseId}`)
        .then(r => r.json())
        .then(data => { info.textContent = `Stok tersedia: ${data.stock}`; info.className = data.stock > 0 ? 'mt-1 text-sm text-green-600 font-medium' : 'mt-1 text-sm text-red-600 font-medium'; })
        .catch(() => { info.textContent = ''; });
}
</script>
@endpush
