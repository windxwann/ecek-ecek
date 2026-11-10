@extends('layouts.admin')

@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data seluruh akun admin, mitra pemilik, dan penyewa dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
            <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i> Tambah Pengguna
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Role Tabs -->
        <div class="flex overflow-x-auto space-x-2 text-xs font-semibold w-full sm:w-auto">
            <a href="{{ route('admin.users.index', ['q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentRole) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $roleCounts['total'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentRole === 'admin' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Admin ({{ $roleCounts['admin'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'pemilik', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentRole === 'pemilik' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pemilik ({{ $roleCounts['pemilik'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'penyewa', 'q' => request('q')]) }}"
               class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentRole === 'penyewa' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Penyewa ({{ $roleCounts['penyewa'] ?? 0 }})
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-72 relative">
            @if($currentRole) <input type="hidden" name="role" value="{{ $currentRole }}"> @endif
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari nama, email, no HP..."
                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Email & Kontak</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="py-4 px-4">
                                <a href="{{ route('admin.users.show', $u) }}" class="font-bold text-slate-900 text-sm hover:text-blue-600 transition">{{ $u->nama }}</a>
                                <div class="text-[11px] text-slate-400">ID: #{{ $u->id }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-slate-800">{{ $u->email }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $u->no_tlpn ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                                    @if($u->role === 'admin') bg-purple-50 text-purple-700 border border-purple-200
                                    @elseif($u->role === 'pemilik') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @else bg-blue-50 text-blue-700 border border-blue-200 @endif">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($u->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200 inline-flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-500">
                                {{ tanggal_id($u->created_at) }}
                            </td>
                            <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.users.toggle_active', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition
                                        {{ $u->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.users.show', $u) }}" class="px-2.5 py-1 rounded-lg text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                                    Detail
                                </a>

                                <a href="{{ route('admin.users.edit', $u) }}" class="px-2.5 py-1 rounded-lg text-blue-600 hover:bg-blue-50 font-bold text-xs transition">
                                    Edit
                                </a>

                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-red-600 hover:bg-red-50 font-bold text-xs transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
