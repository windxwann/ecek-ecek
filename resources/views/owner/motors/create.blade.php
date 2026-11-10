@extends('layouts.owner')

@section('page_title', 'Pendaftaran Motor Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('owner.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('owner.motors.index') }}" class="hover:text-emerald-600">Motor Saya</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Tambah Motor</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center">
                <i data-lucide="bike" class="w-6 h-6 mr-2 text-emerald-600"></i> Formulir Pendaftaran Motor
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Lengkapi informasi motor dan dokumen kepemilikan. Admin akan memverifikasi data dan menentukan tarif sewa.
            </p>
        </div>

        <form method="POST" action="{{ route('owner.motors.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Merk Motor -->
            <div>
                <label for="merk" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Merk & Model Motor <span class="text-red-500">*</span></label>
                <input id="merk" name="merk" type="text" required value="{{ old('merk') }}"
                       placeholder="Contoh: Honda Vario 125 CBS, Yamaha NMAX 155, Honda Scoopy"
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                @error('merk') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Tipe CC & Nomor Plat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tipe_cc" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Kapasitas Mesin (CC) <span class="text-red-500">*</span></label>
                    <select id="tipe_cc" name="tipe_cc" required
                            class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        <option value="">Pilih Kapasitas CC</option>
                        <option value="100" {{ old('tipe_cc') === '100' ? 'selected' : '' }}>100 CC (e.g. Beat, Scoopy)</option>
                        <option value="125" {{ old('tipe_cc') === '125' ? 'selected' : '' }}>125 CC (e.g. Vario 125, Mio M3)</option>
                        <option value="150" {{ old('tipe_cc') === '150' ? 'selected' : '' }}>150 CC (e.g. NMAX, PCX, Aerox)</option>
                    </select>
                    @error('tipe_cc') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_plat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Nomor Polisi (Plat Nomor) <span class="text-red-500">*</span></label>
                    <input id="no_plat" name="no_plat" type="text" required value="{{ old('no_plat') }}"
                           placeholder="Contoh: B 1234 ABC"
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm uppercase font-mono focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('no_plat') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Foto Motor -->
            <div>
                <label for="photo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Foto Kendaraan (Tampak Depan/Samping)</label>
                <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl cursor-pointer focus:outline-none">
                <p class="text-xs text-slate-400 mt-1">Opsional tapi sangat disarankan agar calon penyewa tertarik. Format JPG/PNG max 2MB.</p>
                @error('photo') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Dokumen Kepemilikan (STNK/BPKB) -->
            <div>
                <label for="dokumen_kepemilikan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Dokumen Kepemilikan (STNK / BPKB) <span class="text-red-500">*</span>
                </label>
                <input id="dokumen_kepemilikan" name="dokumen_kepemilikan" type="file" required accept=".pdf,image/jpeg,image/png,image/jpg"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl cursor-pointer focus:outline-none">
                <p class="text-xs text-slate-400 mt-1">Dokumen ini aman dan hanya dapat dilihat oleh admin & pemilik untuk verifikasi legalitas unit. Format PDF atau Foto max 5MB.</p>
                @error('dokumen_kepemilikan') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('owner.motors.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    &larr; Batal & Kembali
                </a>
                <button type="submit"
                        class="py-3 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition flex items-center">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i> Daftarkan Motor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
