@extends('layouts.owner')

@section('page_title', 'Detail Motor Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('owner.motors.index') }}" class="hover:text-emerald-600">Motor Saya</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $motor->merk }}</span>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                @if($motor->photo)
                    <img src="{{ asset('storage/'.$motor->photo) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="bike" class="w-10 h-10 text-slate-400"></i>
                @endif
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-emerald-600 uppercase">{{ $motor->tipe_cc }} CC</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $motor->no_plat }}</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 mt-1">{{ $motor->merk }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Didaftarkan pada {{ tanggal_id($motor->created_at, 'd M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-0 pt-3 sm:pt-0">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold
                @if($motor->status === 'tersedia') bg-emerald-50 text-emerald-700 border border-emerald-200
                @elseif($motor->status === 'disewa') bg-slate-900 text-white border border-slate-900
                @elseif($motor->status === 'menunggu_verifikasi') bg-amber-50 text-amber-700 border border-amber-200
                @elseif($motor->status === 'ditolak') bg-red-50 text-red-600 border border-red-200
                @elseif($motor->status === 'perawatan') bg-amber-50 text-amber-700 border border-amber-200
                @else bg-slate-100 text-slate-600 @endif">
                {{ $motor->status_label }}
            </span>

            @if($motor->status !== 'disewa')
                <a href="{{ route('owner.motors.edit', $motor) }}"
                   class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Ubah Data
                </a>
            @endif
        </div>
    </div>

    <!-- Status & Admin Notes -->
    @if($motor->catatan_admin)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
            <strong class="font-bold flex items-center mb-1">
                <i data-lucide="message-square" class="w-4 h-4 mr-1.5 text-amber-600"></i> Catatan Admin:
            </strong>
            <p>{{ $motor->catatan_admin }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Rental Pricing Rates -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                <i data-lucide="tag" class="w-5 h-5 mr-2 text-emerald-600"></i> Tarif Sewa (Ditetapkan Admin)
            </h3>

            @if($motor->tarif)
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium uppercase tracking-wider">Tarif Harian</span>
                        <span class="font-bold text-slate-900">{{ rupiah($motor->tarif->tarif_harian) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium uppercase tracking-wider">Tarif Mingguan</span>
                        <span class="font-bold text-slate-900">{{ rupiah($motor->tarif->tarif_mingguan) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xs text-slate-600 font-medium uppercase tracking-wider">Tarif Bulanan</span>
                        <span class="font-bold text-slate-900">{{ rupiah($motor->tarif->tarif_bulanan) }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 italic py-4 text-center">
                    Tarif belum ditentukan oleh admin. Setelah admin memverifikasi motor, tarif akan otomatis muncul di sini.
                </p>
            @endif
        </div>

        <!-- Ownership Document -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="file-check" class="w-5 h-5 mr-2 text-emerald-600"></i> Dokumen Kepemilikan Unit
                </h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Dokumen STNK atau BPKB tersimpan di server aman dan dienkripsi untuk privasi Anda.
                </p>
            </div>

            @if($motor->dokumen_kepemilikan)
                <a href="{{ route('files.motor_document', $motor) }}" target="_blank"
                   class="w-full py-3 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center transition border border-blue-200">
                    <i data-lucide="eye" class="w-4 h-4 mr-2"></i> Buka Dokumen STNK / BPKB
                </a>
            @else
                <div class="text-xs text-slate-400 text-center py-3">Tidak ada dokumen tersimpan.</div>
            @endif
        </div>
    </div>

    <!-- Recent Rentals on this Motor -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
            <i data-lucide="history" class="w-5 h-5 mr-2 text-emerald-600"></i> Riwayat Penyewaan Motor Ini
        </h3>

        @if($motor->penyewaans->count() > 0)
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($motor->penyewaans as $rental)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">Penyewa: {{ $rental->penyewa->nama ?? '-' }}</span>
                            <span class="text-slate-400 block">{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }} ({{ $rental->tipe_durasi }})</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">{{ rupiah($rental->harga) }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                @if($rental->status === 'selesai') bg-emerald-100 text-emerald-800
                                @elseif($rental->status === 'berjalan') bg-blue-100 text-blue-800
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ $rental->status_label }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-6 text-xs text-slate-400">Belum ada riwayat sewa untuk motor ini.</p>
        @endif
    </div>
</div>
@endsection
