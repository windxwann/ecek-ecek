@extends('layouts.admin')

@section('page_title', 'Kelola Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Verifikasi Pembayaran</h1>
            <p class="text-sm text-slate-500 mt-1">Periksa bukti transfer dari penyewa dan konfirmasi kelayakan transaksi sewa.</p>
        </div>
        <a href="{{ route('admin.payments.create') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
            <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Entri Pembayaran Manual
        </a>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex overflow-x-auto pb-2 space-x-2 border-b border-slate-200 text-xs font-semibold">
        <a href="{{ route('admin.payments.index') }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua ({{ $counts['total'] }})
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Menunggu Verifikasi ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'berhasil']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'berhasil' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Lunas / Diterima ({{ $counts['berhasil'] }})
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'gagal']) }}"
           class="px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ $currentStatus === 'gagal' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Ditolak ({{ $counts['gagal'] }})
        </a>
    </div>

    <!-- Payments Table -->
    @if($payments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">ID Transaksi & Waktu</th>
                            <th class="py-3.5 px-4">Penyewa</th>
                            <th class="py-3.5 px-4">Motor & No. Sewa</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Jumlah Transfer</th>
                            <th class="py-3.5 px-4">Bukti Struk</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-center">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payments as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $payments->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">
                                    #TRX-{{ $tx->id }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ tanggal_id($tx->tanggal, 'd M Y, H:i') }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $tx->penyewaan->penyewa->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $tx->penyewaan->penyewa->no_tlpn ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800">{{ $tx->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">Sewa #{{ $tx->pemesanan_id }} &bull; {{ $tx->penyewaan->motor->no_plat ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-800">
                                    {{ $tx->metode_label }}
                                </td>
                                <td class="py-4 px-4 font-black text-slate-900 text-sm">
                                    {{ rupiah($tx->jumlah) }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($tx->bukti_pembayaran)
                                        <a href="{{ route('files.payment_proof', $tx) }}" target="_blank"
                                           class="inline-flex items-center whitespace-nowrap text-blue-600 hover:underline font-bold text-xs bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">
                                            <i data-lucide="image" class="w-3.5 h-3.5 mr-1"></i> Buka Struk
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic whitespace-nowrap">Tanpa Struk</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($tx->status === 'berhasil') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($tx->status === 'gagal') bg-red-50 text-red-600 border border-red-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                        {{ $tx->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    @if($tx->status === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.confirm', $tx) }}" class="inline" onsubmit="return swalConfirmPayment(this, 'Konfirmasi pembayaran #TRX-{{ $tx->id }}?', 'Transaksi akan ditandai berhasil dan status sewa akan diperbarui.')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                                                Konfirmasi
                                            </button>
                                        </form>

                                        <div class="inline" x-data="{ open: false }">
                                            <button @click="open = true" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition border border-red-200">
                                                Tolak
                                            </button>

                                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
                                                <div @click.outside="open = false" class="bg-white rounded-2xl max-w-sm w-full p-5 text-left space-y-4">
                                                    <h3 class="text-sm font-bold text-slate-900">Alasan Penolakan Pembayaran</h3>
                                                    <form method="POST" action="{{ route('admin.payments.reject', $tx) }}">
                                                        @csrf
                                                        <textarea name="catatan" rows="3" placeholder="Misal: Jumlah transfer tidak sesuai atau gambar buram..." required
                                                                  class="w-full p-3 border rounded-xl text-xs border-slate-300 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                                                        <div class="mt-3 flex justify-end gap-2">
                                                            <button type="button" @click="open = false" class="px-3 py-1.5 text-xs text-slate-500 font-bold">Batal</button>
                                                            <button type="submit" class="px-4 py-1.5 text-xs bg-red-600 text-white font-bold rounded-lg">Tolak Pembayaran</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($tx->status === 'pending')
                                        <a href="{{ route('admin.payments.edit', $tx) }}"
                                           class="inline-flex px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>

                                        <div class="inline" x-data="{ open: false }">
                                            <button @click="open = true" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition border border-red-200">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>

                                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
                                                <div @click.outside="open = false" class="bg-white rounded-2xl max-w-sm w-full p-5 text-left space-y-4">
                                                    <h3 class="text-sm font-bold text-slate-900">Hapus Transaksi #TRX-{{ $tx->id }}?</h3>
                                                    <p class="text-xs text-slate-500">Data pembayaran dan bukti struk akan dihapus permanen.</p>
                                                    <form method="POST" action="{{ route('admin.payments.destroy', $tx) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="flex justify-end gap-2">
                                                            <button type="button" @click="open = false" class="px-3 py-1.5 text-xs text-slate-500 font-bold">Batal</button>
                                                            <button type="submit" class="px-4 py-1.5 text-xs bg-red-600 text-white font-bold rounded-lg">Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            <i data-lucide="receipt" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Transaksi Pembayaran</h3>
            <p class="text-xs text-slate-500 mt-1">Pembayaran yang masuk akan muncul di sini untuk verifikasi admin.</p>
        </div>
    @endif
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
