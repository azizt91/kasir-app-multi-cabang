<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @yield('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>

    </head>
    <body class="font-inter antialiased bg-gray-50" x-data="{ sidebarOpen: false }" x-init="
        // Close sidebar on route change
        window.addEventListener('beforeunload', () => sidebarOpen = false);
        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) sidebarOpen = false;
        });
    ">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col lg:ml-64 overflow-x-hidden min-w-0">
                <!-- Top Header -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </main>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             @click="sidebarOpen = false"></div>

        <!-- SweetAlert2 for notifications -->
        <script>
            // Global SweetAlert configuration
            if (typeof Swal !== 'undefined') {
                Swal.mixin({
                    customClass: {
                        confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg mx-2 transition-colors',
                        cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg mx-2 transition-colors'
                    },
                    buttonsStyling: false
                });
            }

            // Show success/error messages from session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#fff',
                    iconColor: '#10B981',
                    customClass: {
                        popup: 'rounded-xl shadow-xl'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#fff',
                    iconColor: '#EF4444',
                    customClass: {
                        popup: 'rounded-xl shadow-xl'
                    }
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: '<ul class="text-left text-sm space-y-1">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-xl shadow-xl',
                        confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors'
                    }
                });
            @endif
        </script>
        @stack('scripts')
    </body>
</html>
