@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4 px-4 sm:px-0">
            <div class="flex items-center space-x-4">
                <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📖 Buku Kas Umum</h1>
                    <p class="text-gray-600 mt-1">Histori mutasi kas gabungan secara kronologis</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.cash_flow') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <div class="flex-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                            <button type="button" onclick="window.print()" 
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Export Laporan</h3>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <a href="{{ route('reports.cash_flow.pdf', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                            <a href="{{ route('reports.cash_flow.excel', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mx-4 sm:mx-0">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori / Keterangan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider text-green-600">Debit (Masuk)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider text-red-600">Kredit (Keluar)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-bold">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Opening Balance -->
                                <tr class="bg-gray-50 font-medium font-bold italic">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">-</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        SALDO AWAL (Opening Balance)
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">-</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">-</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-bold bg-yellow-50">
                                        Rp {{ number_format($openingBalance, 0, ',', '.') }}
                                    </td>
                                </tr>

                                @php $rowNum = 1; @endphp
                                @forelse($results as $row)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $rowNum++ }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($row->date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="flex flex-col">
                                                <div class="flex items-center space-x-2">
                                                    @if(str_contains($row->category, '[TRX]'))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                                            {{ substr($row->category, 0, 5) }}
                                                        </span>
                                                    @elseif(str_contains($row->category, '[OUT]'))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">
                                                            {{ substr($row->category, 0, 5) }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">
                                                            {{ substr($row->category, 0, 5) }}
                                                        </span>
                                                    @endif
                                                    <span class="font-medium">
                                                        {{ str_replace(['[TRX] ', '[OUT] ', '[KAS] '], '', $row->category) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-0.5 italic">{{ $row->note }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                                            {{ $row->debit > 0 ? 'Rp ' . number_format($row->debit, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-red-600">
                                            {{ $row->kredit > 0 ? 'Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                            Rp {{ number_format($row->balance, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                            Tidak ada mutasi kas pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                        SALDO AKHIR (Closing Balance)
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-indigo-700 bg-indigo-50">
                                        Rp {{ number_format($results ? end($results)->balance ?? $openingBalance : $openingBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .bg-gray-50 { background-color: #f9fafb !important; -webkit-print-color-adjust: exact; }
        .bg-indigo-50 { background-color: #eef2ff !important; -webkit-print-color-adjust: exact; }
        .bg-yellow-50 { background-color: #fefce8 !important; -webkit-print-color-adjust: exact; }
        nav, side-bar, form, button, a[href="{{ route('reports.index') }}"] { display: none !important; }
        .py-8 { padding-top: 0 !important; padding-bottom: 0 !important; }
        .shadow-sm { shadow: none !important; }
        .rounded-lg { border-radius: 0 !important; }
    }
</style>
@endsection
