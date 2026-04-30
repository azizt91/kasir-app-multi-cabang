@extends('layouts.app')

@section('header')
<div class="flex justify-between items-center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Audit Trail (Log Aktivitas)') }}
    </h2>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Filter Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <form action="{{ route('reports.activity_logs') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    
                    <div class="flex-1 w-full">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Filter User</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->branch ? $user->branch->name : 'Pusat' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 w-full">
                        <label for="action" class="block text-sm font-medium text-gray-700">Tipe Aktivitas</label>
                        <select name="action" id="action" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Semua Aktivitas</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ $action }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow-sm transition-colors">
                            Filter Data
                        </button>
                        <a href="{{ route('reports.activity_logs') }}" class="ml-2 text-gray-500 hover:text-gray-700">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if($logs->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        Belum ada log aktivitas yang tercatat.
                    </div>
                @else
                    <div class="relative border-l border-gray-200 ml-3">
                        @foreach($logs as $log)
                            @php
                                // Tentukan warna ikon berdasarkan aksi
                                $colorClass = 'bg-gray-500';
                                if (in_array($log->action, ['Create', 'Approve Adjustment', 'Approve'])) {
                                    $colorClass = 'bg-emerald-500';
                                } elseif (in_array($log->action, ['Update'])) {
                                    $colorClass = 'bg-amber-500';
                                } elseif (in_array($log->action, ['Delete', 'Reject Adjustment', 'Reject'])) {
                                    $colorClass = 'bg-red-500';
                                } elseif (in_array($log->action, ['Login'])) {
                                    $colorClass = 'bg-blue-500';
                                } elseif (in_array($log->action, ['Logout'])) {
                                    $colorClass = 'bg-slate-400';
                                }
                            @endphp
                            <div class="mb-8 ml-10">
                                <span class="absolute flex items-center justify-center w-4 h-4 {{ $colorClass }} rounded-full -left-2 ring-4 ring-white"></span>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900">
                                            {{ $log->user ? $log->user->name : 'System' }}
                                            @if($log->branch)
                                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded ml-3">{{ $log->branch->name }}</span>
                                            @else
                                                <span class="bg-slate-100 text-slate-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded ml-3">Pusat</span>
                                            @endif
                                        </h3>
                                        <div class="text-sm font-bold text-gray-600 mb-1">
                                            [{{ $log->action }}]
                                        </div>
                                        <p class="mb-1 text-base font-normal text-gray-600 whitespace-pre-wrap">{{ $log->description }}</p>
                                        <p class="text-xs text-gray-400">
                                            IP: {{ $log->ip_address ?? 'N/A' }} | User Agent: {{ Str::limit($log->user_agent, 40) }}
                                        </p>
                                    </div>
                                    <div class="text-sm font-normal text-gray-400 whitespace-nowrap text-right">
                                        {{ $log->created_at->format('d M Y, H:i:s') }}
                                        <div class="text-xs mt-1">{{ $log->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
