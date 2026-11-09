<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Services\PaymentService;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        protected RentalService $rentalService,
        protected PaymentService $paymentService
    ) {}

    public function catalog(Request $request): JsonResponse
    {
        $motors = Motor::query()
            ->available()
            ->with(['pemilik', 'tarif'])
            ->when($request->q, fn ($q, $search) => $q->where('merk', 'like', "%{$search}%"))
            ->when($request->cc, fn ($q, $cc) => $q->where('tipe_cc', $cc))
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $motors,
        ]);
    }

    public function showMotor(Motor $motor): JsonResponse
    {
        abort_unless($motor->status === Motor::STATUS_AVAILABLE, 404);

        $motor->load(['pemilik', 'tarif']);

        return response()->json([
            'success' => true,
            'data' => $motor,
        ]);
    }

    public function storeRental(Request $request): JsonResponse
    {
        $data = $request->validate([
            'motor_id' => ['required', 'exists:motors,id'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tipe_durasi' => ['required', 'in:harian,mingguan,bulanan'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $motor = Motor::findOrFail($data['motor_id']);

        $rental = $this->rentalService->create($request->user(), $motor, $data);

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dibuat.',
            'data' => $rental->load('motor.tarif'),
        ], 201);
    }

    public function rentals(Request $request): JsonResponse
    {
        $rentals = Penyewaan::where('penyewa_id', $request->user()->id)
            ->with(['motor.pemilik', 'transaksiTerakhir'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $rentals,
        ]);
    }

    public function showRental(Request $request, Penyewaan $rental): JsonResponse
    {
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        $rental->load(['motor.pemilik', 'transaksis']);

        return response()->json([
            'success' => true,
            'data' => $rental,
        ]);
    }

    public function storePayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pemesanan_id' => ['required', 'exists:penyewaans,id'],
            'metode_pembayaran' => ['required', 'in:transfer,ewallet,tunai'],
            'bukti_pembayaran' => ['required', 'image', 'max:3072'],
        ]);

        $rental = Penyewaan::findOrFail($data['pemesanan_id']);
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        $transaksi = $this->paymentService->submit($rental, $data, $request->file('bukti_pembayaran'));

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'data' => $transaksi,
        ], 201);
    }

    public function payments(Request $request): JsonResponse
    {
        $payments = Transaksi::whereHas('penyewaan', fn ($q) => $q->where('penyewa_id', $request->user()->id))
            ->with(['penyewaan.motor'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }
}
