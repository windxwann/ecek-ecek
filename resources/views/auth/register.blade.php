
@extends('layouts.auth')

@section('heading', 'Daftar Akun Baru')
@section('subheading', 'Mulai sewakan motor atau cari motor idaman Anda')

@section('content')
<form class="space-y-5" method="POST" action="{{ route('register') }}">
    @csrf

    {{-- Nama Lengkap --}}
    <div>
        <label for="nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
            Nama Lengkap <span class="text-red-500">*</span>
        </label>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M20 21a8 8 0 00-16 0m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>

            <input id="nama" name="nama" type="text" required
                value="{{ old('nama') }}"
                placeholder="Contoh: Budi Santoso"
                autocomplete="name"
                class="w-full pl-11 pr-4 py-3 border @error('nama') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
        </div>

        @error('nama')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email dan Nomor HP --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                Alamat Email <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <input id="email" name="email" type="email" required
                    autocomplete="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    class="w-full pl-11 pr-3 py-3 border @error('email') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
            </div>

            @error('email')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="no_tlpn" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                Nomor WhatsApp / HP <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm4 14h6"/>
                    </svg>
                </div>

                <input id="no_tlpn" name="no_tlpn" type="tel" required
                    autocomplete="tel"
                    value="{{ old('no_tlpn') }}"
                    placeholder="081234567890"
                    class="w-full pl-11 pr-3 py-3 border @error('no_tlpn') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
            </div>

            @error('no_tlpn')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Pilihan Role --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
            Daftar Sebagai <span class="text-red-500">*</span>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

            {{-- Penyewa --}}
            <label class="relative flex items-start gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-emerald-400 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                <input type="radio" name="role" value="penyewa"
                    class="sr-only"
                    {{ old('role', request('role', 'penyewa')) === 'penyewa' ? 'checked' : '' }}>

                <div class="w-10 h-10 shrink-0 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M5 17h14l-1.5-7h-11L5 17zm2-7l2-5h6l2 5M8 17v2m8-2v2"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <span class="block text-sm font-bold text-slate-900">Penyewa</span>
                    <span class="block text-xs text-slate-500 mt-1 leading-relaxed">
                        Sewa motor harian, mingguan, atau bulanan
                    </span>
                </div>
            </label>

            {{-- Pemilik --}}
            <label class="relative flex items-start gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-emerald-400 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                <input type="radio" name="role" value="pemilik"
                    class="sr-only"
                    {{ old('role', request('role')) === 'pemilik' ? 'checked' : '' }}>

                <div class="w-10 h-10 shrink-0 flex items-center justify-center rounded-lg bg-teal-100 text-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6h-6v6H4a1 1 0 01-1-1v-9z"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <span class="block text-sm font-bold text-slate-900">Mitra Pemilik</span>
                    <span class="block text-xs text-slate-500 mt-1 leading-relaxed">
                        Titipkan motor dan dapatkan bagi hasil
                    </span>
                </div>
            </label>

        </div>

        @error('role')
            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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

                <input id="password" name="password" type="password" required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full pl-11 pr-11 py-3 border @error('password') border-red-300 bg-red-50/50 @else border-slate-300 @enderror rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">

                <button type="button" onclick="togglePassword('password', 'eyePassword', 'eyeOffPassword')"
                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-emerald-600 transition"
                    aria-label="Tampilkan password">
                    <svg id="eyePassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="eyeOffPassword" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.7 10.7 0 0112 5c6 0 9.5 7 9.5 7a14.5 14.5 0 01-3.2 4.1M6.2 6.2C3.8 7.9 2.5 12 2.5 12s3.5 7 9.5 7c1 0 1.9-.2 2.8-.5"/>
                    </svg>
                </button>
            </div>

            @error('password')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M7 10V7a5 5 0 0110 0v3m-11 0h12a2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a2 2 0 012-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M9 15l2 2 4-4"/>
                    </svg>
                </div>

                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full pl-11 pr-11 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">

                <button type="button" onclick="togglePassword('password_confirmation', 'eyeConfirm', 'eyeOffConfirm')"
                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-emerald-600 transition"
                    aria-label="Tampilkan konfirmasi password">
                    <svg id="eyeConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="eyeOffConfirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.7 10.7 0 0112 5c6 0 9.5 7 9.5 7a14.5 14.5 0 01-3.2 4.1M6.2 6.2C3.8 7.9 2.5 12 2.5 12s3.5 7 9.5 7c1 0 1.9-.2 2.8-.5"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <button type="submit"
        class="w-full py-3 px-4 rounded-xl shadow-md shadow-emerald-500/20 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all active:scale-[0.98]">
        Buat Akun Sekarang
    </button>
</form>

{{-- Login --}}
<div class="mt-6 pt-6 border-t border-slate-100">
    <div class="text-center text-xs text-slate-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">
            Masuk di sini
        </a>
    </div>
</div>

<script>
    // Tampilkan atau sembunyikan password
    function togglePassword(inputId, eyeId, eyeOffId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        const eyeOffIcon = document.getElementById(eyeOffId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
</script>
@endsection
