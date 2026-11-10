@extends('layouts.tenant')

@section('page_title', 'Tenant Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5"></i> Portal Penyewa
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Halo, {{ $user->nama }}!</h1>
            <p class="text-blue-100 text-sm mt-1 max-w-xl">
                Temukan motor pilihanmu atau pantau penyewaan yang sedang aktif dengan mudah.
            </p>
        </div>
        <a href="{{ route('marketplace.index') }}"
           class="inline-flex items-center px-5 py-3 rounded-2xl bg-white text-blue-600 font-bold text-sm shadow-md hover:bg-blue-50 transition">
            <i data-lucide="search" class="w-4 h-4 mr-2"></i> Cari Motor Lain
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Sewa</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $counts['total_rentals'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sewa Aktif</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $counts['active_rentals'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Bayar</span>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ $counts['pending_payments'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center font-bold">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengeluaran</span>
                <div class="text-lg font-black text-slate-900 mt-1">{{ rupiah($counts['total_spent']) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Active Rentals Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center">
                <i data-lucide="activity" class="w-5 h-5 mr-2 text-blue-600"></i> Penyewaan Aktif & Berjalan
            </h2>
            <a href="{{ route('tenant.rentals.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Lihat Semua
            </a>
        </div>

        @if($activeRentals->count() > 0)
            <div class="space-y-3">
                @foreach($activeRentals as $rental)
                    <div class="p-4 rounded-xl border border-slate-200/90 hover:border-slate-300 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                @if($rental->motor->photo)
                                    <img src="{{ asset('storage/'.$rental->motor->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="bike" class="w-7 h-7 text-slate-400"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $rental->motor->merk }}</h3>
                                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $rental->motor->no_plat }} &bull; {{ $rental->motor->tipe_cc }} CC</p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Periode: <strong class="text-slate-700">{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-0 pt-3 sm:pt-0">
                            <div>
                                <span class="text-xs text-slate-400 block text-right">Total Biaya</span>
                                <span class="text-sm font-bold text-slate-900">{{ rupiah($rental->harga) }}</span>
                            </div>

                            @if($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT)
                                <a href="{{ route('tenant.payments.create', $rental) }}"
                                   class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
                                    Bayar Sekarang
                                </a>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($rental->status === \App\Models\Penyewaan::STATUS_WAITING_CONFIRMATION) bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($rental->status === \App\Models\Penyewaan::STATUS_CONFIRMED) bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($rental->status === \App\Models\Penyewaan::STATUS_ONGOING) bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @endif">
                                    {{ $rental->status_label }}
                                </span>
                            @endif

                            <a href="{{ route('tenant.rentals.show', $rental) }}"
                               class="p-2 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="Detail">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-400 text-xs">
                Tidak ada sewa aktif saat ini. <a href="{{ route('marketplace.index') }}" class="text-blue-600 font-bold hover:underline">Sewa motor sekarang</a>.
            </div>
        @endif
    </div>

    <!-- Recent History -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center">
            <i data-lucide="history" class="w-5 h-5 mr-2 text-slate-400"></i> Riwayat Penyewaan Terakhir
        </h2>

        @if($recentRentals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Motor</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Durasi</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentRentals as $rental)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $rental->motor->merk }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $rental->motor->no_plat }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 capitalize">
                                    {{ $rental->tipe_durasi }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ rupiah($rental->harga) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold
                                        @if($rental->status === \App\Models\Penyewaan::STATUS_COMPLETED) bg-emerald-50 text-emerald-700
                                        @elseif($rental->status === \App\Models\Penyewaan::STATUS_CANCELLED) bg-red-50 text-red-600
                                        @elseif($rental->status === \App\Models\Penyewaan::STATUS_PENDING_PAYMENT) bg-amber-50 text-amber-700
                                        @else bg-blue-50 text-blue-700 @endif">
                                        {{ $rental->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('tenant.rentals.show', $rental) }}" class="text-blue-600 font-bold hover:underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center py-6 text-xs text-slate-400">Belum ada riwayat penyewaan.</p>
        @endif
    </div>
</div>
@endsection
