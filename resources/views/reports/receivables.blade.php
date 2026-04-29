@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">📝 Laporan Piutang</h1>
                    <p class="text-gray-600 mt-1 text-sm">Daftar transaksi dengan metode pembayaran utang</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route('reports.receivables') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <div class="w-full sm:flex-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="w-full sm:flex-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Total Piutang Card -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Piutang</p>
                            <p class="text-2xl sm:text-3xl font-bold mt-1">Rp {{ number_format($summary['total_receivables'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Jumlah Transaksi Card -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm font-medium">Jumlah Transaksi</p>
                            <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $summary['total_transactions'] }} transaksi</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Daftar Piutang</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Semua Waktu' }} {{ $endDate ? ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y') : '' }}
                    </p>
                    
                    <!-- Desktop Table -->
                    <div class="overflow-x-auto hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Transaksi</th> -->
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $transaction->transaction_code }}
                                        </td> -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                👤 {{ $transaction->customer_name ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <ul class="list-disc list-inside">
                                                @foreach($transaction->items as $item)
                                                    <li>{{ $item->product->name }} ({{ (float)$item->quantity }}x)</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <form action="{{ route('reports.receivables.paid', $transaction) }}" method="POST" 
                                                  onsubmit="return confirm('Tandai piutang {{ $transaction->customer_name ?? 'Umum' }} sebagai lunas?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Tandai Lunas
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada piutang</h3>
                                            <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi piutang pada periode yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination (Desktop) -->
                    <div class="px-6 pb-6 mt-4 hidden md:block">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @forelse($transactions as $transaction)
                            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                                <!-- Card Header -->
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-bold text-white text-sm">{{ $transaction->transaction_code }}</p>
                                            <p class="text-orange-100 text-xs">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 text-white">
                                            Utang
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="p-4">
                                    <!-- Customer Name -->
                                    <div class="flex items-center mb-3 pb-3 border-b border-gray-200">
                                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-orange-600 text-lg">👤</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Nama Customer</p>
                                            <p class="font-semibold text-gray-900">{{ $transaction->customer_name ?? 'Umum' }}</p>
                                        </div>
                                    </div>

                                    <!-- Amount -->
                                    <div class="flex justify-between items-center mb-3">
                                        <p class="text-sm text-gray-500">Total Piutang</p>
                                        <p class="text-xl font-bold text-orange-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    
                                    <!-- Details -->
                                    <div class="text-sm space-y-2 text-gray-600 mb-4">
                                        <div class="flex justify-between">
                                            <p>Kasir:</p> 
                                            <p class="font-medium text-gray-900">{{ $transaction->user->name }}</p>
                                        </div>
                                        <div>
                                            <p class="font-medium mb-1">Items:</p>
                                            <ul class="list-disc list-inside text-xs bg-gray-50 rounded-lg p-2">
                                                @foreach($transaction->items as $item)
                                                    <li>{{ $item->product->name }} ({{ (float)$item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <form action="{{ route('reports.receivables.paid', $transaction) }}" method="POST" 
                                          onsubmit="return confirm('Tandai piutang {{ $transaction->customer_name ?? 'Umum' }} sebagai lunas?')">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-sm font-bold rounded-lg transition-all duration-200 shadow-md">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Tandai Lunas
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 bg-gray-50 rounded-xl">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">🎉 Tidak ada piutang</h3>
                                <p class="mt-2 text-sm text-gray-500">Tidak ada transaksi piutang pada periode yang dipilih.</p>
                            </div>
                        @endforelse
                    </div>
                    <!-- Pagination (Mobile) -->
                    <div class="md:hidden mt-4">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
