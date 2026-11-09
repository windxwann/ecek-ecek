@extends('layouts.admin')

@section('page_title', 'Kelola Penyewaan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Penyewaan</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau seluruh siklus pemesanan sewa motor, konfirmasi pesanan, dan penyelesaian pengembalian.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Status Tabs -->
        <div class="flex overflow-x-auto space-x-2 text-xs font-semibold w-full sm:w-auto">
            <a href="{{ route('admin.rentals.index', ['q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.rentals.index', ['status' => 'menunggu_konfirmasi', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_konfirmasi' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menunggu Konfirmasi ({{ $counts['waiting'] }})
            </a>
            <a href="{{ route('admin.rentals.index', ['status' => 'berjalan', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'berjalan' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Berjalan ({{ $counts['berjalan'] }})
            </a>
            <a href="{{ route('admin.rentals.index', ['status' => 'selesai', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Selesai ({{ $counts['selesai'] }})
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('admin.rentals.index') }}" class="w-full sm:w-72 relative">
            @if($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari penyewa, motor, plat..."
                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
        </form>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">ID & Tanggal</th>
                        <th class="py-3.5 px-4">Penyewa</th>
                        <th class="py-3.5 px-4">Unit Motor</th>
                        <th class="py-3.5 px-4">Periode Sewa</th>
                        <th class="py-3.5 px-4">Total Biaya</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rentals as $rental)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                {{ $rentals->firstItem() + $loop->index }}
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-900">
                                #{{ $rental->id }}
                                <div class="text-[10px] text-slate-400 font-normal">{{ tanggal_id($rental->created_at) }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900">{{ $rental->penyewa->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $rental->penyewa->no_tlpn ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">{{ $rental->motor->merk }}</div>
                                <div class="text-[11px] font-mono text-slate-500 font-bold">{{ $rental->motor->no_plat }} &bull; {{ $rental->motor->pemilik->nama ?? 'Mitra' }}</div>
                            </td>
                            <td class="py-4 px-4 text-slate-600">
                                <div>{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}</div>
                                <div class="text-[11px] text-slate-400 capitalize">{{ $rental->tipe_durasi }}</div>
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-900 text-sm">
                                {{ rupiah($rental->harga) }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                    @if($rental->status === 'selesai') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($rental->status === 'berjalan') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($rental->status === 'berjalan') bg-indigo-50 text-indigo-700 border border-indigo-200
                                    @elseif($rental->status === 'menunggu_konfirmasi') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($rental->status === 'dibatalkan') bg-red-50 text-red-600 border border-red-200
                                    @else bg-slate-100 text-slate-600 @endif">
                                    {{ $rental->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                @if($rental->status === 'menunggu_konfirmasi')
                                    <form method="POST" action="{{ route('admin.rentals.confirm', $rental) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                                            Konfirmasi
                                        </button>
                                    </form>
                                @elseif($rental->status === 'berjalan')
                                    <form method="POST" action="{{ route('admin.rentals.return', $rental) }}" class="inline"
                                          onsubmit="return confirm('Konfirmasi pengembalian unit motor? Status sewa akan ditandai selesai dan sistem otomatis mencatat bagi hasil.')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                                            Pengembalian
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.rentals.show', $rental) }}"
                                   class="px-2.5 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold text-xs transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data penyewaan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $rentals->links() }}
        </div>
    </div>
</div>
@endsection
