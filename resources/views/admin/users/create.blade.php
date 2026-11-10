@extends('layouts.admin')

@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Pengguna</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Tambah Baru</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3 mb-6 flex items-center">
            <i data-lucide="user-plus" class="w-5 h-5 mr-2 text-blue-600"></i> Tambah Pengguna Baru
        </h1>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Nama Lengkap <span class="text-red-500 ml-0.5">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Alamat Email <span class="text-red-500 ml-0.5">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Nomor Telepon / WhatsApp <span class="text-red-500 ml-0.5">*</span></label>
                    <input type="text" name="no_tlpn" value="{{ old('no_tlpn') }}" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('no_tlpn') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Role / Peran <span class="text-red-500 ml-0.5">*</span></label>
                    <select name="role" required
                            class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <option value="penyewa" {{ old('role') === 'penyewa' ? 'selected' : '' }}>Penyewa</option>
                        <option value="pemilik" {{ old('role') === 'pemilik' ? 'selected' : '' }}>Mitra Pemilik</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Status Akun <span class="text-red-500 ml-0.5">*</span></label>
                    <select name="is_active" required
                            class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Password Sementara <span class="text-red-500 ml-0.5">*</span></label>
                <input type="password" name="password" required minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:underline">
                    &larr; Batal & Kembali
                </a>
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
