@extends('layouts.owner')

@section('page_title', 'Ubah Data Motor')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('owner.motors.index') }}" class="hover:text-emerald-600">Motor Saya</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Ubah {{ $motor->merk }}</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center">
                <i data-lucide="edit" class="w-6 h-6 mr-2 text-emerald-600"></i> Edit Informasi Motor
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Perbarui data motor. Jika sebelumnya ditolak, memperbarui data akan mengirimkan kembali pengajuan verifikasi ke admin.
            </p>
        </div>

        <form method="POST" action="{{ route('owner.motors.update', $motor) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Merk Motor -->
            <div>
                <label for="merk" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Merk & Model Motor</label>
                <input id="merk" name="merk" type="text" required value="{{ old('merk', $motor->merk) }}"
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                @error('merk') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Tipe CC & Nomor Plat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tipe_cc" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Kapasitas Mesin (CC)</label>
                    <select id="tipe_cc" name="tipe_cc" required
                            class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        <option value="100" {{ old('tipe_cc', $motor->tipe_cc) === '100' ? 'selected' : '' }}>100 CC</option>
                        <option value="125" {{ old('tipe_cc', $motor->tipe_cc) === '125' ? 'selected' : '' }}>125 CC</option>
                        <option value="150" {{ old('tipe_cc', $motor->tipe_cc) === '150' ? 'selected' : '' }}>150 CC</option>
                    </select>
                    @error('tipe_cc') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_plat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nomor Polisi (Plat Nomor)</label>
                    <input id="no_plat" name="no_plat" type="text" required value="{{ old('no_plat', $motor->no_plat) }}"
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm uppercase font-mono focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('no_plat') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Foto Motor -->
            <div>
                <label for="photo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Foto Kendaraan (Ganti Jika Diperlukan)</label>
                @if($motor->photo)
                    <div class="mb-2 w-24 h-24 rounded-xl overflow-hidden border border-slate-200">
                        <img src="{{ asset('storage/'.$motor->photo) }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl cursor-pointer focus:outline-none">
                <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti foto saat ini.</p>
                @error('photo') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Dokumen Kepemilikan -->
            <div>
                <label for="dokumen_kepemilikan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Dokumen Kepemilikan (STNK / BPKB)
                </label>
                @if($motor->dokumen_kepemilikan)
                    <div class="mb-2 text-xs">
                        <a href="{{ route('files.motor_document', $motor) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center">
                            <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> Lihat Dokumen yang Tersimpan
                        </a>
                    </div>
                @endif
                <input id="dokumen_kepemilikan" name="dokumen_kepemilikan" type="file" accept=".pdf,image/jpeg,image/png,image/jpg"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl cursor-pointer focus:outline-none">
                <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti dokumen yang tersimpan.</p>
                @error('dokumen_kepemilikan') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('owner.motors.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    &larr; Batal & Kembali
                </a>
                <button type="submit"
                        class="py-3 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
