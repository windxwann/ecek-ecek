@extends('layouts.owner')

@section('page_title', 'Pendapatan & Bagi Hasil')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendapatan & Bagi Hasil</h1>
        <p class="text-sm text-slate-500 mt-1">Laporan transparan bagi hasil rental ({{ owner_share_percent() }}% pemilik, {{ admin_share_percent() }}% platform) untuk unit motor Anda.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pendapatan</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ rupiah($totalRevenue) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sudah Ditransfer (Settled)</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ rupiah($settledRevenue) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Transfer</span>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ rupiah($pendingRevenue) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center font-bold">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Table of Profit Sharing Records -->
    @if($profitSharings->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Tanggal</th>
                            <th class="py-3.5 px-4">Motor</th>
                            <th class="py-3.5 px-4">Penyewa</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Total Sewa</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Bagian Pemilik ({{ owner_share_percent() }}%)</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Status Pencairan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($profitSharings as $record)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $profitSharings->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700 whitespace-nowrap">
                                    {{ tanggal_id($record->tanggal) }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $record->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-400 whitespace-nowrap">
                                        #{{ $record->pemesanan_id }} &bull; {{ $record->penyewaan->motor->no_plat ?? '' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-700 whitespace-nowrap">
                                    {{ $record->penyewaan->penyewa->nama ?? '-' }}
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-800 whitespace-nowrap">
                                    {{ rupiah($record->penyewaan->harga ?? 0) }}
                                </td>
                                <td class="py-4 px-4 font-bold text-emerald-600 text-sm whitespace-nowrap">
                                    {{ rupiah($record->bagi_hasil_pemilik) }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($record->isSettled())
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center whitespace-nowrap gap-1">
                                            <i data-lucide="check" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                            Ditransfer {{ tanggal_id($record->settled_at) }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center whitespace-nowrap gap-1">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                            Menunggu Transfer
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $profitSharings->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="wallet" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Catatan Bagi Hasil</h3>
            <p class="text-xs text-slate-500 mt-1">Bagi hasil akan otomatis dihitung dan tercatat setiap kali penyewaan motor selesai dikonfirmasi oleh admin.</p>
        </div>
    @endif
</div>
@endsection
