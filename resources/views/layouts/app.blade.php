@extends('layouts.base')

@section('body')
<div class="min-h-full flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                            <i data-lucide="bike" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">
                            Ride<span class="text-emerald-600">Go</span>
                        </span>
                    </a>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex ml-8 space-x-6">
                        <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-emerald-600' : 'text-slate-600 hover:text-slate-900' }}">
                            Beranda
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="text-sm font-medium {{ request()->routeIs('marketplace.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-slate-900' }}">
                            Katalog Motor
                        </a>
                    </nav>
                </div>

                <!-- Right / Auth -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    @auth
                        <!-- Direct Dashboard Quick Link for Easy Return -->
                        <a href="{{ auth()->user()->dashboardRoute() }}"
                           class="inline-flex items-center px-3 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-500/20 transition">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Dashboard {{ auth()->user()->role === 'pemilik' ? 'Pemilik' : (auth()->user()->role === 'admin' ? 'Admin' : 'Penyewa') }}</span>
                        </a>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none">
                                <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr(auth()->user()->nama, 0, 2) }}
                                </span>
                                <span class="hidden sm:inline">{{ auth()->user()->nama }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold uppercase">{{ auth()->user()->role }}</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">
                                <a href="{{ auth()->user()->dashboardRoute() }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2 text-slate-400"></i> Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-400"></i> Profil Saya
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-500/20 transition-all">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white">
                            <i data-lucide="bike" class="w-5 h-5"></i>
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">Ride<span class="text-emerald-500">Go</span></span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm">
                        Platform rental motor modern berbasis kemitraan. Menghubungkan pemilik kendaraan dengan penyewa dalam ekosistem aman, transparan, dan terpercaya.
                    </p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Layanan</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('marketplace.index') }}" class="hover:text-white transition">Katalog Motor</a></li>
                        <li><a href="{{ route('register') }}?role=pemilik" class="hover:text-white transition">Gabung Jadi Mitra Pemilik</a></li>
                        <li><a href="{{ route('register') }}?role=penyewa" class="hover:text-white transition">Daftar Jadi Penyewa</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Metode Pembayaran</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Mendukung Transfer Bank Mandiri, BCA, BNI, BRI dan QRIS Instan dengan konfirmasi cepat.
                    </p>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} RideGo. Platform Rental Motor Terpercaya. All rights reserved.</p>
                <p class="mt-2 sm:mt-0">Uji Kompetensi Keahlian (UJIKOM) RPL</p>
            </div>
        </div>
    </footer>
</div>
@endsection
