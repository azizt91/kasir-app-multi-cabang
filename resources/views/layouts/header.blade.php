<!-- Top Header -->
<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left side: Mobile menu button + Breadcrumbs -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" 
                        class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Breadcrumbs -->
                <nav class="hidden sm:flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                            </a>
                        </li>
                        
                        @if(request()->routeIs('dashboard'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Dashboard</span>
                            </li>
                        @elseif(request()->routeIs('pos.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Point of Sale</span>
                            </li>
                        @elseif(request()->routeIs('products.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Produk</span>
                            </li>
                        @elseif(request()->routeIs('categories.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Kategori</span>
                            </li>
                        @elseif(request()->routeIs('reports.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">
                                    @if(request()->routeIs('reports.activity_logs'))
                                        Log Aktivitas
                                    @elseif(request()->routeIs('reports.receivables'))
                                        Piutang
                                    @else
                                        Laporan
                                    @endif
                                </span>
                            </li>
                        @elseif(request()->routeIs('users.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Pengguna</span>
                            </li>
                        @elseif(request()->routeIs('settings.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Pengaturan</span>
                            </li>
                        @elseif(request()->routeIs('expenses.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Pengeluaran</span>
                            </li>
                        @elseif(request()->routeIs('purchases.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Pembelian</span>
                            </li>
                        @elseif(request()->routeIs('cash-flows.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Arus Kas</span>
                            </li>
                        @elseif(request()->routeIs('suppliers.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Supplier</span>
                            </li>
                        @elseif(request()->routeIs('customers.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Pelanggan</span>
                            </li>
                        @elseif(request()->routeIs('transactions.*'))
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-600 font-medium">Riwayat Transaksi</span>
                            </li>
                        @endif
                    </ol>
                </nav>
            </div>

            <!-- Right side: Quick actions + User menu -->
            <div class="flex items-center space-x-3">
                <!-- Quick POS Access (for admins) -->
                @if(auth()->user()->role === 'admin' && !request()->routeIs('pos.*'))
                    <a href="{{ route('pos.index') }}" 
                       class="hidden sm:flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 1.5M7 13l1.5 1.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                        Quick Sale
                    </a>
                @endif

                <!-- Branch Filter Component -->
                @if(auth()->user()->isSuperAdmin())
                    @php
                        $activeBranchId = session('admin_active_branch_id');
                        $activeBranchName = $activeBranchId ? \App\Models\Branch::find($activeBranchId)->name : 'Global View (Semua Cabang)';
                        $branches = \App\Models\Branch::where('is_active', true)->get();
                    @endphp

                    <div x-data="{ branchMenuOpen: false, mobileBranchModal: false }" class="relative flex items-center">
                        
                        <!-- ========================================== -->
                        <!-- MOBILE VIEW: Map Pin Icon (< 768px sm:hidden) -->
                        <!-- ========================================== -->
                        <button @click="mobileBranchModal = true" 
                                class="sm:hidden p-2 rounded-lg transition-colors duration-200 relative
                                {{ $activeBranchId ? 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100' : 'text-gray-500 hover:bg-gray-100' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- Map Pin Icon -->
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <!-- Badge indicator for specific branch -->
                            @if($activeBranchId)
                                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-indigo-600 border-2 border-white rounded-full"></span>
                            @endif
                        </button>

                        <!-- ========================================== -->
                        <!-- DESKTOP/TABLET VIEW: Dropdown (sm:flex)    -->
                        <!-- ========================================== -->
                        <div class="hidden sm:block relative">
                            <button @click="branchMenuOpen = !branchMenuOpen" @click.away="branchMenuOpen = false"
                                    class="flex items-center space-x-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors duration-200
                                    {{ $activeBranchId 
                                        ? 'bg-indigo-50 border-indigo-200 text-indigo-700 hover:bg-indigo-100' 
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 {{ $activeBranchId ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="max-w-[150px] truncate">{{ $activeBranchName }}</span>
                                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown List -->
                            <div x-show="branchMenuOpen" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 style="display: none;"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                                
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Konteks Data</p>
                                </div>

                                <!-- Opsi Global -->
                                <form action="{{ route('set-global-branch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="">
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between {{ !$activeBranchId ? 'bg-gray-50 font-bold text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-2 h-2 rounded-full {{ !$activeBranchId ? 'bg-gray-800' : 'bg-transparent' }}"></span>
                                            <span>Global View (Semua Cabang)</span>
                                        </div>
                                        @if(!$activeBranchId)
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </button>
                                </form>

                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Opsi Per Cabang -->
                                @foreach($branches as $branch)
                                    <form action="{{ route('set-global-branch') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between {{ $activeBranchId == $branch->id ? 'bg-indigo-50 font-bold text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full {{ $activeBranchId == $branch->id ? 'bg-indigo-600' : 'bg-transparent' }}"></span>
                                                <span>{{ $branch->name }}</span>
                                            </div>
                                            @if($activeBranchId == $branch->id)
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- MOBILE MODAL (Bottom Sheet)                -->
                        <!-- ========================================== -->
                        <div x-show="mobileBranchModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div x-show="mobileBranchModal" 
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                    <div x-show="mobileBranchModal"
                                         @click.away="mobileBranchModal = false"
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         class="relative transform overflow-hidden rounded-t-xl sm:rounded-xl bg-white text-left shadow-xl transition-all w-full sm:my-8 sm:max-w-lg mt-auto">
                                        
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                    <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="modal-title">Pilih Konteks Cabang</h3>
                                                    
                                                    <div class="mt-2 space-y-2">
                                                        <!-- Opsi Global Mobile -->
                                                        <form action="{{ route('set-global-branch') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="branch_id" value="">
                                                            <button type="submit" class="w-full rounded-lg border p-4 text-left flex items-center justify-between {{ !$activeBranchId ? 'border-gray-800 bg-gray-50 ring-1 ring-gray-800' : 'border-gray-200 hover:bg-gray-50' }}">
                                                                <div>
                                                                    <p class="font-semibold {{ !$activeBranchId ? 'text-gray-900' : 'text-gray-700' }}">Global View</p>
                                                                    <p class="text-xs text-gray-500 mt-1">Lihat data kumulatif dari seluruh cabang</p>
                                                                </div>
                                                                @if(!$activeBranchId)
                                                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                @endif
                                                            </button>
                                                        </form>

                                                        <!-- Opsi Cabang Mobile -->
                                                        @foreach($branches as $branch)
                                                            <form action="{{ route('set-global-branch') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                                                <button type="submit" class="w-full rounded-lg border p-4 text-left flex items-center justify-between {{ $activeBranchId == $branch->id ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                                                    <div>
                                                                        <p class="font-semibold {{ $activeBranchId == $branch->id ? 'text-indigo-700' : 'text-gray-700' }}">{{ $branch->name }}</p>
                                                                        <p class="text-xs text-gray-500 mt-1">{{ $branch->address ?? 'Filter khusus data cabang ini' }}</p>
                                                                    </div>
                                                                    @if($activeBranchId == $branch->id)
                                                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                            <button @click="mobileBranchModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif

                <!-- Notifications -->
                <div x-data="{ notificationsOpen: false }" class="relative">
                    <button @click="notificationsOpen = !notificationsOpen"
                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                        </svg>
                        <!-- Notification badge -->
                        @php
                            $lowStockCount = \App\Models\Product::lowStock()->count();
                        @endphp
                        @if($lowStockCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $lowStockCount > 9 ? '9+' : $lowStockCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications dropdown -->
                    <div x-show="notificationsOpen" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="notificationsOpen = false"
                         class="fixed sm:absolute left-4 right-4 sm:left-auto sm:right-0 mt-2 sm:w-96 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        </div>
                        
                        @if($lowStockCount > 0)
                            <div class="px-4 py-3 hover:bg-gray-50">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Low Stock Alert</p>
                                        <p class="text-xs text-gray-500">{{ $lowStockCount }} product{{ $lowStockCount > 1 ? 's' : '' }} running low on stock</p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            <a href="{{ route('products.index') }}?filter=low_stock">View products →</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">All good! No new notifications</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                        <div class="w-6 h-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- User dropdown -->
                    <div x-show="userMenuOpen" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="userMenuOpen = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('settings.index') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                System Settings
                            </a>
                        @endif
                        
                        <div class="border-t border-gray-200 mt-2 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>