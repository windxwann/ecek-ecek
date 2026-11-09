@extends('layouts.tenant')

@section('page_title', 'Riwayat Pembayaran')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Pembayaran</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar transaksi pembayaran yang telah Anda kirimkan.</p>
    </div>

    @if($payments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">ID & Tanggal</th>
                            <th class="py-3.5 px-4">Motor / Sewa</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Jumlah</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Bukti</th>
                            <th class="py-3.5 px-4 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 text-center font-bold text-slate-500 text-xs">
                                    {{ $payments->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-800">
                                    <div class="font-bold">#TRX-{{ $payment->id }}</div>
                                    <div class="text-[11px] text-slate-400">{{ tanggal_id($payment->tanggal, 'd M Y, H:i') }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $payment->penyewaan->motor->merk ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">Sewa #{{ $payment->pemesanan_id }}</div>
                                </td>
                                <td class="py-4 px-4 text-slate-700 font-medium">
                                    {{ $payment->metode_label }}
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900 text-sm">
                                    {{ rupiah($payment->jumlah) }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($payment->status === 'berhasil') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($payment->status === 'gagal') bg-red-50 text-red-600 border border-red-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                        {{ $payment->status_label }}
                                    </span>
                                    @if($payment->catatan_admin)
                                        <div class="text-[10px] text-red-600 mt-0.5 max-w-xs truncate" title="{{ $payment->catatan_admin }}">
                                            {{ $payment->catatan_admin }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($payment->bukti_pembayaran)
                                        <a href="{{ route('files.payment_proof', $payment) }}" target="_blank"
                                           class="inline-flex items-center text-blue-600 hover:underline font-semibold text-[11px]">
                                            <i data-lucide="image" class="w-3.5 h-3.5 mr-1"></i> Struk
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ route('tenant.rentals.show', $payment->pemesanan_id) }}" class="text-blue-600 font-bold hover:underline">
                                        Lihat Sewa
                                    </a>
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
            <h3 class="text-base font-bold text-slate-800">Belum Ada Transaksi Pembayaran</h3>
            <p class="text-xs text-slate-500 mt-1">Pembayaran akan tercatat setelah Anda melakukan pemesanan sewa motor.</p>
        </div>
    @endif
</div>
@endsection
