@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between pb-6 border-b-2 border-gray-200">
            <div>

                <h1 class="text-3xl font-bold text-gray-900">💸 Pengeluaran Operasional</h1>
                <p class="text-gray-600 mt-1">Daftar biaya operasional toko.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('expenses.create') }}" class="w-full sm:w-auto flex items-center justify-center px-5 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-md transition-transform transform hover:scale-105 duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Catat Pengeluaran
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8">
            @if($expenses->isEmpty())
                <div class="text-center py-20 bg-white rounded-lg shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Pengeluaran</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan mencatat pengeluaran pertama Anda.</p>
                </div>
            @else
                <div class="hidden sm:block bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah (Rp)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dicatat Oleh</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($expenses as $expense)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $expense->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('expenses.edit', $expense) }}" class="text-gray-500 hover:text-indigo-600" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" id="delete-form-{{ $expense->id }}" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDeleteExpense({{ $expense->id }}, '{{ addslashes($expense->description) }}')" class="text-red-600 hover:text-red-900" title="Hapus">
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
                    @foreach($expenses as $expense)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase">{{ \Carbon\Carbon::parse($expense->date)->isoFormat('D MMM YYYY') }}</p>
                                    <h3 class="text-sm font-bold text-gray-900 mt-1">{{ $expense->description }}</h3>
                                </div>
                                <span class="text-sm font-bold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-2">
                                <div class="text-xs text-gray-500">
                                    Oleh: {{ $expense->user->name ?? '-' }}
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 text-xs font-medium hover:text-indigo-800">Edit</a>
                                    <button type="button" onclick="confirmDeleteExpense({{ $expense->id }}, '{{ addslashes($expense->description) }}')" class="text-red-600 text-xs font-medium hover:text-red-800">Hapus</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteExpense(expenseId, expenseName) {
        Swal.fire({
            title: `Hapus pengeluaran ini?`,
            text: `"${expenseName}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + expenseId).submit();
            }
        });
    }
</script>
@endpush
