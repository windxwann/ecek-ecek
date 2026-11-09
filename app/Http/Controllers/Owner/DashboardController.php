<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $totalMotors = Motor::where('pemilik_id', $user->id)->count();
        $availableMotors = Motor::where('pemilik_id', $user->id)->where('status', Motor::STATUS_AVAILABLE)->count();
        $rentedMotors = Motor::where('pemilik_id', $user->id)->where('status', Motor::STATUS_RENTED)->count();
        $pendingMotors = Motor::where('pemilik_id', $user->id)->where('status', Motor::STATUS_PENDING)->count();

        // Total pendapatan pemilik dari bagi hasil
        $totalRevenue = BagiHasil::query()
            ->whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))
            ->sum('bagi_hasil_pemilik');

        $settledRevenue = BagiHasil::query()
            ->whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))
            ->whereNotNull('settled_at')
            ->sum('bagi_hasil_pemilik');

        // Recent rentals for owner's motors
        $recentRentals = Penyewaan::query()
            ->whereHas('motor', fn ($q) => $q->where('pemilik_id', $user->id))
            ->with(['motor', 'penyewa', 'transaksiTerakhir'])
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        // Chart: monthly rentals for the current year
        $monthlyRentals = Penyewaan::query()
            ->whereHas('motor', fn ($q) => $q->where('pemilik_id', $user->id))
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $monthlyRentals[$m] ?? 0;
        }

        return view('owner.dashboard', [
            'user' => $user,
            'counts' => [
                'total_motors' => $totalMotors,
                'available_motors' => $availableMotors,
                'rented_motors' => $rentedMotors,
                'pending_motors' => $pendingMotors,
                'total_revenue' => (float) $totalRevenue,
                'settled_revenue' => (float) $settledRevenue,
            ],
            'recentRentals' => $recentRentals,
            'chartData' => json_encode($chartData),
        ]);
    }
}
