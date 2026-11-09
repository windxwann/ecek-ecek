@extends('layouts.admin')

@section('page_title', 'Laporan Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Pembayaran</h1>
            <p class="text-sm text-slate-500 mt-1">Rekapitulasi seluruh transaksi pembayaran sewa motor berdasarkan periode dan status.</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter Date Range & Status -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.payments') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="berhasil" {{ $currentStatus === 'berhasil' ? 'selected' : '' }}>Lunas</option>
                    <option value="gagal" {{ $currentStatus === 'gagal' ? 'selected' : '' }}>Gagal</option>
                    <option value="gagal" {{ $currentStatus === 'gagal' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4 mr-1.5"></i> Terapkan
                </button>
                <a href="{{ route('admin.reports.payments') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $summary['total_count'] }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Lunas</span>
            <div class="text-lg font-black text-emerald-600 mt-1">{{ rupiah($summary['total_paid']) }}</div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $summary['paid_count'] }} transaksi</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
            <div class="text-2xl font-black text-amber-500 mt-1">{{ $summary['pending_count'] }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ditolak / Gagal</span>
            <div class="text-2xl font-black text-red-500 mt-1">{{ $summary['rejected_count'] + $summary['failed_count'] }}</div>
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
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500">
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
                                    <div class="text-[11px] text-slate-400">{{ $tx->penyewaan->penyewa->no_tlpn ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $tx->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $tx->penyewaan->motor->no_plat ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">{{ $tx->metode_label }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($tx->status === 'berhasil') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($tx->status === 'gagal' || $tx->status === 'gagal') bg-red-50 text-red-600 border border-red-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                        {{ $tx->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 text-sm text-right">{{ rupiah($tx->jumlah) }}</td>
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
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Data Pembayaran</h3>
            <p class="text-xs text-slate-500 mt-1">Ubah filter periode atau status untuk menampilkan transaksi lainnya.</p>
        </div>
    @endif
</div>
@endsection
