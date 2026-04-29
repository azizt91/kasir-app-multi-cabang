@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between pb-6 border-b-2 border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🔄 Transfer Stok</h1>
                <p class="text-gray-600 mt-1">Riwayat perpindahan stok antar gudang.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('stock-transfers.create') }}" class="w-full sm:w-auto flex items-center justify-center px-5 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-md transition-transform transform hover:scale-105 duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Transfer Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="mt-8">
            @if($transfers->isEmpty())
                <div class="text-center py-20 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Transfer</h3>
                    <p class="mt-1 text-sm text-gray-500">Lakukan transfer stok pertama antar gudang.</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dari Gudang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ke Gudang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($transfers as $transfer)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-indigo-600">{{ $transfer->transfer_code }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $transfer->product->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $transfer->fromWarehouse->name ?? '-' }}
                                        @if($transfer->fromWarehouse && $transfer->fromWarehouse->branch)
                                            <span class="block text-xs text-gray-400">{{ $transfer->fromWarehouse->branch->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $transfer->toWarehouse->name ?? '-' }}
                                        @if($transfer->toWarehouse && $transfer->toWarehouse->branch)
                                            <span class="block text-xs text-gray-400">{{ $transfer->toWarehouse->branch->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-900">{{ number_format($transfer->quantity, 0) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($transfer->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                        @elseif($transfer->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Batal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->created_at->isoFormat('D MMM YYYY HH:mm') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">{{ $transfers->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
