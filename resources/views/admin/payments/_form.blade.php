@php($transaksi = $transaksi ?? null)

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Penyewaan / Pesanan</label>
        <select name="pemesanan_id" required
                class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <option value="">-- Pilih Pesanan --</option>
            @foreach($rentals as $rental)
                <option value="{{ $rental->id }}"
                    {{ (string) old('pemesanan_id', $transaksi?->pemesanan_id ?? $selectedRentalId ?? '') === (string) $rental->id ? 'selected' : '' }}>
                    #{{ $rental->id }} &mdash; {{ $rental->penyewa->nama ?? '-' }}
                    &bull; {{ $rental->motor->merk ?? '-' }} ({{ $rental->motor->no_plat ?? '-' }})
                    &bull; {{ $rental->status_label }} &bull; {{ rupiah($rental->harga) }}
                </option>
            @endforeach
        </select>
        @error('pemesanan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Jumlah Pembayaran (Rp)</label>
        <input type="number" name="jumlah" min="0" step="100" required
               value="{{ old('jumlah', $transaksi?->jumlah ?? '') }}"
               class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        @error('jumlah') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Tanggal Pembayaran</label>
        <input type="datetime-local" name="tanggal" required
               value="{{ old('tanggal', optional($transaksi?->tanggal)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
               class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
        @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Metode Pembayaran</label>
        <select name="metode_pembayaran" required
                class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            @foreach(config('rental.payment_methods') as $value => $label)
                <option value="{{ $value }}" {{ old('metode_pembayaran', $transaksi?->metode_pembayaran ?? 'transfer') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('metode_pembayaran') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Status Transaksi</label>
        <select name="status" required
                class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <option value="pending" {{ old('status', $transaksi?->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="berhasil" {{ old('status', $transaksi?->status ?? 'pending') === 'berhasil' ? 'selected' : '' }}>Berhasil</option>
            <option value="gagal" {{ old('status', $transaksi?->status ?? 'pending') === 'gagal' ? 'selected' : '' }}>Gagal</option>
        </select>
        <p class="text-[11px] text-slate-400 mt-1">Status <strong>Berhasil</strong> otomatis menjadikan penyewaan berjalan dan motor disewa.</p>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Catatan Admin <span class="normal-case font-normal text-slate-400">(opsional)</span></label>
        <textarea name="catatan_admin" rows="2"
                  class="w-full px-3.5 py-2.5 border rounded-xl border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                  placeholder="Misal: pembayaran diterima tunai di kantor.">{{ old('catatan_admin', $transaksi?->catatan_admin ?? '') }}</textarea>
        @error('catatan_admin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Bukti Pembayaran <span class="normal-case font-normal text-slate-400">(opsional)</span></label>
        <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"
               class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold hover:file:bg-blue-100">
        <p class="text-[11px] text-slate-400 mt-1">Format JPG, PNG, atau PDF maksimal 4 MB.</p>
        @if($transaksi?->bukti_pembayaran)
            <a href="{{ route('files.payment_proof', $transaksi) }}" target="_blank"
               class="inline-flex items-center text-xs text-blue-600 hover:underline font-bold mt-2">
                <i data-lucide="image" class="w-3.5 h-3.5 mr-1"></i> Lihat bukti saat ini
            </a>
        @endif
        @error('bukti_pembayaran') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
