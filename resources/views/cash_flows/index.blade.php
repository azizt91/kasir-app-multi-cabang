@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between pb-6 border-b-2 border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">💸 Arus Kas (Manual)</h1>
                <p class="text-gray-600 mt-1">Catat modal masuk atau penyesuaian kas lainnya.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('cash-flows.create') }}" class="w-full sm:w-auto flex items-center justify-center px-5 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-md transition-transform transform hover:scale-105 duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Arus Kas Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8">
            @if($cashFlows->isEmpty())
                <div class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Data Arus Kas</h3>
                    <p class="mt-1 text-sm text-gray-500">Gunakan tombol di atas untuk mencatat mutasi kas pertama Anda.</p>
                </div>
            @else
                <div class="hidden sm:block bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah (Rp)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cashFlows as $cf)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($cf->date)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($cf->type === 'in')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Uang Masuk
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Uang Keluar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                            {{ $cf->category }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                                            {{ $cf->note ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $cf->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                            Rp {{ number_format($cf->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $cf->user->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('cash-flows.edit', $cf) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('cash-flows.destroy', $cf) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
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
                    @foreach($cashFlows as $cf)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cf->date)->isoFormat('D MMM YYYY') }}</span>
                                    <div class="mt-1 font-bold text-gray-900">{{ $cf->category }}</div>
                                </div>
                                <span class="text-sm font-bold {{ $cf->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($cf->amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $cf->note ?? '-' }}
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="text-xs font-medium {{ $cf->type === 'in' ? 'text-green-800' : 'text-red-800' }}">
                                    {{ $cf->type === 'in' ? 'Uang Masuk' : 'Uang Keluar' }}
                                </span>
                                <div class="flex space-x-4 text-xs font-medium">
                                    <a href="{{ route('cash-flows.edit', $cf) }}" class="text-indigo-600">Edit</a>
                                    <form action="{{ route('cash-flows.destroy', $cf) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 px-2">
                    {{ $cashFlows->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
