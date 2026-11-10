@extends('layouts.owner')

@section('page_title', 'Owner Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-3">
                <i data-lucide="key" class="w-3.5 h-3.5 mr-1.5"></i> Portal Mitra Pemilik Kendaraan
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="text-slate-500 text-sm mt-1 max-w-xl">
                Pantau performa unit motor Anda, jadwal penyewaan, serta total pembagian hasil pendapatan rental.
            </p>
        </div>
        <a href="{{ route('owner.motors.create') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs shadow-sm transition-all duration-200 active:scale-95 border border-emerald-100 hover:-translate-y-[1px]">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Daftarkan Motor Baru
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Motor</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $counts['total_motors'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="bike" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Motor Tersedia</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $counts['available_motors'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Disewa</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $counts['rented_motors'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Bagi Hasil</span>
                <div class="text-lg font-black text-emerald-600 mt-1">{{ rupiah($counts['total_revenue']) }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Monthly Rental Trend Chart (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-emerald-600"></i> Tren Penyewaan Motor Tahun {{ date('Y') }}
                </h3>
            </div>
            <div class="relative flex-1 min-h-0" style="height:220px">
                <canvas id="ownerRentalChart"></canvas>
            </div>
        </div>

        <!-- Recent Rentals (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="history" class="w-5 h-5 mr-2 text-emerald-600"></i> Aktivitas Sewa Terbaru
                </h3>
                <a href="{{ route('owner.rentals.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                    Semua
                </a>
            </div>

            @if($recentRentals->count() > 0)
                <div class="space-y-3">
                    @foreach($recentRentals as $rental)
                        <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $rental->motor->merk }}</span>
                                <span class="text-slate-400 font-mono text-[11px]">{{ $rental->motor->no_plat }}</span>
                                <span class="text-slate-500 block mt-0.5">Penyewa: {{ $rental->penyewa->nama ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-900 block">{{ rupiah($rental->harga) }}</span>
                                @php
                                    $recentStatusClass = match($rental->status) {
                                        'selesai'  => 'bg-emerald-100 text-emerald-800',
                                        'berjalan' => 'bg-blue-100 text-blue-800',
                                        'menunggu_pembayaran' => 'bg-indigo-100 text-indigo-800',
                                        'dibatalkan' => 'bg-red-100 text-red-700',
                                        default    => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center whitespace-nowrap mt-1 {{ $recentStatusClass }}">
                                    {{ $rental->status_label }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-xs text-slate-400">
                    Belum ada riwayat sewa pada motor Anda.
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('ownerRentalChart');
    if (!ctx) return;

    const data = {{ json_encode($chartData) }};
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Transaksi Sewa',
                data: data,
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                hoverBackgroundColor: 'rgba(5, 150, 105, 1)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        font: { size: 11 },
                        color: '#94a3b8',
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} transaksi`
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
