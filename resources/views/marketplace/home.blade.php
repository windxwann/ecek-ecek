@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-50/60 to-white pt-16 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Platform Sewa Motor Kemitraan No. 1</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Sewa Motor Mudah, <span class="text-emerald-600">Aman & Transparan</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Solusi mobilitas praktis untuk harian, mingguan, atau bulanan. Pilih motor impianmu dengan tarif terjangkau, unit terverifikasi, dan proses pemesanan cepat secara online.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <a href="{{ route('marketplace.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/25 transition">
                        <i data-lucide="search" class="w-5 h-5 mr-2"></i> Cari Motor Tersedia
                    </a>
                    @guest
                    <a href="{{ route('register') }}?role=pemilik"
                       class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        <i data-lucide="key" class="w-5 h-5 mr-2 text-emerald-600"></i> Jadi Mitra Pemilik
                    </a>
                    @endguest
                </div>

                <!-- Stats summary -->
                <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-200/60 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <div class="text-2xl font-black text-slate-900">{{ $totalMotors }}</div>
                        <div class="text-xs text-slate-500 font-medium">Motor Siap Pakai</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-slate-900">{{ $totalOwners }}</div>
                        <div class="text-xs text-slate-500 font-medium">Mitra Pemilik Aktif</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-600">100%</div>
                        <div class="text-xs text-slate-500 font-medium">Unit Terverifikasi</div>
                    </div>
                </div>
            </div>

            <!-- Hero Banner Graphic -->
            <div class="relative flex justify-center">
                <div class="w-full max-w-md bg-white p-6 rounded-3xl shadow-2xl border border-slate-100 relative">
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                        Unit Terverifikasi
                    </div>
                    <div class="h-56 bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center mb-5 relative group">
                        <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=800&q=80"
                             alt="Motor Showcase"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <span class="text-xs uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-emerald-600">Populer</span>
                            <h3 class="text-lg font-bold">Yamaha NMAX 155 Connected</h3>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Tarif Harian Mulai</span>
                            <span class="text-lg font-extrabold text-emerald-600">Rp 140.000<span class="text-xs font-normal text-slate-400">/hari</span></span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                            <span class="flex items-center"><i data-lucide="shield-check" class="w-4 h-4 mr-1 text-emerald-500"></i> STNK & BPKB Terjamin</span>
                            <span class="flex items-center"><i data-lucide="zap" class="w-4 h-4 mr-1 text-amber-500"></i> Siap Pakai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Motors Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Motor Tersedia</h2>
                <p class="text-sm text-slate-500 mt-1">Pilihan motor prima siap sewa untuk kelancaran aktivitas Anda.</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="mt-4 sm:mt-0 text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center">
                Lihat Semua Motor <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        @if($motors->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($motors as $motor)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group">
                        <!-- Motor Image -->
                        <div class="h-48 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            @if($motor->photo)
                                <img src="{{ asset('storage/'.$motor->photo) }}" alt="{{ $motor->merk }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i data-lucide="bike" class="w-12 h-12 mb-1"></i>
                                    <span class="text-xs">Foto Kendaraan</span>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 flex gap-1">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/90 backdrop-blur-sm text-slate-800 shadow-sm">
                                    {{ $motor->tipe_cc }} CC
                                </span>
                            </div>
                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5"></span> Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Motor Details -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                    <span>Plat: <strong class="text-slate-600 font-mono">{{ $motor->no_plat }}</strong></span>
                                    <span>Mitra: {{ $motor->pemilik->nama ?? 'Mitra' }}</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 tracking-tight group-hover:text-emerald-600 transition">{{ $motor->merk }}</h3>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-slate-400 block">Tarif Harian</span>
                                    <span class="text-lg font-black text-emerald-600">
                                        {{ rupiah($motor->tarif?->tarif_harian ?? 0) }}
                                    </span>
                                </div>
                                <a href="{{ route('marketplace.show', $motor) }}"
                                   class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm shadow-emerald-500/20">
                                    Detail & Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i data-lucide="bike" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-700">Belum ada motor tersedia</h3>
                <p class="text-xs text-slate-500 mt-1">Silakan mendaftarkan motor sebagai pemilik atau periksa kembali nanti.</p>
            </div>
        @endif
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-slate-50 border-t border-slate-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Cara Kerja Platform</h2>
            <p class="text-sm text-slate-500 mt-2">Alur mudah dan terstruktur dari pencarian hingga motor selesai digunakan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-4 font-bold text-lg">1</div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Pilih Motor</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Cari tipe CC, merk, dan harga sewa motor yang sesuai dengan kebutuhan Anda.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-4 font-bold text-lg">2</div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Tentukan Tanggal</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Pilih periode sewa harian, mingguan, atau bulanan. Sistem menghitung harga otomatis.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-4 font-bold text-lg">3</div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Bayar & Verifikasi</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Lakukan pembayaran via Transfer Bank atau QRIS, lalu unggah bukti transfer.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-4 font-bold text-lg">4</div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Nikmati Perjalanan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Setelah dikonfirmasi admin, motor siap digunakan dan kembalikan sesuai jadwal.</p>
            </div>
        </div>
    </div>
</section>
@endsection
