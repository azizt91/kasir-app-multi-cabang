@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">🛒 Catat Pembelian Stok</h1>
            <p class="text-gray-600 mt-1">Tambah stok barang baru atau restock barang lama.</p>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" class="space-y-6">
            @csrf
            
            {{-- Header Card --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    {{-- Transaction Code --}}
                    <div>
                        <label for="transaction_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Transaksi</label>
                        <input type="text" name="transaction_code" id="transaction_code" value="{{ $transactionCode }}" readonly
                               class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 shadow-sm cursor-not-allowed">
                    </div>

                    {{-- Supplier --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier (Opsional)</label>
                        <div class="relative">
                            <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm appearance-none">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="note" id="note" placeholder="Contoh: Titipan gudang"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Items Card --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Barang</h2>
                    <button type="button" onclick="addItemRow()" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors">
                        + Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-5/12">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Harga Beli Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Subtotal</th>
                                <th class="px-4 py-3 text-center w-1/12"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="itemsContainer">
                            {{-- Rows will be added here by JS --}}
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">Total Pembelian:</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700" id="grandTotal">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('purchases.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Simpan Pembelian
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Product List Data for JS --}}
<script>
    const PRODUCTS = @json($products->map(function($p){
        return ['id' => $p->id, 'name' => $p->name, 'purchase_price' => $p->purchase_price];
    }));
</script>

@push('scripts')
<script>
    let rowIndex = 0;

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
    }

    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const rowId = `row-${rowIndex}`;
        
        // Generate Options for Select
        let productOptions = '<option value="">-- Pilih Produk --</option>';
        PRODUCTS.forEach(p => {
            productOptions += `<option value="${p.id}" data-price="${p.purchase_price}">${p.name}</option>`;
        });

        const tr = document.createElement('tr');
        tr.id = rowId;
        tr.className = 'hover:bg-gray-50 transition-colors';
        tr.innerHTML = `
            <td class="px-4 py-3">
                <select name="items[${rowIndex}][product_id]" class="product-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="updatePrice(this, '${rowId}')" required>
                    ${productOptions}
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${rowIndex}][quantity]" class="qty-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="1" min="1" oninput="calculateRow('${rowId}')" required>
            </td>
            <td class="px-4 py-3">
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-xs">Rp</span>
                    </div>
                    <input type="number" name="items[${rowIndex}][unit_cost]" class="price-input w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="0" min="0" oninput="calculateRow('${rowId}')" required>
                </div>
            </td>
            <td class="px-4 py-3 text-right font-medium text-gray-900 subtotal-display" id="subtotal-${rowId}">
                Rp 0
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow('${rowId}')" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;
        
        container.appendChild(tr);
        rowIndex++;
    }

    function removeRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            calculateGrandTotal();
        }
    }

    function updatePrice(selectElement, rowId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        
        // Find price input in the same row
        const row = document.getElementById(rowId);
        const priceInput = row.querySelector('.price-input');
        priceInput.value = parseInt(price);
        
        calculateRow(rowId);
    }

    function calculateRow(rowId) {
        const row = document.getElementById(rowId);
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.subtotal-display').innerText = formatRupiah(subtotal);
        row.querySelector('.subtotal-display').setAttribute('data-val', subtotal);
        
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-display').forEach(el => {
            total += parseFloat(el.getAttribute('data-val') || 0);
        });
        document.getElementById('grandTotal').innerText = formatRupiah(total);
    }

    // Add initial row
    document.addEventListener('DOMContentLoaded', () => {
        addItemRow();
    });
</script>
@endpush
@endsection
