<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran #TRX-{{ $payment->id }} – RideGo</title>

    <!-- Outfit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .print-shadow { box-shadow: none !important; }
            @page {
                margin: 1cm;
                size: A5 portrait;
            }
        }
    </style>
</head>
<body class="bg-emerald-100 min-h-screen flex flex-col items-center justify-start py-8 px-4" style="font-family: 'Outfit', sans-serif;">

    <!-- Action Bar (tidak tercetak) -->
    <div class="no-print w-full max-w-sm mb-5 flex items-center justify-between gap-3">
        <a href="{{ route('tenant.rentals.show', $payment->penyewaan) }}"
           class="inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-900 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Receipt Card -->
    <div class="bg-white w-full max-w-sm rounded-3xl shadow-xl print-shadow overflow-hidden">

        <!-- Header Hijau -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 px-6 py-7 text-white text-center relative">
            <!-- Decorative circles -->
            <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/5 rounded-full"></div>

            <!-- Logo -->
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm mb-3 relative z-10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c0 0-4 2-4 7s4 11 4 11 4-6 4-11-4-7-4-7z"/>
                    <circle cx="12" cy="10" r="2"/>
                </svg>
            </div>

            <div class="relative z-10">
                <p class="text-emerald-100 text-xs font-semibold uppercase tracking-widest">RideGo Platform</p>
                <h1 class="text-2xl font-black mt-0.5 tracking-tight">Struk Pembayaran</h1>
                <p class="text-emerald-100 text-xs mt-1">#TRX-{{ $payment->id }}</p>
            </div>

            <!-- Status Badge -->
            <div class="mt-4 relative z-10">
                @if($payment->status === \App\Models\Transaksi::STATUS_SUCCESS)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-white text-emerald-700 font-bold text-xs">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Pembayaran Berhasil
                    </span>
                @elseif($payment->status === \App\Models\Transaksi::STATUS_FAILED)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-100 text-red-700 font-bold text-xs">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        Pembayaran Gagal/Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-bold text-xs">
                        <svg class="w-3.5 h-3.5 mr-1 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4m8-8h-4M4 12H0m15.5-6.5l-2.8 2.8M7.3 16.7l-2.8 2.8M19.5 18.5l-2.8-2.8M7.3 7.3L4.5 4.5"/></svg>
                        Menunggu Verifikasi
                    </span>
                @endif
            </div>
        </div>

        <!-- Zigzag Divider -->
        <div class="relative h-5 bg-white">
            <div class="absolute top-0 left-0 right-0 overflow-hidden h-5">
                <svg viewBox="0 0 400 20" class="w-full h-full" preserveAspectRatio="none">
                    <path d="M0,0 L10,10 L20,0 L30,10 L40,0 L50,10 L60,0 L70,10 L80,0 L90,10 L100,0 L110,10 L120,0 L130,10 L140,0 L150,10 L160,0 L170,10 L180,0 L190,10 L200,0 L210,10 L220,0 L230,10 L240,0 L250,10 L260,0 L270,10 L280,0 L290,10 L300,0 L310,10 L320,0 L330,10 L340,0 L350,10 L360,0 L370,10 L380,0 L390,10 L400,0 L400,0 Z"
                          fill="rgb(5,150,105)" />
                </svg>
            </div>
        </div>

        <!-- Body Struk -->
        <div class="px-6 pb-6 space-y-5 bg-white">

            <!-- Total Bayar -->
            <div class="text-center py-4">
                <p class="text-xs text-emerald-400 uppercase font-bold tracking-wider">Total Pembayaran</p>
                <p class="text-4xl font-black text-emerald-900 mt-1 tracking-tight">{{ rupiah($payment->jumlah) }}</p>
                <p class="text-xs text-emerald-400 mt-1">{{ tanggal_id($payment->tanggal, 'd MMMM Y, H:i') }} WIB</p>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-emerald-200"></div>

            <!-- Info Pembayaran -->
            <div class="space-y-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Detail Transaksi</p>

                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">ID Transaksi</span>
                    <span class="text-xs font-bold text-emerald-800 font-mono">#TRX-{{ $payment->id }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Metode Bayar</span>
                    <span class="text-xs font-bold text-emerald-800">{{ $payment->metode_label }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Status</span>
                    <span class="text-xs font-bold {{ $payment->status === \App\Models\Transaksi::STATUS_SUCCESS ? 'text-emerald-600' : ($payment->status === \App\Models\Transaksi::STATUS_FAILED ? 'text-red-600' : 'text-amber-600') }}">
                        {{ $payment->status_label }}
                    </span>
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-emerald-200"></div>

            <!-- Info Sewa -->
            <div class="space-y-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Detail Penyewaan</p>

                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">ID Sewa</span>
                    <span class="text-xs font-bold text-emerald-800 font-mono">#SEWA-{{ $payment->penyewaan->id }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Unit Motor</span>
                    <span class="text-xs font-bold text-emerald-800 text-right max-w-[55%]">{{ $payment->penyewaan->motor->merk }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Nomor Plat</span>
                    <span class="text-xs font-bold text-emerald-800 font-mono">{{ $payment->penyewaan->motor->no_plat }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Tipe Durasi</span>
                    <span class="text-xs font-bold text-emerald-800 capitalize">{{ $payment->penyewaan->tipe_durasi }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Tanggal Mulai</span>
                    <span class="text-xs font-bold text-emerald-800">{{ tanggal_id($payment->penyewaan->tanggal_mulai) }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Tanggal Selesai</span>
                    <span class="text-xs font-bold text-emerald-800">{{ tanggal_id($payment->penyewaan->tanggal_selesai) }}</span>
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-emerald-200"></div>

            <!-- Info Penyewa -->
            <div class="space-y-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Data Penyewa</p>

                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Nama</span>
                    <span class="text-xs font-bold text-emerald-800">{{ $payment->penyewaan->penyewa->nama }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">Email</span>
                    <span class="text-xs font-bold text-emerald-800 text-right max-w-[60%] truncate">{{ $payment->penyewaan->penyewa->email }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-xs text-emerald-500">No. Telepon</span>
                    <span class="text-xs font-bold text-emerald-800">{{ $payment->penyewaan->penyewa->no_tlpn }}</span>
                </div>
            </div>

            @if($payment->catatan_admin)
                <!-- Dashed Divider -->
                <div class="border-t border-dashed border-emerald-200"></div>

                <!-- Catatan Admin -->
                <div class="p-3 rounded-xl bg-red-50 border border-red-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-1">Catatan Admin</p>
                    <p class="text-xs text-red-700">{{ $payment->catatan_admin }}</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="border-t border-dashed border-emerald-200 pt-4 text-center space-y-1">
                <p class="text-[10px] text-emerald-400">Simpan struk ini sebagai bukti pembayaran.</p>
                <p class="text-[10px] text-emerald-400">Hubungi admin jika ada pertanyaan.</p>
                <p class="text-[11px] font-bold text-emerald-600 mt-2">RideGo • Platform Rental Motor Terpercaya</p>
                <p class="text-[10px] text-emerald-300">Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <!-- Bottom spacer untuk print -->
    <div class="h-8 no-print"></div>

</body>
</html>
