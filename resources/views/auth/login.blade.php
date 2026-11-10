
@extends('layouts.auth')

@section('heading', 'Masuk ke RideGo')
@section('subheading', 'Kelola kendaraan atau cari motor sewaan favorit Anda')

@section('content')
<form class="space-y-5" method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
            Email <span class="text-red-500">*</span>
        </label>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                placeholder="Masukkan alamat email Anda"
                required
                value="{{ old('email') }}"
                class="w-full pl-11 pr-4 py-3 border @error('email') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
            >
        </div>

        @error('email')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
            Password <span class="text-red-500">*</span>
        </label>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M7 10V7a5 5 0 0110 0v3m-11 0h12a2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a2 2 0 012-2z"/>
                </svg>
            </div>

            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="Masukkan password Anda"
                required
                class="w-full pl-11 pr-12 py-3 border @error('password') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
            >

            {{-- Tombol tampil/sembunyikan password --}}
            <button
                type="button"
                onclick="togglePassword()"
                class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-emerald-600 transition"
                aria-label="Tampilkan password"
                id="togglePasswordButton"
            >
                {{-- Ikon mata --}}
                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/>
                    <circle cx="12" cy="12" r="3" stroke-width="1.7"/>
                </svg>

                {{-- Ikon mata dicoret --}}
                <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.7 10.7 0 0112 5c6 0 9.5 7 9.5 7a14.5 14.5 0 01-3.2 4.1M6.2 6.2C3.8 7.9 2.5 12 2.5 12s3.5 7 9.5 7c1 0 1.9-.2 2.8-.5"/>
                </svg>
            </button>
        </div>

        @error('password')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember Me --}}
    <div class="flex items-center justify-between text-xs">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
            >
            <span class="text-slate-600">Ingat saya</span>
        </label>
    </div>

    {{-- Submit --}}
    <button
        type="submit"
        class="w-full py-3 px-4 rounded-xl shadow-md shadow-emerald-500/20 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all active:scale-[0.98]"
    >
        Masuk Sekarang
    </button>
</form>

{{-- Demo Account --}}
<div class="mt-6 pt-6 border-t border-slate-100">
    <p class="text-xs font-semibold text-slate-500 mb-3 text-center uppercase tracking-wider">
        Akun Demo Pengujian
    </p>

    <div class="grid grid-cols-3 gap-2">
        <button type="button"
            onclick="fillLogin('admin@motorrent.test', 'password')"
            class="px-2 py-2.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition text-center">
            <span class="block font-bold text-slate-800">Admin</span>
        </button>

        <button type="button"
            onclick="fillLogin('budi.pemilik@motorrent.test', 'password')"
            class="px-2 py-2.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 transition text-center">
            <span class="block font-bold text-emerald-600">Pemilik</span>
        </button>

        <button type="button"
            onclick="fillLogin('andi.penyewa@motorrent.test', 'password')"
            class="px-2 py-2.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-teal-50 hover:border-teal-200 transition text-center">
            <span class="block font-bold text-teal-600">Penyewa</span>
        </button>
    </div>
</div>

{{-- Register --}}
<div class="mt-6 text-center text-xs text-slate-600">
    Belum punya akun?
    <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">
        Daftar gratis di sini
    </a>
</div>

<script>
    // Tampilkan atau sembunyikan password
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        const toggleButton = document.getElementById('togglePasswordButton');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
            toggleButton.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
            toggleButton.setAttribute('aria-label', 'Tampilkan password');
        }
    }

    // Mengisi akun demo secara otomatis
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
