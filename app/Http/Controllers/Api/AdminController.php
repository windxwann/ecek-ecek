<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\TarifRental;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\ProfitSharingService;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected RentalService $rentalService,
        protected PaymentService $paymentService,
        protected ProfitSharingService $profitSharingService
    ) {}

    public function users(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function motors(Request $request): JsonResponse
    {
        $motors = Motor::with(['pemilik', 'tarif'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $motors,
        ]);
    }

    public function verifyMotor(Request $request, Motor $motor): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:tersedia,ditolak'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $motor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi motor berhasil diperbarui.',
            'data' => $motor,
        ]);
    }

    public function updatePricing(Request $request, Motor $motor): JsonResponse
    {
        $data = $request->validate([
            'tarif_harian' => ['required', 'numeric', 'min:0'],
            'tarif_mingguan' => ['required', 'numeric', 'min:0'],
            'tarif_bulanan' => ['required', 'numeric', 'min:0'],
        ]);

        $tarif = TarifRental::updateOrCreate(
            ['motor_id' => $motor->id],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Tarif rental motor berhasil diperbarui.',
            'data' => $tarif,
        ]);
    }

    public function rentals(Request $request): JsonResponse
    {
        $rentals = Penyewaan::with(['motor.pemilik', 'penyewa', 'transaksiTerakhir'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $rentals,
        ]);
    }

    public function confirmRental(Penyewaan $rental): JsonResponse
    {
        $rental->update(['status' => Penyewaan::STATUS_CONFIRMED]);

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan penyewaan berhasil dikonfirmasi.',
            'data' => $rental,
        ]);
    }

    public function returnRental(Penyewaan $rental): JsonResponse
    {
        $this->rentalService->complete($rental, $this->profitSharingService);

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian motor berhasil dikonfirmasi & bagi hasil dicatat.',
            'data' => $rental->fresh(['bagiHasil', 'motor']),
        ]);
    }

    public function payments(Request $request): JsonResponse
    {
        $payments = Transaksi::with(['penyewaan.penyewa', 'penyewaan.motor'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    public function confirmPayment(Transaksi $transaksi): JsonResponse
    {
        $this->paymentService->confirm($transaksi);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi.',
            'data' => $transaksi->fresh('penyewaan'),
        ]);
    }

    public function revenueReport(Request $request): JsonResponse
    {
        $start = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $transactions = Transaksi::where('status', Transaksi::STATUS_PAID)
            ->whereBetween('tanggal', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()])
            ->with(['penyewaan.penyewa', 'penyewaan.motor'])
            ->paginate(15);

        $totalRevenue = (float) Transaksi::where('status', Transaksi::STATUS_PAID)
            ->whereBetween('tanggal', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()])
            ->sum('jumlah');

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'transactions' => $transactions,
            ],
        ]);
    }

    public function profitSharingReport(Request $request): JsonResponse
    {
        $start = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $records = BagiHasil::whereBetween('tanggal', [$start, $end])
            ->with(['penyewaan.motor.pemilik', 'penyewaan.penyewa'])
            ->paginate(15);

        $summary = BagiHasil::whereBetween('tanggal', [$start, $end])
            ->selectRaw('SUM(bagi_hasil_pemilik) as total_owner, SUM(bagi_hasil_admin) as total_admin')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'records' => $records,
            ],
        ]);
    }
}
