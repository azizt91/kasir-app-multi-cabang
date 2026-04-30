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
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex flex-wrap items-center gap-2">
                        <span>📊 Laporan Penjualan</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            {{ auth()->user()->getActiveBranchName() }}
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-1">Analisis transaksi dan pendapatan</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <form action="{{ route('reports.sales') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex-none flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Filter
                        </button>
                        <a href="{{ route('reports.sales', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150">
                            PDF
                        </a>
                        <a href="{{ route('reports.sales', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition ease-in-out duration-150">
                            Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $summary['total_transactions'] }} Transaksi</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total Diterima</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($summary['total_received'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Tunai / Transfer / Qris</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total Piutang</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">Rp {{ number_format($summary['total_receivables'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Belum Lunas</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Laba/Rugi Kotor</p>
                <p class="text-2xl font-bold {{ $summary['net_income'] >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-1">
                    Rp {{ number_format($summary['net_income'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Setelah HPP & Biaya</p>
            </div>
        </div>

        <!-- Details Tab -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px px-6" aria-label="Tabs">
                    <button class="w-1/3 py-4 px-1 text-center border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                        Transaksi Penjualan
                    </button>
                    <button class="w-1/3 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Biaya Operasional
                    </button>
                    <button class="w-1/3 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Pembelian Stok
                    </button>
                </nav>
            </div>

            <!-- Transactions Table -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Nota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Potongan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                                <tr class="{{ $transaction->status == 'void' ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $transaction->transaction_code }}
                                        @if($transaction->status == 'void')
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Void</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                        {{ $transaction->payment_method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                        {{ number_format($transaction->discount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                        {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada transaksi ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
