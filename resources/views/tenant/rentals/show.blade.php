@extends('layouts.tenant')

@section('page_title', 'Detail Pemesanan Sewa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('tenant.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('tenant.rentals.index') }}" class="hover:text-blue-600">Riwayat Sewa</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Penyewaan #{{ $rental->id }}</span>
    </div>

    <!-- Status Header Banner -->
    <div class="rounded-3xl p-6 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
        @if($rental->status === \App\Models\Penyewaan::STATUS_COMPLETED) bg-gradient-to-r from-emerald-600 to-teal-700
        @elseif($rental->status === \App\Models\Penyewaan::STATUS_ONGOING) bg-gradient-to-r from-blue-600 to-indigo-700
        @elseif($rental->status === \App\Models\Penyewaan::STATUS_CONFIRMED) bg-gradient-to-r from-blue-500 to-indigo-600
        @elseif($rental->status === \App\Models\Penyewaan::STATUS_WAITING_CONFIRMATION) bg-gradient-to-r from-amber-500 to-orange-600
        @elseif($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT) bg-gradient-to-r from-amber-600 to-yellow-600
        @elseif($rental->status === \App\Models\Penyewaan::STATUS_CANCELLED) bg-gradient-to-r from-slate-600 to-slate-700
        @endif">
        <div>
            <span class="text-xs uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-sm">
                Status: {{ $rental->status_label }}
            </span>
            <h1 class="text-2xl font-black mt-2">Pemesanan Sewa #{{ $rental->id }}</h1>
            <p class="text-xs text-white/80 mt-1">Dibuat pada {{ tanggal_id($rental->created_at, 'd M Y, H:i') }}</p>
        </div>

        @if($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT)
            <a href="{{ route('tenant.payments.create', $rental) }}"
               class="px-5 py-2.5 rounded-2xl bg-white text-amber-700 font-bold text-sm shadow-md hover:bg-amber-50 transition">
                <i data-lucide="credit-card" class="w-4 h-4 inline mr-1.5"></i> Bayar Sekarang
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Motor & Rental Details (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Motor Details -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="bike" class="w-5 h-5 mr-2 text-blue-600"></i> Unit Kendaraan
                </h3>

                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                        @if($rental->motor->photo)
                            <img src="{{ asset('storage/'.$rental->motor->photo) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="bike" class="w-10 h-10 text-slate-400"></i>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase">{{ $rental->motor->tipe_cc }} CC</span>
                        <h4 class="text-lg font-bold text-slate-900">{{ $rental->motor->merk }}</h4>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">Nomor Plat: <strong class="text-slate-700">{{ $rental->motor->no_plat }}</strong></p>
                        <p class="text-xs text-slate-500 mt-1">Pemilik: {{ $rental->motor->pemilik->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Rental Period & Cost -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 mr-2 text-blue-600"></i> Waktu & Rincian Sewa
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Tanggal Mulai</span>
                        <span class="font-bold text-slate-800">{{ tanggal_id($rental->tanggal_mulai) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Tanggal Selesai</span>
                        <span class="font-bold text-slate-800">{{ tanggal_id($rental->tanggal_selesai) }}</span>
                    </div>
                    @if($rental->dikembalikan_pada)
                        <div class="flex justify-between text-emerald-600">
                            <span>Waktu Dikembalikan</span>
                            <span class="font-bold">{{ tanggal_id($rental->dikembalikan_pada, 'd M Y, H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-600">
                        <span>Tipe Durasi</span>
                        <span class="font-bold text-slate-800 capitalize">{{ $rental->tipe_durasi }}</span>
                    </div>

                    @if($rental->catatan)
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-xs text-slate-400 block mb-1">Catatan Pemesanan:</span>
                            <p class="text-xs text-slate-700 italic bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $rental->catatan }}</p>
                        </div>
                    @endif

                    <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                        <span class="font-bold text-slate-900">Total Biaya Sewa</span>
                        <span class="text-xl font-black text-blue-600">{{ rupiah($rental->harga) }}</span>
                    </div>
                </div>
            </div>

            <!-- Cancel Action if allowed -->
            @if(in_array($rental->status, [\App\Models\Penyewaan::STATUS_PENDING_PAYMENT, \App\Models\Penyewaan::STATUS_WAITING_CONFIRMATION]))
                <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm">
                    <h4 class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Batalkan Pesanan</h4>
                    <p class="text-xs text-slate-500 mb-3">Jika Anda berubah pikiran atau ingin memesan unit/jadwal lain, Anda dapat membatalkan pesanan ini.</p>
                    <form method="POST" action="{{ route('tenant.rentals.cancel', $rental) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penyewaan ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl text-xs font-bold transition">
                            Batalkan Pemesanan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Payment Transactions History (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span class="flex items-center"><i data-lucide="receipt" class="w-5 h-5 mr-2 text-blue-600"></i> Riwayat Pembayaran</span>
                @if($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT)
                    <a href="{{ route('tenant.payments.create', $rental) }}" class="text-xs font-bold text-blue-600 hover:underline">
                        Upload Bukti
                    </a>
                @endif
            </h3>

            @if($rental->transaksis->count() > 0)
                <div class="space-y-3">
                    @foreach($rental->transaksis as $tx)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">{{ $tx->metode_label }}</span>
                                <span class="px-2 py-0.5 rounded-full font-bold
                                    @if($tx->status === 'berhasil') bg-emerald-100 text-emerald-800
                                    @elseif($tx->status === 'gagal') bg-red-100 text-red-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $tx->status_label }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400">{{ tanggal_id($tx->tanggal, 'd M Y, H:i') }}</span>
                                <span class="font-bold text-slate-900">{{ rupiah($tx->jumlah) }}</span>
                            </div>

                            @if($tx->bukti_pembayaran)
                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Bukti Transfer</span>
                                    <a href="{{ route('files.payment_proof', $tx) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Lihat Bukti
                                    </a>
                                </div>
                            @endif

                            @if($tx->catatan_admin)
                                <p class="text-xs text-red-600 bg-red-50 p-2 rounded-lg border border-red-100">
                                    <strong>Catatan Admin:</strong> {{ $tx->catatan_admin }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-slate-400">
                    <i data-lucide="clock" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                    Belum ada bukti pembayaran yang dikirim.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
