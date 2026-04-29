@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Buttons --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('transactions.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 transition">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Riwayat
            </a>
            
            <div class="flex space-x-3">
                @if($transaction->status !== 'cancelled')
                    <button type="button" onclick="confirmVoid()" class="flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 border border-red-200 transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Batalkan Transaksi
                    </button>
                    <form id="void-form" action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @else
                    <span class="flex items-center px-4 py-2 bg-gray-100 text-gray-500 rounded-lg border border-gray-200 font-medium cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Transaksi Dibatalkan
                    </span>
                @endif
            </div>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Nota #{{ $transaction->transaction_code }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $transaction->created_at->isoFormat('dddd, D MMMM YYYY • HH:mm') }}</p>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-gray-500 uppercase font-semibold">Kasir</span>
                    <span class="block text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'Unknown' }}</span>
                </div>
            </div>

            {{-- items list --}}
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm text-left min-w-[500px]">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-y border-gray-100">
                        <tr>
                            <th class="py-3 px-2">Produk</th>
                            <th class="py-3 px-2 text-center">Qty</th>
                            <th class="py-3 px-2 text-right">Harga</th>
                            <th class="py-3 px-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaction->items as $item)
                        <tr>
                            <td class="py-3 px-2">
                                <div class="font-medium text-gray-900">{{ $item->product_name ?? ($item->product->name ?? 'Produk Dihapus') }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product->barcode ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-2 text-center font-medium">{{ (float)$item->quantity }}</td>
                            <td class="py-3 px-2 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <div class="w-full sm:w-1/2 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($transaction->discount > 0)
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if($transaction->tax > 0)
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Pajak</span>
                            <span>+ Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-300 pt-2 flex justify-between text-lg font-bold text-gray-900 mt-2">
                            <span>Total</span>
                            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 text-xs uppercase">Metode Pembayaran</span>
                        <span class="block font-medium capitalize text-gray-900 mt-1">{{ $transaction->payment_method }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs uppercase">Bayar / Kembali</span>
                        <span class="block text-gray-900 mt-1">
                            Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }} / 
                            <span class="text-green-600">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </span>
                    </div>
                </div>

                @if($transaction->note)
                <div class="mt-4 pt-4 border-t border-gray-200">
                     <span class="block text-gray-500 text-xs uppercase">Catatan Transaksi</span>
                     <p class="text-gray-700 italic mt-1 bg-yellow-50 p-2 rounded border border-yellow-200 text-sm">
                        "{{ $transaction->note }}"
                     </p>
                </div>
                @endif
            </div>

            @if($transaction->status === 'cancelled')
            <div class="bg-red-50 px-6 py-3 border-t border-red-200 text-center">
                <p class="text-red-700 font-bold text-sm">🚫 Transaksi ini telah dibatalkan. Stok produk telah dikembalikan.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmVoid() {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Stok produk akan dikembalikan secara otomatis. Aksi ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('void-form').submit();
            }
        })
    }
</script>
@endpush
@endsection
