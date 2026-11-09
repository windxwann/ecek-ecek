@extends('layouts.auth')

@section('heading', 'Daftar Akun Baru')
@section('subheading', 'Mulai sewakan motor atau cari motor idaman Anda')

@section('content')
<form class="space-y-4" method="POST" action="{{ route('register') }}">
    @csrf

    <div>
        <label for="nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lengkap</label>
        <div class="mt-1">
            <input id="nama" name="nama" type="text" required value="{{ old('nama') }}"
                   placeholder="Contoh: Budi Santoso"
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('nama') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('nama')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Email</label>
        <div class="mt-1">
            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                   placeholder="nama@email.com"
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('email') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="no_tlpn" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor WhatsApp / HP</label>
        <div class="mt-1">
            <input id="no_tlpn" name="no_tlpn" type="text" required value="{{ old('no_tlpn') }}"
                   placeholder="081234567890"
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('no_tlpn') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('no_tlpn')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Daftar Sebagai</label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex flex-col p-3 border rounded-xl cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                <input type="radio" name="role" value="penyewa" class="sr-only" {{ old('role', request('role', 'penyewa')) === 'penyewa' ? 'checked' : '' }}>
                <span class="text-sm font-bold text-slate-900">Penyewa</span>
                <span class="text-xs text-slate-500 mt-0.5">Sewa motor untuk harian, mingguan, atau bulanan</span>
            </label>

            <label class="relative flex flex-col p-3 border rounded-xl cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                <input type="radio" name="role" value="pemilik" class="sr-only" {{ old('role', request('role')) === 'pemilik' ? 'checked' : '' }}>
                <span class="text-sm font-bold text-slate-900">Mitra Pemilik</span>
                <span class="text-xs text-slate-500 mt-0.5">Titipkan motor Anda dan dapatkan bagi hasil</span>
            </label>
        </div>
        @error('role')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
        <div class="mt-1">
            <input id="password" name="password" type="password" required
                   placeholder="Minimal 8 karakter"
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('password') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Konfirmasi Password</label>
        <div class="mt-1">
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   placeholder="Ulangi password"
                   class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
    </div>

    <div class="pt-2">
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md shadow-emerald-500/20 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
            Buat Akun Sekarang
        </button>
    </div>
</form>

<div class="mt-6 text-center text-xs text-slate-600">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">
        Masuk di sini
    </a>
</div>
@endsection
