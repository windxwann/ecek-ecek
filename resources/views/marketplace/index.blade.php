@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Breadcrumbs -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Katalog Motor</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Motor Disewakan</h1>
            <p class="text-sm text-slate-500 mt-1">Ditemukan {{ $motors->total() }} unit motor tersedia siap jalan.</p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm mb-8">
            <form method="GET" action="{{ route('marketplace.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                
                <!-- Search Keyword -->
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari merk motor, plat nomor..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                </div>

                <!-- CC Filter -->
                <div class="sm:col-span-3">
                    <select name="cc" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        <option value="">Semua Kapasitas CC</option>
                        <option value="100" {{ request('cc') === '100' ? 'selected' : '' }}>100 CC (Hemat & Lincah)</option>
                        <option value="125" {{ request('cc') === '125' ? 'selected' : '' }}>125 CC (Seimbang & Praktis)</option>
                        <option value="150" {{ request('cc') === '150' ? 'selected' : '' }}>150 CC (Tenaga & Maxi)</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="sm:col-span-2">
                    <select name="sort" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        <option value="">Urutkan: Terbaru</option>
                        <option value="termurah" {{ request('sort') === 'termurah' ? 'selected' : '' }}>Tarif Termurah</option>
                        <option value="termahal" {{ request('sort') === 'termahal' ? 'selected' : '' }}>Tarif Termahal</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="sm:col-span-2 flex space-x-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center justify-center">
                        <i data-lucide="filter" class="w-4 h-4 mr-1.5"></i> Filter
                    </button>
                    @if(request()->anyFilled(['q', 'cc', 'sort']))
                        <a href="{{ route('marketplace.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Motor Grid -->
        @if($motors->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($motors as $motor)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group">
                        
                        <!-- Motor Image -->
                        <div class="h-52 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            @if($motor->photo)
                                <img src="{{ asset('storage/'.$motor->photo) }}" alt="{{ $motor->merk }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i data-lucide="bike" class="w-12 h-12 mb-1"></i>
                                    <span class="text-xs">Foto Kendaraan</span>
                                </div>
                            @endif

                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/95 backdrop-blur-sm text-slate-800 shadow-sm">
                                    {{ $motor->tipe_cc }} CC
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Siap Sewa
                                </span>
                            </div>
                        </div>

                        <!-- Motor Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-xs text-slate-400 mb-1.5 font-medium">
                                    <span class="font-mono bg-slate-100 text-slate-700 px-2 py-0.5 rounded">{{ $motor->no_plat }}</span>
                                    <span>Mitra: {{ $motor->pemilik->nama ?? 'Mitra' }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight group-hover:text-emerald-600 transition">{{ $motor->merk }}</h3>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <div class="grid grid-cols-3 gap-1 text-center py-2 bg-slate-50 rounded-xl mb-4 text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Harian</span>
                                        <span class="font-bold text-slate-800">{{ rupiah($motor->tarif?->tarif_harian ?? 0) }}</span>
                                    </div>
                                    <div class="border-x border-slate-200">
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Mingguan</span>
                                        <span class="font-bold text-slate-800">{{ rupiah($motor->tarif?->tarif_mingguan ?? 0) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Bulanan</span>
                                        <span class="font-bold text-slate-800">{{ rupiah($motor->tarif?->tarif_bulanan ?? 0) }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-slate-400 block">Mulai dari</span>
                                        <span class="text-xl font-black text-emerald-600">
                                            {{ rupiah($motor->tarif?->tarif_harian ?? 0) }}
                                            <span class="text-xs font-normal text-slate-400">/hari</span>
                                        </span>
                                    </div>
                                    <a href="{{ route('marketplace.show', $motor) }}"
                                       class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm shadow-emerald-500/20">
                                        Detail & Sewa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $motors->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
                <i data-lucide="search-x" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-slate-800">Motor Tidak Ditemukan</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Tidak ada motor yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci atau filter lain.</p>
                <div class="mt-6">
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition">
                        Reset Semua Filter
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
