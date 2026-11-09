@extends('layouts.auth')

@section('heading', 'Masuk ke RideGo')
@section('subheading', 'Kelola kendaraan atau cari motor sewaan favorit Anda')

@section('content')
<form class="space-y-5" method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Email</label>
        <div class="mt-1.5 relative">
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('email') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="flex items-center justify-between">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
        </div>
        <div class="mt-1.5 relative">
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   class="appearance-none block w-full px-3.5 py-2.5 border @error('password') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox"
                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
            <label for="remember" class="ml-2 block text-xs text-slate-600">Ingat saya</label>
        </div>
    </div>

    <div>
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md shadow-emerald-500/20 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
            Masuk Sekarang
        </button>
    </div>
</form>

<!-- Quick Demo Login Helper for Testing -->
<div class="mt-6 pt-6 border-t border-slate-100">
    <p class="text-xs font-semibold text-slate-500 mb-2 text-center uppercase tracking-wider">Akun Demo Pengujian:</p>
    <div class="grid grid-cols-3 gap-2">
        <button type="button" onclick="fillLogin('admin@motorrent.test', 'password')"
                class="px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition text-center">
            <span class="block font-bold text-slate-800">Admin</span>
        </button>
        <button type="button" onclick="fillLogin('budi.pemilik@motorrent.test', 'password')"
                class="px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition text-center">
            <span class="block font-bold text-emerald-600">Pemilik</span>
        </button>
        <button type="button" onclick="fillLogin('andi.penyewa@motorrent.test', 'password')"
                class="px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition text-center">
            <span class="block font-bold text-teal-600">Penyewa</span>
        </button>
    </div>
</div>

<div class="mt-6 text-center text-xs text-slate-600">
    Belum punya akun?
    <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">
        Daftar gratis di sini
    </a>
</div>

<script>
function fillLogin(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
