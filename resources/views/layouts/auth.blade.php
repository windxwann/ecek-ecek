
@extends('layouts.base')

@section('body')
<div class="min-h-screen flex flex-col justify-center items-center bg-slate-50 px-4 py-6">

    <!-- Mobile: branding on top -->
    <div class="lg:hidden w-full max-w-md px-2 py-4">
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-sm shadow-emerald-500/25">
                    <i data-lucide="bike" class="w-5 h-5"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">
                    Ride<span class="text-emerald-600">Go</span>
                </span>
            </a>
            <h2 class="mt-3 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                @yield('heading', 'Selamat Datang')
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                @yield('subheading', 'Platform rental motor modern dan terpercaya')
            </p>
        </div>
    </div>

    <!-- Desktop: split layout -->
    <div class="hidden lg:flex w-full max-w-[1100px] mx-auto overflow-hidden rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 bg-white">

        <!-- Left: Identity -->
        <div class="lg:w-5/12 bg-emerald-600 p-8 sm:p-12 text-white relative overflow-hidden flex flex-col justify-center items-start sm:items-center">
            <div class="relative z-10 max-w-xs sm:max-w-none mx-auto sm:mx-0">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white backdrop-blur-sm">
                        <i data-lucide="bike" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight">
                        Ride<span class="text-emerald-300">Go</span>
                    </span>
                </a>
                <h2 class="text-2xl font-bold tracking-tight mb-3 sm:text-3xl">
                    @yield('heading', 'Selamat Datang')
                </h2>
                <p class="text-emerald-100 text-sm leading-relaxed mb-8 sm:mb-10">
                    @yield('subheading', 'Platform rental motor modern dan terpercaya')
                </p>
                <div class="space-y-4 text-emerald-100/90 text-sm">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Sewa motor harian, mingguan, atau bulanan</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Unit terverifikasi & STNK/BPKB terjamin</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Konfirmasi pembayaran cepat oleh admin</span>
                    </div>
                </div>
            </div>

            <!-- Decorative element -->
            <div class="absolute -right-10 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Right: Form -->
        <div class="lg:w-7/12 bg-white p-6 sm:p-10 flex flex-col justify-center items-center">
            <div class="w-full max-w-sm sm:max-w-md mx-auto">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile: form below branding -->
    <div class="lg:hidden w-full max-w-md px-2">
        <div class="bg-white py-6 sm:py-8 px-4 sm:px-6 rounded-2xl border border-slate-100 shadow-sm shadow-slate-200/50">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} RideGo. Aman, Mudah &amp; Transparan.
    </div>

</div>
@endsection
