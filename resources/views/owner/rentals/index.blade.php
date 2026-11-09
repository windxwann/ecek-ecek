@extends('layouts.owner')

@section('page_title', 'Riwayat Penyewaan Motor')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Penyewaan Motor</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar transaksi penyewaan atas seluruh unit motor yang Anda miliki.</p>
    </div>

    <!-- Filter Tabs -->
    <div class="flex overflow-x-auto pb-2 space-x-2 border-b border-slate-200 text-xs font-semibold">
        <a href="{{ route('owner.rentals.index') }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua
        </a>
        <a href="{{ route('owner.rentals.index', ['status' => 'berjalan']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'berjalan' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Berjalan
        </a>
        <a href="{{ route('owner.rentals.index', ['status' => 'menunggu_pembayaran']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_pembayaran' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Pembayaran
        </a>
        <a href="{{ route('owner.rentals.index', ['status' => 'selesai']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Selesai
        </a>
        <a href="{{ route('owner.rentals.index', ['status' => 'menunggu_konfirmasi']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_konfirmasi' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Konfirmasi
        </a>
    </div>

    @if($rentals->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Motor</th>
                            <th class="py-3.5 px-4">Penyewa</th>
                            <th class="py-3.5 px-4">Periode Sewa</th>
                            <th class="py-3.5 px-4">Total Sewa</th>
                            <th class="py-3.5 px-4">Bagian Pemilik</th>
                            <th class="py-3.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rentals as $rental)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $rentals->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $rental->motor->merk }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $rental->motor->no_plat }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800">{{ $rental->penyewa->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $rental->penyewa->no_tlpn ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    <div>{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}</div>
                                    <div class="text-[11px] text-slate-400 capitalize">{{ $rental->tipe_durasi }}</div>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    {{ rupiah($rental->harga) }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($rental->bagiHasil)
                                        <div class="font-bold text-emerald-600">{{ rupiah($rental->bagiHasil->bagi_hasil_pemilik) }}</div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $rental->bagiHasil->isSettled() ? 'Sudah Ditransfer' : 'Tercatat' }}
                                        </div>
                                    @else
                                        <div class="text-slate-400 italic">Dihitung saat selesai</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        $statusClass = match($rental->status) {
                                            'selesai'            => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'berjalan'           => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'menunggu_pembayaran'=> 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                                            'dibatalkan'         => 'bg-red-50 text-red-600 border border-red-200',
                                            default              => 'bg-amber-50 text-amber-700 border border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusClass }}">
                                        {{ $rental->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $rentals->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="calendar" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Riwayat Penyewaan</h3>
            <p class="text-xs text-slate-500 mt-1">Transaksi sewa atas motor Anda akan tampil di halaman ini.</p>
        </div>
    @endif
</div>
@endsection
