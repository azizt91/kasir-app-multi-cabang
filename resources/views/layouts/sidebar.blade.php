@php
use Illuminate\Support\Facades\Storage;
@endphp

<!-- Desktop Sidebar -->
<div class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 z-50 shrink-0">
    <div class="flex flex-col flex-grow h-full bg-white border-r border-gray-200 shadow-sm">
        <!-- Logo Section -->
        <div class="flex items-center flex-shrink-0 px-6 py-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                @if(isset($storeSettings) && $storeSettings->store_logo && Storage::disk('public')->exists($storeSettings->store_logo))
                    <img src="{{ Storage::url($storeSettings->store_logo) }}"
                         alt="{{ $storeSettings->store_name }}"
                         class="w-10 h-10 rounded-lg object-cover">
                @else
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $storeSettings->store_name ?? 'Minimarket POS' }}</h1>
                    <p class="text-xs text-gray-500">Point of Sale System</p>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto min-h-0">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               data-tooltip="Dashboard">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- POS -->
            <a href="{{ route('pos.index') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('pos.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               data-tooltip="Point of Sale">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                <span>Point of Sale</span>
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products') || auth()->user()->hasPermission('view_suppliers') || auth()->user()->hasPermission('view_customers') || auth()->user()->hasPermission('view_categories') || auth()->user()->hasPermission('view_expenses') || auth()->user()->hasPermission('view_purchases') || auth()->user()->hasPermission('view_transactions') || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_receivables'))
                <!-- Management Section Divider -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen</p>
                </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products'))
                <!-- Products -->
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Product Management">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Produk</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_suppliers'))
                <!-- Suppliers -->
                <a href="{{ route('suppliers.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('suppliers.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Manajemen Supplier">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Suppliers</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_customers'))
                <!-- Customers -->
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Manajemen Pelanggan">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Pelanggan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_categories'))
                <!-- Categories -->
                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Categories">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kategori</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_expenses'))
                <!-- Expenses -->
                <a href="{{ route('expenses.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('expenses.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Pengeluaran Operasional">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pengeluaran</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_purchases'))
                <!-- Purchases -->
                <a href="{{ route('purchases.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('purchases.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Pembelian Stok">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                    <span>Pembelian</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_cash_flows'))
                <!-- Cash Flow -->
                <a href="{{ route('cash-flows.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('cash-flows.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Manajemen Arus Kas">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Arus Kas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_transactions'))
                <!-- Transaction History -->
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Riwayat Transaksi">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Transaksi</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_reports'))
                <!-- Reports -->
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.*') && !request()->routeIs('reports.receivables') && !request()->routeIs('reports.approvals') && !request()->routeIs('reports.activity_logs') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Reports & Analytics">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Laporan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_receivables'))
                <!-- Piutang -->
                <a href="{{ route('reports.receivables') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.receivables') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Laporan Piutang">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Piutang</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <!-- Log Aktivitas -->
                <a href="{{ route('reports.activity_logs') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.activity_logs') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Log Aktivitas (Audit Trail)">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Log Aktivitas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                {{-- System Section: visible to all admins --}}
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sistem</p>
                </div>

                {{-- Users: Branch Admin can manage users of their own branch --}}
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="User Management">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                    </svg>
                    <span>Pengguna</span>
                </a>

                {{-- Gudang --}}
                <a href="{{ route('warehouses.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Manajemen Gudang">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                    <span>Gudang</span>
                </a>

                {{-- Transfer Stok --}}
                <a href="{{ route('stock-transfers.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('stock-transfers.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Transfer Stok">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Transfer Stok</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                {{-- Superadmin-only: structural system configuration --}}
                {{-- Cabang --}}
                <a href="{{ route('branches.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('branches.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Manajemen Cabang">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Cabang</span>
                </a>

                {{-- Verifikasi Kas (New for Superadmin) --}}

                {{-- Verifikasi Kas (New for Superadmin) --}}
                @php
                    $pendingCount = \App\Models\CashFlow::pendingApproval()->count();
                @endphp
                <a href="{{ route('reports.approvals') }}"
                   class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.approvals') ? 'bg-red-50 text-red-700 border-r-2 border-red-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Verifikasi Selisih Kas">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verifikasi Kas</span>
                    </div>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                {{-- Pengaturan --}}
                <a href="{{ route('settings.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Settings">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Section -->
        <div class="flex-shrink-0 border-t border-gray-200 p-4">
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen"
                        class="w-full flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="ml-3 text-left flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="profileOpen = false"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg lg:hidden">
    <div class="flex flex-col h-full">
        <!-- Mobile Logo Section -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                @if(isset($storeSettings) && $storeSettings->store_logo && Storage::disk('public')->exists($storeSettings->store_logo))
                    <img src="{{ Storage::url($storeSettings->store_logo) }}"
                         alt="{{ $storeSettings->store_name }}"
                         class="w-8 h-8 rounded-lg object-cover">
                @else
                    <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-bold text-gray-900">{{ $storeSettings->store_name ?? 'Minimarket POS' }}</h1>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto min-h-0">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               @click="sidebarOpen = false">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- POS -->
            <a href="{{ route('pos.index') }}"
               class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('pos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               @click="sidebarOpen = false">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                <span>Point of Sale</span>
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products') || auth()->user()->hasPermission('view_suppliers') || auth()->user()->hasPermission('view_customers') || auth()->user()->hasPermission('view_categories') || auth()->user()->hasPermission('view_expenses') || auth()->user()->hasPermission('view_purchases') || auth()->user()->hasPermission('view_transactions') || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_receivables'))
                <!-- Management Section Divider -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Manajemen</p>
                </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products'))
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Produk</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_categories'))
                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kategori</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_suppliers'))
                <a href="{{ route('suppliers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Suppliers</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_customers'))
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Pelanggan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_expenses'))
                <a href="{{ route('expenses.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pengeluaran</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_purchases'))
                <a href="{{ route('purchases.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('purchases.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                    <span>Pembelian</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_cash_flows'))
                <a href="{{ route('cash-flows.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('cash-flows.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Arus Kas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_transactions'))
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Transaksi</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_reports'))
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.index') || (request()->routeIs('reports.*') && !request()->routeIs('reports.receivables') && !request()->routeIs('reports.approvals') && !request()->routeIs('reports.activity_logs')) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Laporan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_receivables'))
                <a href="{{ route('reports.receivables') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.receivables') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Piutang</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('reports.activity_logs') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.activity_logs') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Log Aktivitas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sistem</p>
                </div>

                {{-- Users: accessible by all admins --}}
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                    </svg>
                    <span>Pengguna</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                {{-- Superadmin-only menu items --}}
                <a href="{{ route('branches.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('branches.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Cabang</span>
                </a>

                <a href="{{ route('warehouses.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                    <span>Gudang</span>
                </a>

                <a href="{{ route('stock-transfers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('stock-transfers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Transfer Stok</span>
                </a>

                {{-- Verifikasi Kas (New for Superadmin) --}}
                @php
                    $pendingCount = \App\Models\CashFlow::pendingApproval()->count();
                @endphp
                <a href="{{ route('reports.approvals') }}"
                   class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.approvals') ? 'bg-red-50 text-red-700 border-r-2 border-red-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   data-tooltip="Verifikasi Selisih Kas">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verifikasi Kas</span>
                    </div>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('settings.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Section -->
        <div class="flex-shrink-0 border-t border-gray-200 p-4">
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen"
                        class="w-full flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="ml-3 text-left flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="profileOpen = false"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg lg:hidden">
    <div class="flex flex-col h-full">
        <!-- Mobile Logo Section -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                @if(isset($storeSettings) && $storeSettings->store_logo && Storage::disk('public')->exists($storeSettings->store_logo))
                    <img src="{{ Storage::url($storeSettings->store_logo) }}"
                         alt="{{ $storeSettings->store_name }}"
                         class="w-8 h-8 rounded-lg object-cover">
                @else
                    <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-bold text-gray-900">{{ $storeSettings->store_name ?? 'Minimarket POS' }}</h1>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto min-h-0">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               @click="sidebarOpen = false">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- POS -->
            <a href="{{ route('pos.index') }}"
               class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('pos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               @click="sidebarOpen = false">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                <span>Point of Sale</span>
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products') || auth()->user()->hasPermission('view_suppliers') || auth()->user()->hasPermission('view_customers') || auth()->user()->hasPermission('view_categories') || auth()->user()->hasPermission('view_expenses') || auth()->user()->hasPermission('view_purchases') || auth()->user()->hasPermission('view_transactions') || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_receivables'))
                <!-- Management Section Divider -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Manajemen</p>
                </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_products'))
                <a href="{{ route('products.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Produk</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_categories'))
                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kategori</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_suppliers'))
                <a href="{{ route('suppliers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Suppliers</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_customers'))
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Pelanggan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_expenses'))
                <a href="{{ route('expenses.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pengeluaran</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_purchases'))
                <a href="{{ route('purchases.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('purchases.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                    <span>Pembelian</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_cash_flows'))
                <a href="{{ route('cash-flows.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('cash-flows.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Arus Kas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_transactions'))
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Transaksi</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_reports'))
                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.index') || (request()->routeIs('reports.*') && !request()->routeIs('reports.receivables') && !request()->routeIs('reports.approvals') && !request()->routeIs('reports.activity_logs')) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Laporan</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_receivables'))
                <a href="{{ route('reports.receivables') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.receivables') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Piutang</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('reports.activity_logs') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.activity_logs') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Log Aktivitas</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Sistem</p>
                </div>

                {{-- Users --}}
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                    </svg>
                    <span>Pengguna</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('branches.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('branches.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Cabang</span>
                </a>

                <a href="{{ route('warehouses.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                    </svg>
                    <span>Gudang</span>
                </a>

                <a href="{{ route('stock-transfers.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('stock-transfers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Transfer Stok</span>
                </a>

                {{-- Verifikasi Kas --}}
                @php
                    $pendingCount = \App\Models\CashFlow::pendingApproval()->count();
                @endphp
                <a href="{{ route('reports.approvals') }}"
                   class="flex items-center justify-between px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.approvals') ? 'bg-red-50 text-red-700 border-r-2 border-red-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verifikasi Kas</span>
                    </div>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('settings.index') }}"
                   class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                   @click="sidebarOpen = false">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            @endif
        </nav>

        <!-- Mobile User Profile -->
        <div class="flex-shrink-0 border-t border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-50"
                   @click="sidebarOpen = false">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
