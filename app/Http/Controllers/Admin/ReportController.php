<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Transaksi;
use App\Services\ProfitSharingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ProfitSharingService $profitSharingService
    ) {}

    public function revenue(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $transactions = Transaksi::query()
            ->where('status', Transaksi::STATUS_PAID)
            ->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->with(['penyewaan.penyewa', 'penyewaan.motor.pemilik'])
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $totalRevenue = Transaksi::query()
            ->where('status', Transaksi::STATUS_PAID)
            ->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->sum('jumlah');

        return view('admin.reports.revenue', [
            'transactions' => $transactions,
            'totalRevenue' => (float) $totalRevenue,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function payments(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());
        $status = $request->query('status');

        $baseQuery = Transaksi::query()->whereBetween('tanggal', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ]);

        $transactions = (clone $baseQuery)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with(['penyewaan.penyewa', 'penyewaan.motor'])
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_count' => (clone $baseQuery)->count(),
            'total_paid' => (float) (clone $baseQuery)->where('status', Transaksi::STATUS_PAID)->sum('jumlah'),
            'paid_count' => (clone $baseQuery)->where('status', Transaksi::STATUS_PAID)->count(),
            'pending_count' => (clone $baseQuery)->where('status', Transaksi::STATUS_PENDING)->count(),
            'rejected_count' => (clone $baseQuery)->where('status', Transaksi::STATUS_REJECTED)->count(),
            'failed_count' => (clone $baseQuery)->where('status', Transaksi::STATUS_FAILED)->count(),
        ];

        return view('admin.reports.payments', [
            'transactions' => $transactions,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentStatus' => $status,
        ]);
    }

    public function profitSharing(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $profitSharings = BagiHasil::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with(['penyewaan.motor.pemilik', 'penyewaan.penyewa'])
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $summary = BagiHasil::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(bagi_hasil_pemilik) as total_owner, SUM(bagi_hasil_admin) as total_admin, COUNT(*) as total_count')
            ->first();

        return view('admin.reports.profit-sharing', [
            'profitSharings' => $profitSharings,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function settleProfitSharing(BagiHasil $bagiHasil): RedirectResponse
    {
        $this->profitSharingService->settle($bagiHasil);

        return back()->with('success', 'Bagi hasil berhasil diselesaikan (settled).');
    }
}
