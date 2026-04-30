@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Enterprise POS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Gradients & Glassmorphism */
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .hero-gradient {
            background: radial-gradient(circle at top right, #eef2ff, #ffffff);
        }

        .blob-1 {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #c7d2fe, #e0e7ff);
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
            animation: float 6s ease-in-out infinite;
        }

        .blob-2 {
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #ddd6fe, #ede9fe);
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            z-index: 0;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }

        /* Mockup Devices */
        .mockup-desktop {
            position: relative;
            z-index: 10;
            border: 8px solid #1e293b;
            border-bottom-width: 12px;
            border-radius: 16px;
            background-color: #f8fafc;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: transform 0.5s ease;
        }
        
        .mockup-desktop:hover {
            transform: translateY(-5px);
        }

        .mockup-mobile {
            position: absolute;
            bottom: -20px;
            right: -30px;
            z-index: 20;
            width: 140px;
            height: 280px;
            border: 6px solid #0f172a;
            border-radius: 24px;
            background-color: #ffffff;
            box-shadow: -10px 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: transform 0.4s ease;
        }

        .mockup-mobile:hover {
            transform: translateY(-10px) scale(1.05);
        }

        /* Bento Grid Styles */
        .bento-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }
        
        .bento-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-5px);
            border-color: #e2e8f0;
        }

        .bento-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, transparent, transparent);
            transition: all 0.3s ease;
        }

        .bento-card:hover::before {
            background: linear-gradient(to right, #4f46e5, #9333ea);
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    
    <!-- Navigation (Glassmorphism) -->
    <nav :class="{ 'glass-nav shadow-sm': scrolled, 'bg-transparent': !scrolled }" class="fixed top-0 left-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center" data-aos="fade-right" data-aos-duration="800">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        @if($storeSettings->store_logo && Storage::disk('public')->exists($storeSettings->store_logo))
                            <img src="{{ Storage::url($storeSettings->store_logo) }}"
                                 alt="{{ $storeSettings->store_name }}"
                                 class="w-12 h-12 rounded-xl object-cover shadow-sm group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl shadow-md flex items-center justify-center group-hover:rotate-12 transition-transform">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        @endif
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight">
                                {{ $storeSettings->store_name }}
                            </h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">Enterprise Edition</p>
                        </div>
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="flex items-center space-x-4 sm:space-x-8" data-aos="fade-left" data-aos-duration="800">
                    <a href="#fitur" class="hidden md:block text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Modul Sistem</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-full text-white bg-slate-900 hover:bg-indigo-600 shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-full text-white bg-slate-900 hover:bg-indigo-600 shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                            Akses Login Sistem
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 hero-gradient overflow-hidden min-h-screen flex items-center">
        <!-- Background Blobs -->
        <div class="blob-1"></div>
        <div class="blob-2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="col-span-1 lg:col-span-6 text-center lg:text-left" data-aos="fade-up" data-aos-duration="1000">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-50 border border-indigo-100 mb-6">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse mr-2"></span>
                        <span class="text-xs font-bold text-indigo-700 uppercase tracking-widest">Internal Operations Portal</span>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6">
                        Pusat Kendali 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Operasional,</span><br/>
                        Terintegrasi Sepenuhnya.
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-slate-600 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                        Portal eksklusif manajemen kasir multi-cabang. Pantau arus kas secara *real-time*, kendalikan rantai pasok antar gudang, dan pastikan integritas finansial perusahaan.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold rounded-full text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-1">
                                Lanjutkan ke Dashboard
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold rounded-full text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all duration-300 transform hover:-translate-y-1">
                                Login Karyawan
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </a>
                        @endauth
                        <a href="#fitur" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold rounded-full text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all duration-300">
                            Lihat Modul
                        </a>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-12 flex items-center justify-center lg:justify-start gap-6 text-sm font-semibold text-slate-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Internal Access Only
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            End-to-End Encryption
                        </div>
                    </div>
                </div>

                <!-- Mockup Visual -->
                <div class="col-span-1 lg:col-span-6 relative mt-16 lg:mt-0" data-aos="zoom-in-left" data-aos-duration="1200" data-aos-delay="200">
                    <!-- Desktop Mockup -->
                    <div class="mockup-desktop w-full h-[400px] flex flex-col relative">
                        <!-- Mac OS Topbar -->
                        <div class="h-8 bg-slate-100 border-b border-slate-200 flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            <div class="mx-auto h-4 w-1/3 bg-slate-200 rounded-full"></div>
                        </div>
                        <!-- Dashboard Content Fake -->
                        <div class="flex-1 p-6 flex gap-6 bg-slate-50">
                            <!-- Sidebar -->
                            <div class="w-1/4 h-full bg-white rounded-xl shadow-sm border border-slate-100 p-4 space-y-4">
                                <div class="h-6 w-3/4 bg-slate-200 rounded-md"></div>
                                <div class="h-4 w-full bg-slate-100 rounded-md mt-8"></div>
                                <div class="h-4 w-5/6 bg-slate-100 rounded-md"></div>
                                <div class="h-4 w-full bg-indigo-50 rounded-md"></div>
                                <div class="h-4 w-4/5 bg-slate-100 rounded-md"></div>
                            </div>
                            <!-- Main -->
                            <div class="w-3/4 h-full space-y-4 flex flex-col">
                                <!-- Global View Filter Simulation -->
                                <div class="flex justify-between items-center bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                                    <div class="h-5 w-1/3 bg-slate-200 rounded-md"></div>
                                    <div class="h-8 w-32 bg-indigo-100 rounded-lg flex items-center px-2 gap-2">
                                        <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                                        <div class="h-3 w-16 bg-indigo-200 rounded-md"></div>
                                    </div>
                                </div>
                                <!-- Cards -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="h-24 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md p-4 flex flex-col justify-between">
                                        <div class="h-3 w-1/2 bg-white/30 rounded-md"></div>
                                        <div class="h-6 w-3/4 bg-white/90 rounded-md"></div>
                                    </div>
                                    <div class="h-24 bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col justify-between">
                                        <div class="h-3 w-1/2 bg-slate-200 rounded-md"></div>
                                        <div class="h-6 w-3/4 bg-slate-800 rounded-md"></div>
                                    </div>
                                    <div class="h-24 bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col justify-between">
                                        <div class="h-3 w-1/2 bg-slate-200 rounded-md"></div>
                                        <div class="h-6 w-3/4 bg-slate-800 rounded-md"></div>
                                    </div>
                                </div>
                                <!-- Chart -->
                                <div class="flex-1 bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex items-end gap-2 pb-0 pt-8">
                                    <div class="w-1/6 h-[40%] bg-indigo-100 rounded-t-lg"></div>
                                    <div class="w-1/6 h-[60%] bg-indigo-200 rounded-t-lg"></div>
                                    <div class="w-1/6 h-[30%] bg-indigo-100 rounded-t-lg"></div>
                                    <div class="w-1/6 h-[80%] bg-indigo-400 rounded-t-lg"></div>
                                    <div class="w-1/6 h-[50%] bg-indigo-200 rounded-t-lg"></div>
                                    <div class="w-1/6 h-[90%] bg-indigo-600 rounded-t-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Mockup -->
                    <div class="mockup-mobile flex flex-col">
                        <!-- Notch -->
                        <div class="h-4 w-1/2 bg-slate-900 rounded-b-xl mx-auto absolute left-0 right-0 top-0"></div>
                        <!-- Screen -->
                        <div class="flex-1 bg-slate-50 pt-8 px-3 pb-4 space-y-3">
                            <div class="h-8 w-full bg-white rounded-lg shadow-sm flex items-center justify-center gap-2 border border-slate-100">
                                <!-- Map Pin Icon -->
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <div class="h-3 w-16 bg-slate-200 rounded-md"></div>
                            </div>
                            <div class="h-20 w-full bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl shadow-md p-3 flex flex-col justify-between">
                                <div class="h-2 w-1/2 bg-white/40 rounded-full"></div>
                                <div class="h-4 w-3/4 bg-white/90 rounded-md"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="h-16 flex-1 bg-white rounded-lg shadow-sm border border-slate-100"></div>
                                <div class="h-16 flex-1 bg-white rounded-lg shadow-sm border border-slate-100"></div>
                            </div>
                            <div class="h-24 w-full bg-white rounded-xl shadow-sm border border-slate-100 mt-auto"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Features Section (Bento Grid) -->
    <div id="fitur" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <h2 class="text-indigo-600 font-bold tracking-wide uppercase text-sm mb-3">Modul Utama Sistem</h2>
                <h3 class="text-3xl md:text-4xl font-black text-slate-900 mb-6">Infrastruktur Kasir Generasi Baru</h3>
                <p class="text-lg text-slate-500 font-medium">Sistem terpadu yang didesain khusus untuk efisiensi operasional internal, menjamin keamanan data, akurasi stok multi-gudang, dan transparansi arus kas perusahaan.</p>
            </div>

            <!-- Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[minmax(250px,auto)]">
                
                <!-- 1. Multi-Branch Management -->
                <div class="bento-card md:col-span-2 md:row-span-1 group" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h4 class="text-2xl font-bold text-slate-900 mb-3">Multi-Branch Management</h4>
                    <p class="text-slate-500 mb-6 text-base">Kendali penuh seluruh cabang dari satu pintu. Dilengkapi *Hierarchical Access Control* yang memisahkan wewenang Superadmin pusat dengan Admin masing-masing Cabang secara aman.</p>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Centralized Control</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Data Isolation</span>
                    </div>
                </div>

                <!-- 2. Financial Integrity -->
                <div class="bento-card md:col-span-1 md:row-span-2 group flex flex-col" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-bold text-slate-900 mb-3">Financial Integrity</h4>
                    <p class="text-slate-500 mb-6 text-base flex-1">Sistem verifikasi selisih kas untuk keamanan arus kas. Setiap shift kasir dipantau ketat, dan pelaporan selisih dana membutuhkan validasi berlapis sebelum diakui sistem.</p>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mt-auto">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500">Audit Kas Harian</span>
                            <span class="text-xs font-black text-emerald-600">Verified</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full w-full"></div></div>
                    </div>
                </div>

                <!-- 3. Precision Inventory -->
                <div class="bento-card md:col-span-1 md:row-span-1 group" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-cyan-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Precision Inventory</h4>
                    <p class="text-slate-500 text-sm">Stok akurat di setiap gudang dengan fitur transfer stok internal yang terdokumentasi dan membutuhkan *approval* manajemen.</p>
                </div>

                <!-- 4. Identity Override -->
                <div class="bento-card md:col-span-1 md:row-span-1 group" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Identity Override</h4>
                    <p class="text-slate-500 text-sm">Standarisasi format struk otomatis yang dapat dikustomisasi (Logo, Alamat, Kertas) menyesuaikan profil masing-masing cabang operasional.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-slate-900 py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/50 to-purple-900/50"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in" data-aos-duration="1000">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Akses Portal Karyawan</h2>
            <p class="text-lg text-slate-300 mb-10">Silakan login menggunakan kredensial yang telah diberikan oleh tim IT Support. Segala aktivitas di dalam sistem tercatat dan diawasi.</p>
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-10 py-5 text-lg font-bold rounded-full text-slate-900 bg-white hover:bg-slate-100 transition-all duration-300 transform hover:scale-105">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-10 py-5 text-lg font-bold rounded-full text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-900/20 transition-all duration-300 transform hover:scale-105">
                    Login Ke Sistem
                </a>
            @endauth
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-950 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold text-white">{{ config('app.name', 'Kasir App') }}</span>
            </div>
            <div class="text-slate-400 text-sm font-medium text-center md:text-right">
                <p>&copy; {{ date('Y') }} {{ $storeSettings->store_name }}. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- AOS Script Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true, // Animasi hanya berjalan sekali saat discroll ke bawah
                offset: 50, // Trigger animasi sedikit lebih awal
                easing: 'ease-out-cubic',
            });
        });
    </script>
</body>
</html>
