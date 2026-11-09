<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $activeRentals = Penyewaan::query()
            ->where('penyewa_id', $user->id)
            ->whereIn('status', [
                Penyewaan::STATUS_PENDING_PAYMENT,
                Penyewaan::STATUS_WAITING_CONFIRMATION,
                Penyewaan::STATUS_CONFIRMED,
                Penyewaan::STATUS_ONGOING,
            ])
            ->with(['motor.pemilik', 'transaksiTerakhir'])
            ->orderBy('id', 'asc')
            ->get();

        $recentRentals = Penyewaan::query()
            ->where('penyewa_id', $user->id)
            ->with(['motor', 'transaksiTerakhir'])
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        $totalSpent = Transaksi::query()
            ->whereHas('penyewaan', fn ($q) => $q->where('penyewa_id', $user->id))
            ->where('status', Transaksi::STATUS_PAID)
            ->sum('jumlah');

        $counts = [
            'total_rentals' => Penyewaan::where('penyewa_id', $user->id)->count(),
            'active_rentals' => $activeRentals->count(),
            'pending_payments' => Penyewaan::where('penyewa_id', $user->id)->where('status', Penyewaan::STATUS_PENDING_PAYMENT)->count(),
            'total_spent' => (float) $totalSpent,
        ];

        return view('tenant.dashboard', [
            'user' => $user,
            'counts' => $counts,
            'activeRentals' => $activeRentals,
            'recentRentals' => $recentRentals,
        ]);
    }
}
