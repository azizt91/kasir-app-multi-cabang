@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 border-b-2 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">📋 Detail Transfer</h1>
            <p class="text-gray-600 mt-1">{{ $stockTransfer->transfer_code }}</p>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-500 uppercase">Kode Transfer</p><p class="font-mono font-bold text-indigo-600">{{ $stockTransfer->transfer_code }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">Status</p>
                    @if($stockTransfer->status === 'completed')<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>@endif
                </div>
                <div><p class="text-xs text-gray-500 uppercase">Produk</p><p class="font-medium">{{ $stockTransfer->product->name ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">Jumlah</p><p class="font-bold text-lg">{{ number_format($stockTransfer->quantity, 0) }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">Dari Gudang</p><p class="font-medium">{{ $stockTransfer->fromWarehouse->name ?? '-' }}</p><p class="text-xs text-gray-400">{{ $stockTransfer->fromWarehouse->branch->name ?? '' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">Ke Gudang</p><p class="font-medium">{{ $stockTransfer->toWarehouse->name ?? '-' }}</p><p class="text-xs text-gray-400">{{ $stockTransfer->toWarehouse->branch->name ?? '' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">User</p><p>{{ $stockTransfer->user->name ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase">Tanggal</p><p>{{ $stockTransfer->created_at->isoFormat('D MMMM YYYY HH:mm') }}</p></div>
            </div>
            @if($stockTransfer->notes)
                <div class="pt-4 border-t"><p class="text-xs text-gray-500 uppercase">Catatan</p><p class="text-sm">{{ $stockTransfer->notes }}</p></div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('stock-transfers.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">← Kembali ke daftar transfer</a>
        </div>
    </div>
</div>
@endsection
