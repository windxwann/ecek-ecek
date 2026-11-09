@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
            <span>/</span>
            <a href="{{ route('marketplace.index') }}" class="hover:text-emerald-600">Katalog Motor</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">{{ $motor->merk }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Motor Media & Specs (8 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Main Image -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="h-80 sm:h-96 bg-slate-100 relative flex items-center justify-center">
                        @if($motor->photo)
                            <img src="{{ asset('storage/'.$motor->photo) }}" alt="{{ $motor->merk }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i data-lucide="bike" class="w-20 h-20 mb-2"></i>
                                <span class="text-sm font-medium">Foto Unit Belum Diunggah</span>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-white/95 backdrop-blur-sm text-slate-900 shadow-md">
                                {{ $motor->tipe_cc }} CC
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-md flex items-center">
                                <span class="w-2 h-2 rounded-full bg-white mr-2"></span> Unit Tersedia
                            </span>
                        </div>
                    </div>

                    <!-- Motor Highlights -->
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4 mb-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $motor->merk }}</h1>
                                <p class="text-xs text-slate-400 mt-1">
                                    Nomor Plat: <span class="font-mono font-bold text-slate-700">{{ $motor->no_plat }}</span> &bull; 
                                    Pemilik: <span class="font-medium text-slate-700">{{ $motor->pemilik->nama ?? '-' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Specifications Grid -->
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Spesifikasi & Fasilitas</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-xs text-slate-400">Kapasitas Mesin</div>
                                <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $motor->tipe_cc }} CC</div>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-xs text-slate-400">Status Legalitas</div>
                                <div class="text-sm font-bold text-emerald-600 mt-0.5 flex items-center">
                                    <i data-lucide="check" class="w-4 h-4 mr-1"></i> STNK Aktif
                                </div>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-xs text-slate-400">Kelengkapan</div>
                                <div class="text-sm font-bold text-slate-800 mt-0.5">2 Helm + Jas Hujan</div>
                            </div>
                        </div>

                        <!-- Jadwal Terpesan -->
                        @if($bookedRanges->count() > 0)
                            <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                                <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center mb-2">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-1.5 text-amber-600"></i> Periode Telah Dipesan
                                </h4>
                                <p class="text-xs text-amber-700 mb-2">Unit ini sudah memiliki jadwal sewa pada tanggal berikut:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($bookedRanges as $range)
                                        <span class="px-2.5 py-1 rounded-lg bg-white border border-amber-300 text-xs font-semibold text-amber-900">
                                            {{ tanggal_id($range['start']) }} s/d {{ tanggal_id($range['end']) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Pricing & Booking Action (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-lg p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4">Pilihan Tarif Sewa</h2>

                    <!-- Tariff cards -->
                    <div class="space-y-3 mb-6">
                        <div class="p-4 rounded-2xl border-2 border-emerald-600 bg-emerald-50/50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Tarif Harian</span>
                                <span class="text-xs text-slate-500">Cocok untuk kebutuhan harian & santai</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-emerald-700">{{ rupiah($motor->tarif?->tarif_harian ?? 0) }}</span>
                                <span class="text-xs text-slate-400 block">/hari</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-white flex items-center justify-between hover:border-slate-300 transition">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Tarif Mingguan</span>
                                <span class="text-xs text-slate-500">Lebih hemat untuk 7 hari atau lebih</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-bold text-slate-900">{{ rupiah($motor->tarif?->tarif_mingguan ?? 0) }}</span>
                                <span class="text-xs text-slate-400 block">/minggu</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 bg-white flex items-center justify-between hover:border-slate-300 transition">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Tarif Bulanan</span>
                                <span class="text-xs text-slate-500">Paling ekonomis untuk sewa jangka panjang</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-bold text-slate-900">{{ rupiah($motor->tarif?->tarif_bulanan ?? 0) }}</span>
                                <span class="text-xs text-slate-400 block">/bulan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Button or Auth redirect -->
                    @auth
                        @if(auth()->user()->isPenyewa())
                            <a href="{{ route('tenant.rentals.create', $motor) }}"
                               class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center text-sm shadow-lg shadow-emerald-500/25 flex items-center justify-center transition">
                                <i data-lucide="key" class="w-5 h-5 mr-2"></i> Lanjutkan Pemesanan Sewa
                            </a>
                        @else
                            <div class="p-4 bg-slate-100 text-slate-700 rounded-2xl text-xs text-center font-medium border border-slate-200">
                                <p>Anda saat ini masuk sebagai <strong class="text-slate-900">{{ strtoupper(auth()->user()->role) }}</strong>. Pemesanan unit hanya dapat dilakukan menggunakan akun penyewa.</p>
                                <div class="mt-3">
                                    <a href="{{ auth()->user()->dashboardRoute() }}"
                                       class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                                        <i data-lucide="arrow-left" class="w-3.5 h-3.5 mr-1.5"></i> Kembali ke Dashboard {{ ucfirst(auth()->user()->role) }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center text-sm shadow-lg shadow-emerald-500/25 flex items-center justify-center transition">
                            <i data-lucide="log-in" class="w-5 h-5 mr-2"></i> Masuk untuk Menyewa Motor
                        </a>
                        <p class="text-center text-xs text-slate-500 mt-2">
                            Belum punya akun? <a href="{{ route('register') }}?role=penyewa" class="text-emerald-600 font-bold hover:underline">Daftar sekarang</a>
                        </p>
                    @endauth

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                        <div class="flex items-center">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0"></i>
                            <span>Verifikasi kondisi fisik & kelengkapan unit oleh admin</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0"></i>
                            <span>Konfirmasi pembayaran cepat dalam jam operasional</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="help-circle" class="w-4 h-4 text-slate-400 mr-2 flex-shrink-0"></i>
                            <span>Bantuan teknis & darurat saat masa sewa aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
