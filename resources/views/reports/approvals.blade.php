@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <span class="p-3 bg-red-100 rounded-2xl text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Verifikasi Selisih Kas
                </h1>
                <p class="mt-2 text-gray-600">Review dan setujui selisih uang laci saat penutupan shift kasir.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm text-gray-500">Total Pending:</span>
                <span class="ml-1 text-xl font-bold text-red-600">{{ count($pendingAdjustments) }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            @forelse($pendingAdjustments as $adj)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $adj->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $adj->type === 'in' ? 'Uang Lebih (+)' : 'Uang Kurang (-)' }}
                                    </span>
                                    <span class="text-sm text-gray-400">#Shift-{{ $adj->shift_id }}</span>
                                    <span class="text-sm text-gray-400">•</span>
                                    <span class="text-sm text-gray-500 font-medium">{{ $adj->date->isoFormat('D MMMM YYYY') }}</span>
                                </div>
                                
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                    Rp {{ number_format($adj->amount, 0, ',', '.') }}
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div class="flex items-center text-gray-600">
                                        <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase font-bold">Kasir</p>
                                            <p class="font-semibold">{{ $adj->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase font-bold">Cabang</p>
                                            <p class="font-semibold">{{ $adj->branch->name ?? 'Pusat' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100 italic text-gray-600 text-sm">
                                    "{{ $adj->note }}"
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 min-w-[200px]">
                                <form action="{{ route('reports.approve_adjustment', $adj->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Setujui (Approved)
                                    </button>
                                </form>
                                
                                <button type="button" 
                                        onclick="document.getElementById('reject-modal-{{ $adj->id }}').classList.remove('hidden')"
                                        class="w-full px-6 py-3 bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 font-bold rounded-xl transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Tolak (Reject)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="reject-modal-{{ $adj->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form action="{{ route('reports.reject_adjustment', $adj->id) }}" method="POST">
                                @csrf
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 text-red-600" id="modal-title">Alasan Penolakan</h3>
                                    <textarea name="reason" rows="4" required class="w-full rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Berikan alasan mengapa selisih ini ditolak..."></textarea>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700">Tolak Sekarang</button>
                                    <button type="button" 
                                            onclick="document.getElementById('reject-modal-{{ $adj->id }}').classList.add('hidden')"
                                            class="mt-3 w-full sm:w-auto px-6 py-2 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 sm:mt-0">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Semua Terverifikasi!</h3>
                    <p class="text-gray-500 mt-2">Tidak ada selisih kas yang menunggu persetujuan saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
