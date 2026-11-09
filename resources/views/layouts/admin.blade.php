@extends('layouts.base')

@section('body')
<div class="min-h-full flex" x-data="{ sidebarOpen: false }">
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col lg:w-64">
        
        <!-- Brand -->
        <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Ride<span class="text-blue-500">Go</span></span>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="px-4 py-3 border-b border-slate-800/60 bg-slate-900/60 flex items-center space-x-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase tracking-wider">
                Admin Panel
            </span>
            <span class="text-xs text-slate-400 truncate">{{ auth()->user()->nama }}</span>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 px-3 mb-2">Utama</div>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                Dashboard
            </a>

            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 px-3 mt-6 mb-2">Manajemen</div>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                Kelola Pengguna
            </a>

            <a href="{{ route('admin.motors.index') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.motors.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="bike" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.motors.*') ? 'text-white' : 'text-slate-400' }}"></i>
                Motor & Verifikasi
            </a>

            <a href="{{ route('admin.rentals.index') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.rentals.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.rentals.*') ? 'text-white' : 'text-slate-400' }}"></i>
                Penyewaan
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.payments.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="credit-card" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.payments.*') ? 'text-white' : 'text-slate-400' }}"></i>
                Pembayaran
            </a>

            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 px-3 mt-6 mb-2">Laporan & Finansial</div>

            <a href="{{ route('admin.reports.revenue') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.reports.revenue') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="trending-up" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.revenue') ? 'text-white' : 'text-slate-400' }}"></i>
                Laporan Pendapatan
            </a>

            <a href="{{ route('admin.reports.payments') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.reports.payments') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="receipt" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.payments') ? 'text-white' : 'text-slate-400' }}"></i>
                Laporan Pembayaran
            </a>

            <a href="{{ route('admin.reports.profit_sharing') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.reports.profit_sharing') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.profit_sharing') ? 'text-white' : 'text-slate-400' }}"></i>
                Laporan Bagi Hasil
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-slate-800">
            <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">
                <i data-lucide="store" class="w-4 h-4 mr-2"></i> Buka Marketplace
            </a>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 lg:hidden focus:outline-none">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-lg font-bold text-slate-800">@yield('page_title', 'Admin Dashboard')</h1>
            </div>

            <!-- Profile Menu -->
            <div class="flex items-center space-x-4">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none">
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr(auth()->user()->nama, 0, 2) }}
                        </span>
                        <span class="hidden sm:inline font-semibold">{{ auth()->user()->nama }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs text-slate-400">Masuk sebagai</p>
                            <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->email }}</p>
                        </div>
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
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>
@endsection
