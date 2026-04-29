@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between pb-6 border-b-2 border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📦 Pembelian Stok (Kulakan)</h1>
                <p class="text-gray-600 mt-1">Riwayat belanja barang dagangan.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('purchases.create') }}" class="w-full sm:w-auto flex items-center justify-center px-5 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-md transition-transform transform hover:scale-105 duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Catat Pembelian Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8">
            @if($purchases->isEmpty())
                <div class="text-center py-20 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Pembelian</h3>
                    <p class="mt-1 text-sm text-gray-500">Catat pembelian stok pertama Anda untuk menambah stok barang.</p>
                </div>
            @else
                <div class="hidden sm:block bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode TRX</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dicatat Oleh</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchases as $purchase)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $purchase->transaction_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $purchase->supplier->name ?? 'Umum/Lainnya' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <ul class="list-disc list-inside">
                                            @foreach($purchase->items->take(2) as $item)
                                                <li>{{ $item->product->name }} ({{ $item->quantity }})</li>
                                            @endforeach
                                            @if($purchase->items->count() > 2)
                                                <li class="italic text-xs text-gray-400">+ {{ $purchase->items->count() - 2 }} lainnya</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $purchase->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button type="button" onclick="confirmEditPurchase('{{ route('purchases.edit', $purchase) }}', '{{ $purchase->transaction_code }}')" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" id="delete-form-{{ $purchase->id }}" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDeletePurchase({{ $purchase->id }}, '{{ $purchase->transaction_code }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden grid grid-cols-1 gap-4">
                    @foreach($purchases as $purchase)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-indigo-600">{{ $purchase->transaction_code }}</span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Supplier: {{ $purchase->supplier->name ?? 'Umum/Lainnya' }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="bg-gray-50 p-2 rounded-lg text-xs text-gray-600">
                                <ul class="list-disc list-inside">
                                    @foreach($purchase->items->take(2) as $item)
                                        <li>{{ $item->product->name }} ({{ $item->quantity }})</li>
                                    @endforeach
                                    @if($purchase->items->count() > 2)
                                        <li class="italic text-xs text-gray-400">+ {{ $purchase->items->count() - 2 }} lainnya</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <div class="text-xs text-gray-500">
                                    Oleh: {{ $purchase->user->name ?? '-' }}
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" onclick="confirmEditPurchase('{{ route('purchases.edit', $purchase) }}', '{{ $purchase->transaction_code }}')" class="text-indigo-600 text-xs font-medium hover:text-indigo-800">Edit</button>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" id="delete-form-{{ $purchase->id }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDeletePurchase({{ $purchase->id }}, '{{ $purchase->transaction_code }}')" class="text-red-600 text-xs font-medium hover:text-red-800">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 px-2">
                     {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeletePurchase(purchaseId, transactionCode) {
        Swal.fire({
            title: 'Hapus Pembelian?',
            html: `Transaksi <strong>${transactionCode}</strong> akan dihapus.<br><br>
                   <div class="text-left text-sm bg-red-50 p-3 rounded-md text-red-700">
                       <strong>Dampak Penghapusan:</strong><br>
                       • Stok barang yang dibeli di transaksi ini akan <strong>DIKURANGI/DITARIK</strong>.<br>
                       • Riwayat stok akan mencatat "Hapus Pembelian".
                   </div><br>
                   Pastikan sisa stok fisik mencukupi!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Saja!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + purchaseId).submit();
            }
        });
    }

    function confirmEditPurchase(url, transactionCode) {
        Swal.fire({
            title: 'Edit Pembelian?',
            html: `Anda akan mengedit transaksi <strong>${transactionCode}</strong>.<br><br>
                   <div class="text-left text-sm bg-yellow-50 p-3 rounded-md text-yellow-700">
                       <strong>Sistem Edit:</strong><br>
                       1. Stok lama dari transaksi ini akan <strong>DIKURANGI</strong> dulu.<br>
                       2. Lalu, stok baru sesuai hasil edit akan <strong>DITAMBAHKAN</strong>.<br>
                   </div><br>
                   Lanjutkan edit?`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjut Edit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
@endpush
@endsection
