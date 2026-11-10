@extends('layouts.admin')

@section('page_title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Top Banner -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-3">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1.5"></i> Admin Control Center
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Overview Sistem RideGo</h1>
            <p class="text-slate-500 text-sm mt-1 max-w-xl">
                Statistik terpusat pengguna, unit motor, transaksi keuangan, serta pembagian hasil.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.motors.index', ['status' => 'menunggu_verifikasi']) }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs shadow-sm transition-all duration-200 active:scale-95 border border-emerald-100 hover:-translate-y-[1px]">
                <i data-lucide="check-square" class="w-4 h-4 mr-1.5"></i> Verifikasi Motor ({{ $counts['pending_motors'] }})
            </a>
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs shadow-sm transition-all duration-200 active:scale-95 border border-indigo-100 hover:-translate-y-[1px]">
                <i data-lucide="credit-card" class="w-4 h-4 mr-1.5"></i> Cek Pembayaran ({{ $counts['pending_payments'] }})
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengguna</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $counts['total_users'] }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Unit Motor</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $counts['total_motors'] }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    <span class="text-emerald-600 font-bold">{{ $counts['available_motors'] }} Siap</span> &bull;
                    <span class="text-blue-600 font-bold">{{ $counts['rented_motors'] }} Disewa</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="bike" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi Lunas</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $counts['total_transactions'] }}</div>
                <div class="text-[11px] text-amber-600 font-bold mt-0.5">{{ $counts['pending_payments'] }} Menunggu Approval</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pendapatan Platform</span>
                <div class="text-lg font-black text-blue-600 mt-1">{{ rupiah($counts['total_revenue']) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    Admin: <strong class="text-slate-700">{{ rupiah($counts['admin_revenue']) }}</strong>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Chart & Pending Verifications -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Monthly Trend Chart (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 flex items-center mb-4">
                <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 text-blue-600"></i> Tren Penyewaan Tahun {{ date('Y') }}
            </h3>
            <div class="h-64">
                <canvas id="adminRentalChart"></canvas>
            </div>
        </div>

        <!-- Pending Verifications (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2 text-amber-500"></i> Motor Menunggu Verifikasi
                </h3>
                <a href="{{ route('admin.motors.index', ['status' => 'menunggu_verifikasi']) }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Semua
                </a>
            </div>

            @if($pendingVerifications->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingVerifications as $motor)
                        <div class="p-3.5 rounded-xl border border-amber-100 bg-amber-50/40 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $motor->merk }}</span>
                                <span class="text-slate-500 font-mono text-[11px]">{{ $motor->no_plat }} &bull; Mitra: {{ $motor->pemilik->nama ?? '-' }}</span>
                            </div>
                            <a href="{{ route('admin.motors.show', $motor) }}"
                               class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition">
                                Verifikasi
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-xs text-slate-400">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                    Semua unit motor sudah diverifikasi!
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Rentals Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center">
                <i data-lucide="clipboard-list" class="w-5 h-5 mr-2 text-blue-600"></i> 5 Transaksi Penyewaan Terbaru
            </h3>
            <a href="{{ route('admin.rentals.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Lihat Seluruh Pesanan ->
            </a>
        </div>

        @if($recentRentals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">ID & Tanggal</th>
                            <th class="py-3 px-4">Penyewa</th>
                            <th class="py-3 px-4">Motor</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentRentals as $rental)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    #{{ $rental->id }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ tanggal_id($rental->created_at) }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $rental->penyewa->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $rental->penyewa->no_tlpn ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $rental->motor->merk }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $rental->motor->no_plat }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ tanggal_id($rental->tanggal_mulai) }} - {{ tanggal_id($rental->tanggal_selesai) }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ rupiah($rental->harga) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($rental->status === 'selesai') bg-emerald-50 text-emerald-700
                                        @elseif($rental->status === 'berjalan') bg-blue-50 text-blue-700
                                        @elseif($rental->status === 'berjalan') bg-indigo-50 text-indigo-700
                                        @elseif($rental->status === 'menunggu_konfirmasi') bg-amber-50 text-amber-700
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ $rental->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.rentals.show', $rental) }}" class="text-blue-600 font-bold hover:underline">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center py-6 text-xs text-slate-400">Belum ada transaksi penyewaan.</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('adminRentalChart');
    if (!ctx) return;

    const data = {{ json_encode($chartData) }};
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Penyewaan',
                data: data,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
@endsection
