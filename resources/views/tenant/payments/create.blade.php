@extends('layouts.tenant')

@section('page_title', 'Konfirmasi Pembayaran Sewa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('tenant.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('tenant.rentals.show', $rental) }}" class="hover:text-blue-600">Penyewaan #{{ $rental->id }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Kirim Pembayaran</span>
    </div>

    <!-- Booking Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-blue-600 uppercase">Penyewaan #{{ $rental->id }}</span>
            <h2 class="text-lg font-bold text-slate-900 mt-0.5">{{ $rental->motor->merk }} ({{ $rental->motor->no_plat }})</h2>
            <p class="text-xs text-slate-500 mt-1">
                Periode: <strong class="text-slate-700">{{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}</strong>
            </p>
        </div>
        <div class="text-left sm:text-right border-t sm:border-0 pt-3 sm:pt-0 w-full sm:w-auto">
            <span class="text-xs text-slate-400 block">Total yang Harus Dibayar</span>
            <span class="text-2xl font-black text-blue-600">{{ rupiah($rental->harga) }}</span>
        </div>
    </div>

    <!-- Payment Instruction & Form -->
    <form method="POST" action="{{ route('tenant.payments.store', $rental) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Payment Method Selection -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center">
                <i data-lucide="wallet" class="w-5 h-5 mr-2 text-blue-600"></i> Pilih Rekening / Metode Pembayaran
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Bank Transfer Option -->
                <label class="p-4 border-2 rounded-2xl cursor-pointer transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-slate-900 flex items-center">
                            <i data-lucide="building-2" class="w-4 h-4 mr-2 text-blue-600"></i> Transfer
                        </span>
                        <input type="radio" name="metode_pembayaran" value="transfer" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                    </div>
                    <div class="text-xs text-slate-600 space-y-1 pt-2 border-t border-slate-100">
                        <div>Bank: <strong class="text-slate-900">{{ $bank['name'] }}</strong></div>
                        <div>No. Rekening: <strong class="font-mono text-sm text-blue-600">{{ $bank['account'] }}</strong></div>
                        <div>Atas Nama: <strong class="text-slate-900">{{ $bank['holder'] }}</strong></div>
                    </div>
                </label>

                <!-- E-Wallet Option -->
                <label class="p-4 border-2 rounded-2xl cursor-pointer transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-slate-900 flex items-center">
                            <i data-lucide="wallet" class="w-4 h-4 mr-2 text-emerald-600"></i> E-Wallet
                        </span>
                        <input type="radio" name="metode_pembayaran" value="ewallet" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                    </div>
                    <div class="text-xs text-slate-600 space-y-1 pt-2 border-t border-slate-100">
                        <p class="leading-relaxed">GoPay, OVO, Dana, ShopeePay, atau mobile banking.</p>
                        <p class="text-[11px] text-slate-400">Merchant: {{ $bank['holder'] }}</p>
                    </div>
                </label>

                <!-- Cash Option -->
                <label class="p-4 border-2 rounded-2xl cursor-pointer transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-slate-900 flex items-center">
                            <i data-lucide="banknote" class="w-4 h-4 mr-2 text-amber-600"></i> Tunai
                        </span>
                        <input type="radio" name="metode_pembayaran" value="tunai" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                    </div>
                    <div class="text-xs text-slate-600 space-y-1 pt-2 border-t border-slate-100">
                        <p class="leading-relaxed">Bayar langsung di kantor admin, lalu unggah bukti penerimaan.</p>
                    </div>
                </label>
            </div>
            @error('metode_pembayaran') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Upload Proof -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center">
                <i data-lucide="upload-cloud" class="w-5 h-5 mr-2 text-blue-600"></i> Unggah Bukti Transfer / Pembayaran
            </h3>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Foto / Screenshot Bukti Struk</label>
                <input type="file" name="bukti_pembayaran" required accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl cursor-pointer focus:outline-none">
                <p class="text-xs text-slate-400 mt-1.5">Format: JPG, PNG, atau WEBP. Maksimal ukuran 3MB.</p>
                @error('bukti_pembayaran') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('tenant.rentals.show', $rental) }}" class="text-xs font-bold text-slate-600 hover:underline">
                &larr; Kembali ke Detail Sewa
            </a>
            <button type="submit"
                    class="py-3 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition flex items-center">
                <i data-lucide="send" class="w-4 h-4 mr-2"></i> Kirim Bukti Pembayaran
            </button>
        </div>
    </form>
</div>
@endsection
