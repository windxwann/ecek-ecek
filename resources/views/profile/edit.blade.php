@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isPemilik() ? 'layouts.owner' : 'layouts.tenant'))

@section('page_title', 'Profil Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Profil</h2>
        <p class="text-sm text-slate-500 mt-1">Perbarui informasi profil dan keamanan akun Anda.</p>
    </div>

    <!-- Informasi Profil -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center">
            <i data-lucide="user" class="w-5 h-5 mr-2 text-blue-600"></i> Informasi Pribadi
        </h3>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="no_tlpn" value="{{ old('no_tlpn', $user->no_tlpn) }}" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('no_tlpn') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Role / Peran</label>
                    <input type="text" value="{{ strtoupper($user->role) }}" disabled
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm font-semibold cursor-not-allowed">
                </div>
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Ubah Password -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center">
            <i data-lucide="lock" class="w-5 h-5 mr-2 text-blue-600"></i> Ganti Password
        </h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
