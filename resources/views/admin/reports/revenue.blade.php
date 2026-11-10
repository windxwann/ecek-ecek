@extends('layouts.admin')

@section('page_title', 'Laporan Pendapatan Platform')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-blue-400 border border-emerald-500 mb-3">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1.5"></i> Laporan Pendapatan
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Laporan Pendapatan Platform</h1>
            <p class="text-slate-400 text-sm mt-1 max-w-xl">
                Rekapitulasi seluruh transaksi pembayaran sewa yang telah diverifikasi lunas.
            </p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-emerald-500 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter Date Range -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.revenue') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
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
                <a href="{{ route('admin.reports.revenue') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Total Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Pendapatan Terverifikasi</span>
            <div class="text-3xl font-black mt-1">{{ rupiah($totalRevenue) }}</div>
            <p class="text-xs text-slate-500 mt-1">Periode: {{ tanggal_id($startDate) }} s/d {{ tanggal_id($endDate) }} ({{ $transactions->total() }} transaksi)</p>
        </div>
        <div class="inline-flex w-14 h-14 rounded-2xl border border-emerald-500 flex items-center justify-center text-emerald-500 backdrop-blur-sm">
            <i data-lucide="trending-up" class="w-7 h-7"></i>
        </div>
    </div>

    <!-- Transactions Table -->
    @if($transactions->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Waktu Transaksi</th>
                            <th class="py-3.5 px-4">ID Transaksi & Sewa</th>
                            <th class="py-3.5 px-4">Penyewa</th>
                            <th class="py-3.5 px-4">Unit Motor</th>
                            <th class="py-3.5 px-4">Metode Bayar</th>
                            <th class="py-3.5 px-4 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $transactions->firstItem() + $loop->index }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    {{ tanggal_id($tx->tanggal, 'd M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    #TRX-{{ $tx->id }}
                                    <div class="text-[10px] text-slate-400 font-normal">Sewa #{{ $tx->pemesanan_id }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $tx->penyewaan->penyewa->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $tx->penyewaan->penyewa->email ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $tx->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $tx->penyewaan->motor->no_plat ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $tx->metode_label }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 text-sm text-right">
                                    {{ rupiah($tx->jumlah) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="receipt" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Pendapatan pada Periode Ini</h3>
            <p class="text-xs text-slate-500 mt-1">Ubah filter tanggal untuk melihat transaksi periode lainnya.</p>
        </div>
    @endif
</div>
@endsection
