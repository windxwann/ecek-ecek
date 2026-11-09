@extends('layouts.admin')

@section('page_title', 'Laporan Bagi Hasil')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Bagi Hasil (Profit Sharing)</h1>
            <p class="text-sm text-slate-500 mt-1">Rekapitulasi pembagian hasil pendapatan rental antara mitra pemilik ({{ owner_share_percent() }}%) dan platform admin ({{ admin_share_percent() }}%).</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter Date Range -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.profit_sharing') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-4 flex gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4 mr-1.5"></i> Terapkan Periode
                </button>
                <a href="{{ route('admin.reports.profit_sharing') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi Selesai</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $summary->total_count ?? 0 }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Bagian Pemilik ({{ owner_share_percent() }}%)</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ rupiah($summary->total_owner ?? 0) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Bagian Admin ({{ admin_share_percent() }}%)</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ rupiah($summary->total_admin ?? 0) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="pie-chart" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Profit Sharing Table -->
    @if($profitSharings->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Tanggal Catat</th>
                            <th class="py-3.5 px-4">ID Sewa & Motor</th>
                            <th class="py-3.5 px-4">Mitra Pemilik</th>
                            <th class="py-3.5 px-4">Penyewa</th>
                            <th class="py-3.5 px-4">Nilai Sewa</th>
                            <th class="py-3.5 px-4">Bagian Pemilik ({{ owner_share_percent() }}%)</th>
                            <th class="py-3.5 px-4">Bagian Admin ({{ admin_share_percent() }}%)</th>
                            <th class="py-3.5 px-4 text-right">Status Settlement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($profitSharings as $record)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $profitSharings->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">
                                    {{ tanggal_id($record->tanggal) }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $record->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">Sewa #{{ $record->pemesanan_id }} &bull; {{ $record->penyewaan->motor->no_plat ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800">{{ $record->penyewaan->motor->pemilik->nama ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-slate-700">
                                    {{ $record->penyewaan->penyewa->nama ?? '-' }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ rupiah($record->penyewaan->harga ?? 0) }}
                                </td>
                                <td class="py-4 px-4 font-bold text-emerald-600">
                                    {{ rupiah($record->bagi_hasil_pemilik) }}
                                </td>
                                <td class="py-4 px-4 font-bold text-blue-600">
                                    {{ rupiah($record->bagi_hasil_admin) }}
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    @if($record->isSettled())
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center">
                                            <i data-lucide="check" class="w-3.5 h-3.5 mr-1"></i> Ditransfer {{ tanggal_id($record->settled_at, 'd M Y') }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.reports.profit_sharing.settle', $record) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                                                Tandai Ditransfer
                                            </button>
                                        </form>
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
            <i data-lucide="pie-chart" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Catatan Bagi Hasil pada Periode Ini</h3>
            <p class="text-xs text-slate-500 mt-1">Ubah rentang tanggal untuk menampilkan riwayat bagi hasil lainnya.</p>
        </div>
    @endif
</div>
@endsection
