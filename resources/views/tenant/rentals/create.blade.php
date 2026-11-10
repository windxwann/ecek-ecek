@extends('layouts.tenant')

@section('page_title', 'Form Pemesanan Sewa Motor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6"
     x-data="rentalBooking({
         quoteUrl: '{{ route('tenant.rentals.quote', $motor) }}',
         csrf: '{{ csrf_token() }}',
         dailyRate: {{ $motor->tarif?->tarif_harian ?? 0 }},
         weeklyRate: {{ $motor->tarif?->tarif_mingguan ?? 0 }},
         monthlyRate: {{ $motor->tarif?->tarif_bulanan ?? 0 }},
     })">

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('tenant.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('marketplace.show', $motor) }}" class="hover:text-blue-600">{{ $motor->merk }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Formulir Sewa</span>
    </div>

    <!-- Motor Info Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                @if($motor->photo)
                    <img src="{{ asset('storage/'.$motor->photo) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="bike" class="w-8 h-8 text-slate-400"></i>
                @endif
            </div>
            <div>
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">{{ $motor->tipe_cc }} CC</span>
                <h2 class="text-lg font-extrabold text-slate-900">{{ $motor->merk }}</h2>
                <p class="text-xs text-slate-400 font-mono">{{ $motor->no_plat }} &bull; Mitra: {{ $motor->pemilik->nama ?? 'Mitra' }}</p>
            </div>
        </div>

        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto border-t sm:border-0 pt-3 sm:pt-0">
            <span class="text-xs text-slate-400">Tarif Harian</span>
            <span class="text-lg font-black text-blue-600">{{ rupiah($motor->tarif?->tarif_harian ?? 0) }}<span class="text-xs font-normal text-slate-400">/hari</span></span>
        </div>
    </div>

    <!-- Booking Form -->
    <form method="POST" action="{{ route('tenant.rentals.store', $motor) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <!-- Form Inputs (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="calendar" class="w-5 h-5 mr-2 text-blue-600"></i> Rincian Waktu Sewa
            </h3>

            <!-- Tipe Durasi -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Tipe Durasi Sewa <span class="text-red-500 ml-0.5">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="p-3 border rounded-xl cursor-pointer text-center transition"
                           :class="tipeDurasi === 'harian' ? 'border-blue-600 bg-blue-50/50 text-blue-700 font-bold' : 'border-slate-200 text-slate-700'">
                        <input type="radio" name="tipe_durasi" value="harian" class="sr-only" x-model="tipeDurasi" @change="calculate()">
                        <span class="text-xs block font-bold">Harian</span>
                        <span class="text-[10px] text-slate-500 font-normal">Per 24 Jam</span>
                    </label>

                    <label class="p-3 border rounded-xl cursor-pointer text-center transition"
                           :class="tipeDurasi === 'mingguan' ? 'border-blue-600 bg-blue-50/50 text-blue-700 font-bold' : 'border-slate-200 text-slate-700'">
                        <input type="radio" name="tipe_durasi" value="mingguan" class="sr-only" x-model="tipeDurasi" @change="calculate()">
                        <span class="text-xs block font-bold">Mingguan</span>
                        <span class="text-[10px] text-slate-500 font-normal">Paket 7 Hari</span>
                    </label>

                    <label class="p-3 border rounded-xl cursor-pointer text-center transition"
                           :class="tipeDurasi === 'bulanan' ? 'border-blue-600 bg-blue-50/50 text-blue-700 font-bold' : 'border-slate-200 text-slate-700'">
                        <input type="radio" name="tipe_durasi" value="bulanan" class="sr-only" x-model="tipeDurasi" @change="calculate()">
                        <span class="text-xs block font-bold">Bulanan</span>
                        <span class="text-[10px] text-slate-500 font-normal">Paket 30 Hari</span>
                    </label>
                </div>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Tanggal Mulai <span class="text-red-500 ml-0.5">*</span></label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" required
                           min="{{ date('Y-m-d') }}"
                           x-model="tanggalMulai" @change="calculate()"
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('tanggal_mulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Tanggal Selesai <span class="text-red-500 ml-0.5">*</span></label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" required
                           :min="tanggalMulai || '{{ date('Y-m-d') }}'"
                           x-model="tanggalSelesai" @change="calculate()"
                           class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    @error('tanggal_selesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea id="catatan" name="catatan" rows="3"
                          placeholder="Misal: Perlu tambahan helm untuk penumpang, atau perkiraan jam pengambilan unit..."
                          class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
            </div>
        </div>

        <!-- Summary & Calculation (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center mb-4">
                    <i data-lucide="receipt" class="w-5 h-5 mr-2 text-blue-600"></i> Ringkasan Biaya
                </h3>

                <!-- Warning when not available -->
                <div x-show="errorMessage" x-cloak class="p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs mb-4">
                    <span class="font-bold block">Periode Tidak Tersedia:</span>
                    <span x-text="errorMessage"></span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Tipe Durasi</span>
                        <span class="font-semibold text-slate-800 uppercase text-xs" x-text="tipeDurasi"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Durasi Terhitung</span>
                        <span class="font-semibold text-slate-800" x-text="unitLabel || '-'"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Tarif Satuan</span>
                        <span class="font-semibold text-slate-800" x-text="formattedRate"></span>
                    </div>

                    <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                        <span class="font-bold text-slate-900">Total Estimasi</span>
                        <span class="text-xl font-black text-blue-600" x-text="formattedTotal"></span>
                    </div>
                </div>

                <div class="mt-6 p-3.5 bg-blue-50/70 rounded-xl border border-blue-100 text-xs text-blue-800 space-y-1">
                    <div class="font-bold flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-1"></i> Informasi Pembayaran
                    </div>
                    <p class="leading-relaxed text-[11px] text-blue-700">
                        Setelah menekan tombol buat pemesanan, Anda akan diarahkan ke halaman transfer untuk menyelesaikan pembayaran dan mengunggah bukti transaksi.
                    </p>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit"
                        :disabled="loading || isOverlapping || !tanggalMulai || !tanggalSelesai"
                        class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold text-sm shadow-md transition flex items-center justify-center">
                    <span x-show="!loading">Lanjutkan Pembayaran</span>
                    <span x-show="loading">Memeriksa Ketersediaan...</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function rentalBooking(config) {
    return {
        tipeDurasi: 'harian',
        tanggalMulai: '{{ date('Y-m-d') }}',
        tanggalSelesai: '{{ date('Y-m-d', strtotime('+1 day')) }}',
        loading: false,
        isOverlapping: false,
        errorMessage: '',
        unitLabel: '1 hari',
        formattedRate: 'Rp ' + Number(config.dailyRate).toLocaleString('id-ID'),
        formattedTotal: 'Rp ' + Number(config.dailyRate).toLocaleString('id-ID'),

        init() {
            this.calculate();
        },

        async calculate() {
            if (!this.tanggalMulai || !this.tanggalSelesai) return;

            this.loading = true;
            this.errorMessage = '';
            this.isOverlapping = false;

            try {
                const response = await fetch(config.quoteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        tanggal_mulai: this.tanggalMulai,
                        tanggal_selesai: this.tanggalSelesai,
                        tipe_durasi: this.tipeDurasi,
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    this.isOverlapping = true;
                    this.errorMessage = result.message || 'Periode sewa ini bertabrakan atau tidak valid.';
                    this.formattedTotal = '-';
                } else {
                    this.unitLabel = result.quote.unit_label;
                    this.formattedRate = 'Rp ' + Number(result.quote.rate).toLocaleString('id-ID');
                    this.formattedTotal = result.formatted_total;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
@endsection
