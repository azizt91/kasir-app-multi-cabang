@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Laporan Penjualan</h1>
                    <p class="text-gray-600 mt-1">Analisis transaksi dan pendapatan</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-end gap-4">
                        <div class="w-full sm:flex-1 min-w-48">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="w-full sm:flex-1 min-w-48">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" 
                                    class="w-full justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Export Laporan</h3>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <a href="{{ route('reports.sales', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                            <a href="{{ route('reports.sales', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Excel
                            </a>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4">
                <!-- Total Sales -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Penjualan</dt>
                                <dd class="text-lg font-bold text-gray-900">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</dd>
                                <dd class="text-xs text-blue-600 mt-1">
                                    Tunai: Rp {{ number_format($summary['total_received'], 0, ',', '.') }} <br>
                                    Piutang: Rp {{ number_format($summary['total_receivables'], 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Beban Operasional</dt>
                                <dd class="text-lg font-bold text-red-600">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Purchases -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pembelian Stok</dt>
                                <dd class="text-lg font-bold text-orange-600">Rp {{ number_format($summary['total_purchases'], 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Net Income -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 {{ $summary['net_income'] >= 0 ? 'border-green-500' : 'border-red-600' }}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 {{ $summary['net_income'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-md p-3">
                            <svg class="h-6 w-6 {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Laba Bersih</dt>
                                <dd class="text-lg font-bold {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($summary['net_income'], 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Cards -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Transaksi</h3>
                    <div class="md:hidden space-y-4">
                        @forelse($transactions as $transaction)
                            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $transaction->transaction_code }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($transaction->payment_method === 'cash') bg-green-100 text-green-800
                                        @elseif($transaction->payment_method === 'card') bg-blue-100 text-blue-800
                                        @elseif($transaction->payment_method === 'ewallet') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($transaction->payment_method) }}
                                    </span>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-sm space-y-1 text-gray-600">
                                        @if($transaction->note)
                                            <div class="bg-gray-50 p-2 rounded text-xs mb-2">
                                                <span class="font-semibold">Ket:</span> {{ $transaction->note }}
                                            </div>
                                        @endif
                                        <div class="flex justify-between mt-2 pt-2"><p>Kasir:</p> <p class="font-medium">{{ $transaction->user->name }}</p></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
                                <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi pada periode yang dipilih.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table -->
                    <div class="overflow-x-auto hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 {{ $transaction->status == 'cancelled' ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $transaction->note ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $transaction->status == 'cancelled' ? 'text-gray-400 line-through' : 'text-green-600' }}">
                                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaction->status == 'cancelled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    DIBATALKAN
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($transaction->payment_method === 'cash') bg-green-100 text-green-800
                                                    @elseif($transaction->payment_method === 'card') bg-blue-100 text-blue-800
                                                    @elseif($transaction->payment_method === 'ewallet') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($transaction->payment_method) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
                                            <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi pada periode yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @forelse($transactions as $transaction)
                            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $transaction->transaction_code }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($transaction->payment_method === 'cash') bg-green-100 text-green-800
                                        @elseif($transaction->payment_method === 'card') bg-blue-100 text-blue-800
                                        @elseif($transaction->payment_method === 'ewallet') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($transaction->payment_method) }}
                                    </span>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-sm space-y-1 text-gray-600">
                                        <div class="flex justify-between"><p>Subtotal:</p> <p>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between"><p>Diskon:</p> <p class="text-orange-600">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between"><p>Pajak:</p> <p>+ Rp {{ number_format($transaction->tax, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between border-t mt-2 pt-2"><p>Kasir:</p> <p class="font-medium">{{ $transaction->user->name }}</p></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
                                <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi pada periode yang dipilih.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @forelse($transactions as $transaction)
                            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 {{ $transaction->status == 'cancelled' ? 'bg-red-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-900 {{ $transaction->status == 'cancelled' ? 'line-through text-gray-500' : '' }}">{{ $transaction->transaction_code }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    @if($transaction->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            DIBATALKAN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($transaction->payment_method === 'cash') bg-green-100 text-green-800
                                            @elseif($transaction->payment_method === 'card') bg-blue-100 text-blue-800
                                            @elseif($transaction->payment_method === 'ewallet') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($transaction->payment_method) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="text-lg font-bold {{ $transaction->status == 'cancelled' ? 'text-gray-400 line-through' : 'text-green-600' }}">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-sm space-y-1 text-gray-600">
                                        <div class="flex justify-between"><p>Subtotal:</p> <p>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between"><p>Diskon:</p> <p class="text-orange-600">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between"><p>Pajak:</p> <p>+ Rp {{ number_format($transaction->tax, 0, ',', '.') }}</p></div>
                                        <div class="flex justify-between border-t mt-2 pt-2"><p>Kasir:</p> <p class="font-medium">{{ $transaction->user->name }}</p></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
                                <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi pada periode yang dipilih.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <!-- Pagination -->
                <div class="px-6 pb-6 mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Operational Expenses Details -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-red-600">🔻 Rincian Pengeluaran Operasional</h3>
                    
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dicatat Oleh</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($expenses as $expense)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $expense->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $expense->user->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600">
                                            Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pengeluaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                     <!-- Mobile Cards for Expenses -->
                    <div class="md:hidden space-y-4">
                        @forelse($expenses as $expense)
                            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium uppercase">{{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}</p>
                                        <h4 class="text-sm font-bold text-gray-900 mt-1">{{ $expense->description }}</h4>
                                    </div>
                                    <span class="text-sm font-bold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                    Dicatat: {{ $expense->user->name ?? '-' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500 py-4">Tidak ada data pengeluaran.</p>
                        @endforelse
                    </div>

                </div>
                <!-- Pagination -->
                <div class="px-6 pb-6 mt-4">
                    {{ $expenses->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Stock Purchases Details -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-orange-600">📦 Rincian Pembelian Stok</h3>
                    
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($purchases as $purchase)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <ul class="list-disc list-inside">
                                                @foreach($purchase->items as $item)
                                                    <li>{{ $item->product->name ?? 'Produk Dihapus' }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <ul class="list-none">
                                                @foreach($purchase->items as $item)
                                                    <li>{{ $item->quantity }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $purchase->supplier->name ?? 'Umum' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $purchase->note ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-orange-600">
                                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pembelian stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                     <!-- Mobile Cards for Purchases -->
                    <div class="md:hidden space-y-4">
                        @forelse($purchases as $purchase)
                            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($purchase->date)->isoFormat('D MMM YYYY') }}</span>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 mb-1">Items:</div>
                                        <ul class="list-disc list-inside text-sm text-gray-600 mb-2">
                                            @foreach($purchase->items as $item)
                                                <li>{{ $item->product->name ?? 'Produk Dihapus' }} (x{{ $item->quantity }})</li>
                                            @endforeach
                                        </ul>
                                        <p class="text-xs text-gray-500 mt-1">Supplier: {{ $purchase->supplier->name ?? 'Umum' }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-orange-600">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                                </div>
                                @if($purchase->note)
                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded mb-2">
                                        Note: {{ $purchase->note }}
                                    </div>
                                @endif
                                <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                    Dicatat: {{ $purchase->user->name ?? '-' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500 py-4">Tidak ada data pembelian stok.</p>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
        <!-- Pagination -->
        <div class="px-6 pb-6 mt-4">
            {{ $purchases->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</div>
@endsection
