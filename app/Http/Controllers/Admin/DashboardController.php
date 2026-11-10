<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalUsers = User::count();
        $totalMotors = Motor::count();
        $availableMotors = Motor::where('status', Motor::STATUS_AVAILABLE)->count();
        $rentedMotors = Motor::where('status', Motor::STATUS_RENTED)->count();
        $pendingMotors = Motor::where('status', Motor::STATUS_PENDING)->count();

        $totalTransactions = Transaksi::where('status', Transaksi::STATUS_PAID)->count();
        $pendingPayments = Transaksi::where('status', Transaksi::STATUS_PENDING)->count();

        $totalPlatformRevenue = (float) Transaksi::where('status', Transaksi::STATUS_PAID)->sum('jumlah');
        $adminRevenue = (float) BagiHasil::sum('bagi_hasil_admin');
        $ownerRevenue = (float) BagiHasil::sum('bagi_hasil_pemilik');

        $recentRentals = Penyewaan::query()
            ->with(['motor.pemilik', 'penyewa', 'transaksiTerakhir'])
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        $pendingVerifications = Motor::query()
            ->where('status', Motor::STATUS_PENDING)
            ->with('pemilik')
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        // 12 Months Rentals Chart
        $monthlyRentals = Penyewaan::query()
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $monthlyRentals[$m] ?? 0;
        }

        return view('admin.dashboard', [
            'counts' => [
                'total_users' => $totalUsers,
                'total_motors' => $totalMotors,
                'available_motors' => $availableMotors,
                'rented_motors' => $rentedMotors,
                'pending_motors' => $pendingMotors,
                'total_transactions' => $totalTransactions,
                'pending_payments' => $pendingPayments,
                'total_revenue' => $totalPlatformRevenue,
                'admin_revenue' => $adminRevenue,
                'owner_revenue' => $ownerRevenue,
            ],
            'recentRentals' => $recentRentals,
            'pendingVerifications' => $pendingVerifications,
            'chartData' => $chartData,
        ]);
    }
}
