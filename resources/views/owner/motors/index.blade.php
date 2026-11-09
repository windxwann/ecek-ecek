@extends('layouts.owner')

@section('page_title', 'Kelola Motor Saya')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Motor Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar kendaraan yang telah Anda daftarkan ke sistem RideGo.</p>
        </div>
        <a href="{{ route('owner.motors.create') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Motor Baru
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex overflow-x-auto pb-2 space-x-2 border-b border-slate-200 text-xs font-semibold">
        <a href="{{ route('owner.motors.index') }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua Motor
        </a>
        <a href="{{ route('owner.motors.index', ['status' => 'menunggu_verifikasi']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('owner.motors.index', ['status' => 'tersedia']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'tersedia' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Tersedia
        </a>
        <a href="{{ route('owner.motors.index', ['status' => 'disewa']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'disewa' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Sedang Disewa
        </a>
        <a href="{{ route('owner.motors.index', ['status' => 'ditolak']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'ditolak' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Ditolak
        </a>
    </div>

    <!-- Table of Motors -->
    @if($motors->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Unit Kendaraan</th>
                            <th class="py-3.5 px-4">CC & Plat</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Tarif Rental (Admin)</th>
                            <th class="py-3.5 px-4">Dokumen</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($motors as $motor)
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
                                            <div class="text-[11px] text-slate-400">Ditambahkan {{ tanggal_id($motor->created_at) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-baseline space-x-2">
                                        <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $motor->no_plat }}</span>
                                        <span class="text-[11px] text-slate-500 font-semibold">{{ $motor->tipe_cc }} CC</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        $motorStatusClass = match($motor->status) {
                                            'tersedia'            => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'disewa'              => 'bg-slate-900 text-white border border-slate-900',
                                            'menunggu_verifikasi' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'ditolak'             => 'bg-red-50 text-red-600 border border-red-200',
                                            'perawatan'           => 'bg-orange-50 text-orange-700 border border-orange-200',
                                            default               => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-[11px] font-bold {{ $motorStatusClass }}">
                                            {{ $motor->status_label }}
                                        </span>
                                        @if($motor->catatan_admin)
                                            <div class="relative group flex-shrink-0">
                                                <button type="button" class="text-slate-400 hover:text-amber-500 transition">
                                                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <div class="absolute left-0 top-5 z-20 hidden group-hover:block w-56 bg-slate-800 text-white text-[11px] rounded-lg px-3 py-2 shadow-lg leading-relaxed">
                                                    <span class="font-semibold text-amber-300">Catatan Admin:</span><br>
                                                    {{ $motor->catatan_admin }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($motor->tarif)
                                        <div class="font-bold text-slate-900">{{ rupiah($motor->tarif->tarif_harian) }}<span class="text-[10px] font-normal text-slate-400">/hr</span></div>
                                    @else
                                        <span class="text-slate-400 italic">Belum ada tarif</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($motor->dokumen_kepemilikan)
                                        <a href="{{ route('files.motor_document', $motor) }}" target="_blank"
                                           class="inline-flex items-center text-xs text-blue-600 hover:underline font-bold">
                                            <i data-lucide="file" class="w-3.5 h-3.5 mr-1"></i> Lihat Dokumen
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Belum ada dokumen</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="inline-flex items-center gap-2 justify-center">
                                        <a href="{{ route('owner.motors.edit', $motor) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5 mr-1"></i> Ubah
                                        </a>

                                        <form method="POST" action="{{ route('owner.motors.destroy', $motor) }}"
                                              onsubmit="return confirm('Yakin menghapus motor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition border border-red-200">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4 border-t border-slate-100">
                    {{ $motors->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="bike" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Motor</h3>
            <p class="text-xs text-slate-500 mt-1">Tambah motor pertama Anda melalui form di atas.</p>
        </div>
    @endif
</div>
@endsection