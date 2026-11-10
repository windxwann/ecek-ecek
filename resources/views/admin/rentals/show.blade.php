@extends('layouts.admin')

@section('page_title', 'Detail Penyewaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.rentals.index') }}" class="hover:text-blue-600">Penyewaan</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Pesanan #{{ $rental->id }}</span>
    </div>

    <!-- Status Header Banner -->
    <div class="rounded-3xl p-6 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
        @if($rental->status === 'selesai') bg-gradient-to-r from-emerald-600 to-teal-700
        @elseif($rental->status === 'berjalan') bg-gradient-to-r from-blue-600 to-indigo-700
        @elseif($rental->status === 'menunggu_konfirmasi') bg-gradient-to-r from-amber-500 to-orange-600
        @elseif($rental->status === 'menunggu_pembayaran') bg-gradient-to-r from-amber-600 to-yellow-600
        @else bg-gradient-to-r from-slate-600 to-slate-700 @endif">
        <div>
            <span class="text-xs uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-sm">
                Status: {{ $rental->status_label }}
            </span>
            <h1 class="text-2xl font-black mt-2">Penyewaan #{{ $rental->id }}</h1>
            <p class="text-xs text-white/80 mt-1">Dipesan pada {{ tanggal_id($rental->created_at, 'd M Y, H:i') }}</p>
        </div>

        <!-- Action Controls -->
        <div class="flex flex-wrap gap-2">
            @if($rental->status === 'berjalan')
                <form method="POST" action="{{ route('admin.rentals.return', $rental) }}"
                      onsubmit="return confirm('Konfirmasi pengembalian unit motor? Motor akan berstatus Tersedia dan bagi hasil otomatis dihitung.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-white text-emerald-700 font-bold text-xs rounded-xl shadow-md hover:bg-emerald-50 transition">
                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i> Konfirmasi Pengembalian Motor
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Rental, Motor, and Tenant Details (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Unit Motor -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="bike" class="w-5 h-5 mr-2 text-blue-600"></i> Unit Kendaraan Disewa
                </h3>

                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                        @if($rental->motor->photo)
                            <img src="{{ asset('storage/'.$rental->motor->photo) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="bike" class="w-10 h-10 text-slate-400"></i>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase">{{ $rental->motor->tipe_cc }} CC</span>
                        <h4 class="text-lg font-bold text-slate-900">{{ $rental->motor->merk }}</h4>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">Plat: <strong class="text-slate-700">{{ $rental->motor->no_plat }}</strong></p>
                        <p class="text-xs text-slate-500 mt-1">
                            Mitra Pemilik: <strong class="text-slate-700">{{ $rental->motor->pemilik->nama ?? '-' }}</strong> ({{ $rental->motor->pemilik->no_tlpn ?? '-' }})
                        </p>
                    </div>
                </div>
            </div>

            <!-- Penyewa Details -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="user" class="w-5 h-5 mr-2 text-blue-600"></i> Data Penyewa
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-400">Nama Penyewa:</span>
                        <span class="font-bold text-slate-900">{{ $rental->penyewa->nama }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-400">Alamat Email:</span>
                        <span class="font-medium text-slate-800">{{ $rental->penyewa->email }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400">Nomor Telepon:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $rental->penyewa->no_tlpn ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Rental Timeline & Cost -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 mr-2 text-blue-600"></i> Rincian Jadwal & Biaya
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Tanggal Mulai:</span>
                        <span class="font-bold text-slate-800">{{ tanggal_id($rental->tanggal_mulai) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Tanggal Selesai:</span>
                        <span class="font-bold text-slate-800">{{ tanggal_id($rental->tanggal_selesai) }}</span>
                    </div>
                    @if($rental->dikembalikan_pada)
                        <div class="flex justify-between text-emerald-700 bg-emerald-50 p-2 rounded-lg">
                            <span class="font-bold">Dikembalikan Pada:</span>
                            <span class="font-bold">{{ tanggal_id($rental->dikembalikan_pada, 'd M Y, H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-600">
                        <span>Tipe Durasi:</span>
                        <span class="font-bold text-slate-800 capitalize">{{ $rental->tipe_durasi }}</span>
                    </div>

                    @if($rental->catatan)
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-slate-400 block mb-1">Catatan Penyewa:</span>
                            <p class="text-slate-700 italic bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $rental->catatan }}</p>
                        </div>
                    @endif

                    <div class="border-t border-slate-100 pt-3 flex justify-between items-center text-sm">
                        <span class="font-bold text-slate-900">Total Harga Sewa</span>
                        <span class="text-xl font-black text-blue-600">{{ rupiah($rental->harga) }}</span>
                    </div>
                </div>
            </div>

            <!-- Cancel Action if allowed -->
            @if(!in_array($rental->status, ['selesai', 'dibatalkan']))
                <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm">
                    <h4 class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Batalkan Penyewaan</h4>
                    <p class="text-xs text-slate-500 mb-3">Batalkan pesanan ini jika terjadi kendala teknis atau atas permintaan penyewa.</p>
                    <form method="POST" action="{{ route('admin.rentals.cancel', $rental) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penyewaan ini?')">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="alasan" placeholder="Alasan pembatalan..." required
                                   class="w-full px-3 py-2 border rounded-xl border-slate-300 text-xs focus:ring-2 focus:ring-red-500 focus:outline-none">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition">
                            Proses Pembatalan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Payments & Bagi Hasil Breakdown (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Payment Transactions -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-2 text-blue-600"></i> Pembayaran Terkait
                </h3>

                @forelse($rental->transaksis as $tx)
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 text-xs space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-800">{{ $tx->metode_label }}</span>
                            <span class="px-2 py-0.5 rounded-full font-bold
                                @if($tx->status === 'berhasil') bg-emerald-100 text-emerald-800
                                @elseif($tx->status === 'gagal') bg-red-100 text-red-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $tx->status_label }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">{{ tanggal_id($tx->tanggal, 'd M Y, H:i') }}</span>
                            <span class="font-bold text-slate-900">{{ rupiah($tx->jumlah) }}</span>
                        </div>
                        @if($tx->bukti_pembayaran)
                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between whitespace-nowrap">
                                <span class="text-slate-500 whitespace-nowrap">Bukti Transfer</span>
                                <a href="{{ route('files.payment_proof', $tx) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center whitespace-nowrap">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Buka Gambar Bukti
                                </a>
                            </div>
                        @endif

                        @if($tx->status === 'pending')
                            <div class="pt-2 flex gap-2">
                                <form method="POST" action="{{ route('admin.payments.confirm', $tx) }}" class="flex-1" onsubmit="return swalConfirmPayment(this, 'Konfirmasi pembayaran #TRX-{{ $tx->id }}?', 'Transaksi akan ditandai berhasil dan status sewa akan diperbarui.')">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition">
                                        Konfirmasi Lunas
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-center py-4 text-xs text-slate-400">Belum ada transaksi pembayaran untuk sewa ini.</p>
                @endforelse
            </div>

            <!-- Bagi Hasil Record -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                    <i data-lucide="pie-chart" class="w-5 h-5 mr-2 text-emerald-600"></i> Pembagian Hasil
                </h3>

                @if($rental->bagiHasil)
                    <div class="space-y-3 text-xs">
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 flex justify-between items-center">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 block">Bagian Pemilik ({{ owner_share_percent() }}%)</span>
                                <span class="text-xs text-slate-500">{{ $rental->motor->pemilik->nama ?? '-' }}</span>
                            </div>
                            <span class="text-base font-black text-emerald-700">{{ rupiah($rental->bagiHasil->bagi_hasil_pemilik) }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-100 flex justify-between items-center">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block">Bagian Admin / Platform ({{ admin_share_percent() }}%)</span>
                                <span class="text-xs text-slate-500">Biaya Pengelolaan</span>
                            </div>
                            <span class="text-base font-black text-blue-700">{{ rupiah($rental->bagiHasil->bagi_hasil_admin) }}</span>
                        </div>

                        <div class="pt-2 flex justify-between items-center text-slate-500">
                            <span>Status Settlement:</span>
                            @if($rental->bagiHasil->isSettled())
                                <span class="font-bold text-emerald-600">Selesai ({{ tanggal_id($rental->bagiHasil->settled_at) }})</span>
                            @else
                                <form method="POST" action="{{ route('admin.reports.profit_sharing.settle', $rental->bagiHasil) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition">
                                        Tandai Ditransfer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500 leading-relaxed text-center">
                        Bagi hasil akan otomatis dihitung ({{ owner_share_percent() }}% pemilik : {{ admin_share_percent() }}% platform) setelah pengembalian motor dikonfirmasi.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function swalConfirmPayment(form, title, html) {
        event.preventDefault();
        Swal.fire({
            title: title,
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
@endpush
