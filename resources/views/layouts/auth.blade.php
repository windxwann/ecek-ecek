@extends('layouts.base')

@section('body')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <i data-lucide="bike" class="w-7 h-7"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">
                Ride<span class="text-emerald-600">Go</span>
            </span>
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            @yield('heading', 'Selamat Datang')
        </h2>
        <p class="mt-1 text-sm text-slate-600">
            @yield('subheading', 'Platform rental motor modern dan terpercaya')
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100">
            @yield('content')
        </div>

        <div class="mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} RideGo. Aman, Mudah & Transparan.
        </div>
    </div>
</div>
@endsection
