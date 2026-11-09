@extends('layouts.admin')

@section('page_title', 'Detail & Verifikasi Motor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.motors.index') }}" class="hover:text-blue-600">Motor & Verifikasi</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $motor->merk }} ({{ $motor->no_plat }})</span>
    </div>

    <!-- Motor Info Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            <div class="w-24 h-24 rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                @if($motor->photo)
                    <img src="{{ asset('storage/'.$motor->photo) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="bike" class="w-12 h-12 text-slate-400"></i>
                @endif
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-blue-600 uppercase">{{ $motor->tipe_cc }} CC</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $motor->no_plat }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1">{{ $motor->merk }}</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Mitra Pemilik: <strong class="text-slate-800">{{ $motor->pemilik->nama ?? '-' }}</strong> ({{ $motor->pemilik->no_tlpn ?? '-' }})
                </p>
            </div>
        </div>

        <div class="flex flex-col items-start sm:items-end w-full sm:w-auto border-t sm:border-0 pt-4 sm:pt-0">
            <span class="text-xs text-slate-400 mb-1">Status Saat Ini:</span>
            <span class="px-3 py-1.5 rounded-full text-xs font-bold
                @if($motor->status === 'tersedia') bg-emerald-50 text-emerald-700 border border-emerald-200
                @elseif($motor->status === 'disewa') bg-slate-900 text-white border border-slate-900
                @elseif($motor->status === 'menunggu_verifikasi') bg-amber-50 text-amber-700 border border-amber-200
                @elseif($motor->status === 'ditolak') bg-red-50 text-red-600 border border-red-200
                @else bg-slate-100 text-slate-600 @endif">
                {{ $motor->status_label }}
            </span>
        </div>
    </div>

    <!-- Verification Document Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900 flex items-center">
                <i data-lucide="file-check" class="w-5 h-5 mr-2 text-blue-600"></i> Dokumen Kepemilikan (STNK / BPKB)
            </h3>
            <p class="text-xs text-slate-500 mt-1">Periksa keabsahan nomor plat, nama pemilik, dan masa berlaku pajak STNK.</p>
        </div>
        @if($motor->dokumen_kepemilikan)
            <a href="{{ route('files.motor_document', $motor) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center transition border border-blue-200">
                <i data-lucide="external-link" class="w-4 h-4 mr-1.5"></i> Buka Dokumen STNK / BPKB
            </a>
        @else
            <span class="text-xs text-red-500 font-medium">Mitra belum mengunggah dokumen.</span>
        @endif
    </div>

    <!-- Verification Form Action Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-5 flex items-center">
            <i data-lucide="check-square" class="w-5 h-5 mr-2 text-blue-600"></i> Proses Verifikasi & Tarif Awal
        </h3>

        <form method="POST" action="{{ route('admin.motors.verify', $motor) }}" class="space-y-5">
            @csrf

            <!-- Approval / Rejection Radio -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-2">Keputusan Verifikasi</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="p-4 border-2 rounded-2xl cursor-pointer transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-900 flex items-center">
                                <i data-lucide="check" class="w-4 h-4 mr-2 text-emerald-600"></i> Setujui (Approve)
                            </span>
                            <input type="radio" name="action" value="approve" checked class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Motor akan berstatus Tersedia dan tampil di katalog publik.</p>
                    </label>

                    <label class="p-4 border-2 rounded-2xl cursor-pointer transition has-[:checked]:border-red-600 has-[:checked]:bg-red-50/40">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-900 flex items-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2 text-red-600"></i> Tolak (Reject)
                            </span>
                            <input type="radio" name="action" value="reject" class="h-4 w-4 text-red-600 focus:ring-red-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Motor ditolak dan catatan alasan akan dikirimkan ke pemilik.</p>
                    </label>
                </div>
            </div>

            <!-- Catatan Admin -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Catatan Admin</label>
                <textarea name="catatan_admin" rows="2"
                          placeholder="Misal: Dokumen valid, motor siap disewakan. Atau sebutkan alasan jika ditolak..."
                          class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('catatan_admin', $motor->catatan_admin) }}</textarea>
            </div>

            <!-- Set Initial Pricing if approved -->
            <div class="pt-3 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase text-slate-700 mb-2">Tetapkan Tarif Rental</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Harian (Rp)</label>
                        <input type="number" name="tarif_harian" value="{{ old('tarif_harian', $motor->tarif?->tarif_harian ?? 80000) }}" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Mingguan (Rp)</label>
                        <input type="number" name="tarif_mingguan" value="{{ old('tarif_mingguan', $motor->tarif?->tarif_mingguan ?? 480000) }}" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Bulanan (Rp)</label>
                        <input type="number" name="tarif_bulanan" value="{{ old('tarif_bulanan', $motor->tarif?->tarif_bulanan ?? 1600000) }}" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
                    Simpan Keputusan Verifikasi
                </button>
            </div>
        </form>
    </div>

    <!-- Rental Pricing Management Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
            <i data-lucide="tags" class="w-5 h-5 mr-2 text-blue-600"></i> Manajemen Tarif Rental
        </h3>

        @if($motor->tarif)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider block">Harian</span>
                    <span class="font-bold text-slate-900 text-sm">{{ rupiah($motor->tarif->tarif_harian) }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider block">Mingguan</span>
                    <span class="font-bold text-slate-900 text-sm">{{ rupiah($motor->tarif->tarif_mingguan) }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-400 font-semibold uppercase tracking-wider block">Bulanan</span>
                    <span class="font-bold text-slate-900 text-sm">{{ rupiah($motor->tarif->tarif_bulanan) }}</span>
                </div>
            </div>
        @else
            <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5">
                Motor ini belum memiliki tarif rental. Tetapkan tarif agar motor dapat disewakan.
            </p>
        @endif

        <form method="POST" action="{{ route('admin.motors.pricing', $motor) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Harian (Rp)</label>
                    <input type="number" name="tarif_harian" value="{{ old('tarif_harian', $motor->tarif?->tarif_harian ?? 80000) }}" min="1000" step="1000" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Mingguan (Rp)</label>
                    <input type="number" name="tarif_mingguan" value="{{ old('tarif_mingguan', $motor->tarif?->tarif_mingguan ?? 480000) }}" min="1000" step="1000" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-1 font-medium">Tarif Bulanan (Rp)</label>
                    <input type="number" name="tarif_bulanan" value="{{ old('tarif_bulanan', $motor->tarif?->tarif_bulanan ?? 1600000) }}" min="1000" step="1000" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                @if($motor->tarif)
                    <button type="button"
                            onclick="if (confirm('Hapus tarif rental motor ini? Motor akan dinonaktifkan dari katalog.')) { document.getElementById('delete-pricing-{{ $motor->id }}').submit(); }"
                            class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs border border-red-200 transition">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-1 inline"></i> Hapus Tarif
                    </button>
                @else
                    <span></span>
                @endif

                <button type="submit" class="py-2.5 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
                    Simpan Tarif Rental
                </button>
            </div>
        </form>

        @if($motor->tarif)
            <form id="delete-pricing-{{ $motor->id }}" method="POST" action="{{ route('admin.motors.pricing.destroy', $motor) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <!-- Manual Status Change Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
            <i data-lucide="settings" class="w-5 h-5 mr-2 text-slate-600"></i> Pengaturan Status Manual
        </h3>

        <form method="POST" action="{{ route('admin.motors.status', $motor) }}" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <select name="status" class="w-full sm:w-64 px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <option value="tersedia" {{ $motor->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="disewa" {{ $motor->status === 'disewa' ? 'selected' : '' }}>Disewa</option>
                <option value="menunggu_verifikasi" {{ $motor->status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="perawatan" {{ $motor->status === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                <option value="ditolak" {{ $motor->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="w-full sm:w-auto py-2.5 px-5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                Perbarui Status
            </button>
        </form>
    </div>
</div>
@endsection
