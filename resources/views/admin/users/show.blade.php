@extends('layouts.admin')

@section('page_title', 'Detail Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Pengguna</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $user->nama }}</span>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            <div class="w-20 h-20 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-black uppercase flex-shrink-0">
                {{ substr($user->nama, 0, 2) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                        @if($user->role === 'admin') bg-purple-50 text-purple-700 border border-purple-200
                        @elseif($user->role === 'pemilik') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @else bg-blue-50 text-blue-700 border border-blue-200 @endif">
                        {{ $user->role }}
                    </span>
                    @if($user->is_active)
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Nonaktif</span>
                    @endif
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-2">{{ $user->nama }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->no_tlpn ?? '-' }}</p>
            </div>
        </div>
        <div class="text-left sm:text-right text-xs text-slate-500 border-t sm:border-0 pt-4 sm:pt-0 w-full sm:w-auto">
            <p>ID Pengguna: <strong class="text-slate-800">#{{ $user->id }}</strong></p>
            <p class="mt-1">Terdaftar: <strong class="text-slate-800">{{ tanggal_id($user->created_at) }}</strong></p>
            <p class="mt-1">Email diverifikasi: <strong class="text-slate-800">{{ $user->email_verified_at ? tanggal_id($user->email_verified_at) : 'Belum' }}</strong></p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Motor Terdaftar</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $user->motors_count }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penyewaan</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $user->penyewaans_count }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                {{ $user->isPemilik() ? 'Total Bagi Hasil' : ($user->isPenyewa() ? 'Total Pembayaran Lunas' : 'Peran Sistem') }}
            </span>
            <div class="text-lg font-black {{ $user->isPemilik() ? 'text-emerald-600' : 'text-blue-600' }} mt-1">
                @if($user->isPemilik())
                    {{ rupiah($ownerRevenue) }}
                @elseif($user->isPenyewa())
                    {{ rupiah($totalSpent) }}
                @else
                    Administrator
                @endif
            </div>
        </div>
    </div>

    @if($user->isPemilik() && $motors->count() > 0)
        <!-- Owner Motors -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="bike" class="w-5 h-5 mr-2 text-blue-600"></i> Motor Milik {{ $user->nama }}
                </h3>
                <a href="{{ route('admin.motors.index', ['q' => $user->nama]) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Motor</th>
                            <th class="py-3 px-4">No. Plat</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Tarif Harian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($motors as $motor)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $motor->merk }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $motor->no_plat }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">{{ $motor->status_label }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-semibold text-slate-800">
                                    {{ $motor->tarif ? rupiah($motor->tarif->tarif_harian) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($user->isPenyewa() && $rentals->count() > 0)
        <!-- Tenant Rentals -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="clipboard-list" class="w-5 h-5 mr-2 text-blue-600"></i> Penyewaan {{ $user->nama }}
                </h3>
                <a href="{{ route('admin.rentals.index', ['q' => $user->nama]) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">ID Sewa</th>
                            <th class="py-3 px-4">Motor</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rentals as $rental)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">#{{ $rental->id }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $rental->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $rental->motor->no_plat ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">{{ $rental->status_label }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-semibold text-slate-800">{{ rupiah($rental->harga) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:underline">&larr; Kembali ke Daftar Pengguna</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
            <i data-lucide="edit" class="w-4 h-4 mr-1.5"></i> Ubah Data
        </a>
    </div>
</div>
@endsection
