@extends('layouts.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 px-4 sm:px-0 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <span class="p-2 bg-white rounded-xl shadow-sm border border-gray-100">📊</span>
                    Dashboard POS
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
                        {{ auth()->user()->getActiveBranchName() }}
                    </span>
                </h1>
                <p class="text-gray-500 mt-2 font-medium">Selamat datang kembali, {{ Auth::user()->name }}!</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span id="current-time" class="text-sm font-bold text-gray-700"></span>
                </div>
                
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('reports.approvals') }}" class="relative inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-red-200 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approval Selisih Kas
                        @php $pendingCount = \App\Models\CashFlow::pendingApproval()->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="absolute -top-2 -right-2 flex h-6 w-6">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-6 w-6 bg-red-500 text-white text-[10px] font-bold items-center justify-center border-2 border-white">{{ $pendingCount }}</span>
                            </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 px-4 sm:px-0">
            <!-- Total Produk -->
            <div class="group relative bg-gradient-to-br from-blue-600 to-blue-700 rounded-3xl p-6 shadow-xl shadow-blue-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-blue-200 overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <span class="text-blue-100 text-[10px] font-bold uppercase tracking-widest">Products</span>
                    </div>
                    <p class="text-blue-100 text-sm font-medium">Total Produk</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ number_format($stats['total_products']) }}</h3>
                    <div class="mt-4 flex items-center text-xs text-blue-100 font-bold">
                        <span class="bg-white/20 px-2 py-0.5 rounded-lg">Katalog Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Stok Rendah -->
            <div class="group relative bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl p-6 shadow-xl shadow-orange-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-orange-200 overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <span class="text-orange-100 text-[10px] font-bold uppercase tracking-widest">Inventory</span>
                    </div>
                    <p class="text-orange-100 text-sm font-medium">Stok Rendah</p>
                    <h3 class="text-3xl font-black text-white mt-1">{{ (float)$stats['low_stock_products'] }}</h3>
                    <div class="mt-4 flex items-center text-xs text-orange-100 font-bold">
                        <span class="bg-white/20 px-2 py-0.5 rounded-lg">Perlu Restok</span>
                    </div>
                </div>
            </div>

            <!-- Saldo Kas -->
            <div class="group relative bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl p-6 shadow-xl shadow-indigo-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-indigo-200 overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="text-indigo-100 text-[10px] font-bold uppercase tracking-widest">Cash</span>
                    </div>
                    <p class="text-indigo-100 text-sm font-medium">Saldo Kas</p>
                    <h3 class="text-2xl font-black text-white mt-1 truncate">Rp {{ number_format($currentBalance, 0, ',', '.') }}</h3>
                    <div class="mt-4 flex items-center text-xs text-indigo-100 font-bold">
                        <span class="bg-white/20 px-2 py-0.5 rounded-lg">Real-time Verified</span>
                    </div>
                </div>
            </div>

            <!-- Penjualan Hari Ini -->
            <div class="group relative bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 shadow-xl shadow-emerald-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-emerald-200 overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-emerald-100 text-[10px] font-bold uppercase tracking-widest">Revenue</span>
                    </div>
                    <p class="text-emerald-100 text-sm font-medium">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-black text-white mt-1">Rp {{ number_format($stats['daily_sales'], 0, ',', '.') }}</h3>
                    <div class="mt-4 flex items-center text-xs text-emerald-100 font-bold">
                        <span class="bg-white/20 px-2 py-0.5 rounded-lg">Trend Positif</span>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isSuperAdmin() && !session('admin_active_branch_id'))
        <!-- Widget Saldo Per Cabang -->
        <div class="mb-8 px-4 sm:px-0">
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-100 rounded-3xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-50 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-2xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Ringkasan Saldo Per Cabang</h3>
                            <p class="text-sm text-gray-500 font-medium">Monitoring likuiditas seluruh unit bisnis</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Nama Cabang</th>
                                <th class="px-8 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Saldo Terverifikasi</th>
                                <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($branchBalances as $bb)
                            <tr class="hover:bg-indigo-50/30 transition-colors duration-150 group">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 mr-3 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                            {{ substr($bb['name'], 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">{{ $bb['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right">
                                    <span class="text-sm font-black text-indigo-600">Rp {{ number_format($bb['balance'], 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 border border-green-200">
                                        ● Aktif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 px-4 sm:px-0">
            <!-- Sales Chart -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-100 rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-blue-50 rounded-2xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Trend Penjualan</h3>
                            <p class="text-xs text-gray-500 font-medium">Statistik 7 hari terakhir</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div class="h-[300px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Product Categories Chart -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-100 rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-emerald-50 rounded-2xl">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Distribusi Kategori</h3>
                            <p class="text-xs text-gray-500 font-medium">Persentase stok per kategori</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div class="h-[300px]">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 px-4 sm:px-0">
            <!-- Recent Transactions -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-100 rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-cyan-50 rounded-2xl">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Transaksi Terbaru</h3>
                            <p class="text-xs text-gray-500 font-medium">Aktivitas penjualan terkini</p>
                        </div>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($recent_transactions as $transaction)
                        <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-2xl hover:bg-gray-100 transition-all duration-200 group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">#{{ $transaction->transaction_code }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Sukses</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-500 italic">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-100 rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-red-50 rounded-2xl">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Peringatan Stok</h3>
                            <p class="text-xs text-gray-500 font-medium">Produk di bawah batas minimum</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($low_stock_products as $product)
                        <div class="flex items-center justify-between p-4 bg-red-50/30 rounded-2xl border border-red-50 group hover:bg-red-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-red-600">{{ (float)$product->total_stock }} Unit</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Min: {{ (float)$product->minimum_stock }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Stok aman terkendali</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Defaults
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';
    
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const gradient = salesCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($sales_chart, 'date')) !!},
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($daily_sales) !!},
                borderColor: '#3b82f6',
                borderWidth: 4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                        }
                    }
                }
            }
        }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($category_distribution->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($category_distribution->pluck('product_count')) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                borderWidth: 8,
                borderColor: '#ffffff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 25,
                        font: { size: 12, weight: 'bold' }
                    }
                }
            },
            cutout: '75%'
        }
    });

    // Real-time clock
    function updateClock() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString('id-ID', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            hour12: false
        }) + ' WIB';
    }
    updateClock();
    setInterval(updateClock, 1000);
});
</script>
@endsection
