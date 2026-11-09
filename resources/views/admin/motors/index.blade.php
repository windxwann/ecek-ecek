@extends('layouts.admin')

@section('page_title', 'Kelola Motor & Verifikasi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Motor & Verifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">Periksa pendaftaran motor baru dari mitra pemilik, kelola tarif, dan pantau ketersediaan unit.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Status Tabs -->
        <div class="flex overflow-x-auto space-x-2 text-xs font-semibold w-full sm:w-auto">
            <a href="{{ route('admin.motors.index', ['q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.motors.index', ['status' => 'menunggu_verifikasi', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menunggu Verifikasi ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.motors.index', ['status' => 'tersedia', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'tersedia' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Tersedia ({{ $counts['tersedia'] }})
            </a>
            <a href="{{ route('admin.motors.index', ['status' => 'disewa', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'disewa' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Disewa ({{ $counts['disewa'] }})
            </a>
            <a href="{{ route('admin.motors.index', ['status' => 'ditolak', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'ditolak' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Ditolak
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('admin.motors.index') }}" class="w-full sm:w-72 relative">
            @if($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari merk, plat, pemilik..."
                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
        </form>
    </div>

    <!-- Motors Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Unit Kendaraan</th>
                        <th class="py-3.5 px-4">Mitra Pemilik</th>
                        <th class="py-3.5 px-4">Plat & CC</th>
                        <th class="py-3.5 px-4">Tarif (H/M/B)</th>
                        <th class="py-3.5 px-4">Dokumen</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($motors as $motor)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                {{ $motors->firstItem() + $loop->index }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        @if($motor->photo)
                                            <img src="{{ asset('storage/'.$motor->photo) }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="bike" class="w-6 h-6 text-slate-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $motor->merk }}</div>
                                        <div class="text-[11px] text-slate-400">Didaftarkan {{ tanggal_id($motor->created_at) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">{{ $motor->pemilik->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $motor->pemilik->no_tlpn ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $motor->no_plat }}</span>
                                <div class="text-[11px] text-slate-500 mt-0.5 font-semibold">{{ $motor->tipe_cc }} CC</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($motor->tarif)
                                    <div class="font-bold text-slate-900">{{ rupiah($motor->tarif->tarif_harian) }}<span class="text-[10px] font-normal text-slate-400">/hr</span></div>
                                    <div class="text-[11px] text-slate-500">{{ rupiah($motor->tarif->tarif_mingguan) }}/mg</div>
                                @else
                                    <span class="text-xs text-amber-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($motor->dokumen_kepemilikan)
                                    <a href="{{ route('files.motor_document', $motor) }}" target="_blank"
                                       class="inline-flex items-center text-blue-600 hover:underline font-semibold text-[11px]">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 mr-1"></i> STNK/BPKB
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                    @if($motor->status === 'tersedia') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($motor->status === 'disewa') bg-slate-900 text-white border border-slate-900
                                    @elseif($motor->status === 'menunggu_verifikasi') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($motor->status === 'ditolak') bg-red-50 text-red-600 border border-red-200
                                    @else bg-slate-100 text-slate-600 @endif">
                                    {{ $motor->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.motors.show', $motor) }}"
                                   class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition">
                                    {{ $motor->status === 'menunggu_verifikasi' ? 'Verifikasi & Tarif' : 'Detail & Tarif' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data motor yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $motors->links() }}
        </div>
    </div>
</div>
@endsection
