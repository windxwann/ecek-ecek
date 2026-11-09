<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function __construct(
        protected RentalService $rentalService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $rentals = Penyewaan::query()
            ->where('penyewa_id', $request->user()->id)
            ->with(['motor.pemilik', 'transaksiTerakhir'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('tenant.rentals.index', [
            'rentals' => $rentals,
            'currentStatus' => $status,
        ]);
    }

    public function create(Motor $motor): View|RedirectResponse
    {
        if ($motor->status !== Motor::STATUS_AVAILABLE) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Motor ini saat ini tidak tersedia untuk disewa.');
        }

        $motor->load(['pemilik', 'tarif', 'penyewaans' => fn ($q) => $q->whereIn('status', [
            Penyewaan::STATUS_CONFIRMED,
            Penyewaan::STATUS_ONGOING,
        ])]);

        return view('tenant.rentals.create', [
            'motor' => $motor,
        ]);
    }

    public function quote(Request $request, Motor $motor): JsonResponse
    {
        $data = $request->validate([
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tipe_durasi' => ['required', 'in:harian,mingguan,bulanan'],
        ]);

        $isAvailable = $this->rentalService->isAvailable(
            $motor,
            $data['tanggal_mulai'],
            $data['tanggal_selesai']
        );

        if (! $isAvailable) {
            return response()->json([
                'available' => false,
                'message' => 'Motor sudah dipesan pada periode tersebut.',
            ], 422);
        }

        $quote = $this->rentalService->quote(
            $motor,
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['tipe_durasi']
        );

        return response()->json([
            'available' => true,
            'quote' => $quote,
            'formatted_total' => rupiah($quote['total']),
        ]);
    }

    public function store(Request $request, Motor $motor): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tipe_durasi' => ['required', 'in:harian,mingguan,bulanan'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $rental = $this->rentalService->create(
            $request->user(),
            $motor,
            $data
        );

        return redirect()->route('tenant.payments.create', $rental)
            ->with('success', 'Pemesanan berhasil dibuat! Silakan lakukan pembayaran untuk menyelesaikan proses.');
    }

    public function show(Request $request, Penyewaan $rental): View
    {
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        $rental->load(['motor.pemilik', 'transaksis' => fn ($q) => $q->latest()]);

        return view('tenant.rentals.show', [
            'rental' => $rental,
        ]);
    }

    public function cancel(Request $request, Penyewaan $rental): RedirectResponse
    {
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        $data = $request->validate([
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        $this->rentalService->cancel($rental, $data['alasan'] ?? 'Dibatalkan oleh penyewa.');

        return redirect()->route('tenant.rentals.show', $rental)
            ->with('success', 'Penyewaan berhasil dibatalkan.');
    }
}
