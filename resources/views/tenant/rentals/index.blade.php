@extends('layouts.tenant')

@section('page_title', 'Riwayat Penyewaan Saya')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Penyewaan</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar seluruh kendaraan yang pernah atau sedang Anda sewa.</p>
        </div>
        <a href="{{ route('marketplace.index') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Sewa Motor Baru
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex overflow-x-auto pb-2 space-x-2 border-b border-slate-200 text-xs font-semibold">
        <a href="{{ route('tenant.rentals.index') }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'menunggu_pembayaran']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_pembayaran' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Pembayaran
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'menunggu_konfirmasi']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_konfirmasi' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Konfirmasi
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'menunggu_konfirmasi']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'berjalan' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dikonfirmasi
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'menunggu_konfirmasi']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'berjalan' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Berlangsung
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'selesai']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai
        </a>
        <a href="{{ route('tenant.rentals.index', ['status' => 'dibatalkan']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'dibatalkan' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Dibatalkan
        </a>
    </div>

    <!-- Rentals List -->
    @if($rentals->count() > 0)
        <div class="space-y-4">
            @foreach($rentals as $rental)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                            @if($rental->motor->photo)
                                <img src="{{ asset('storage/'.$rental->motor->photo) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="bike" class="w-8 h-8 text-slate-400"></i>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold text-blue-600 uppercase">{{ $rental->motor->tipe_cc }} CC</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs font-mono text-slate-500 font-bold">{{ $rental->motor->no_plat }}</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ $rental->motor->merk }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Periode: <strong class="text-slate-700">{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}</strong>
                                ({{ ucfirst($rental->tipe_durasi) }})
                            </p>
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto border-t sm:border-0 pt-3 sm:pt-0 gap-3">
                        <div class="sm:text-right">
                            <span class="text-xs text-slate-400 block">Total Biaya</span>
                            <span class="text-base font-extrabold text-slate-900">{{ rupiah($rental->harga) }}</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                @if($rental->status === \App\Models\Penyewaan::STATUS_COMPLETED) bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($rental->status === \App\Models\Penyewaan::STATUS_ONGOING) bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($rental->status === \App\Models\Penyewaan::STATUS_CONFIRMED) bg-blue-50 text-blue-700 border border-blue-200
                                @elseif($rental->status === \App\Models\Penyewaan::STATUS_WAITING_CONFIRMATION) bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT) bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($rental->status === \App\Models\Penyewaan::STATUS_CANCELLED) bg-red-50 text-red-600 border border-red-200
                                @endif">
                                {{ $rental->status_label }}
                            </span>

                            @if($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT)
                                <a href="{{ route('tenant.payments.create', $rental) }}"
                                   class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
                                    Bayar
                                </a>
                            @endif

                            <a href="{{ route('tenant.rentals.show', $rental) }}"
                               class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $rentals->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="calendar-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Penyewaan</h3>
            <p class="text-xs text-slate-500 mt-1">Belum ada transaksi pada kategori status ini.</p>
            <div class="mt-4">
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                    Sewa Motor Sekarang
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
