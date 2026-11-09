<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $payments = Transaksi::query()
            ->whereHas('penyewaan', fn ($q) => $q->where('penyewa_id', $request->user()->id))
            ->with(['penyewaan.motor'])
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('tenant.payments.index', [
            'payments' => $payments,
        ]);
    }

    public function create(Request $request, Penyewaan $rental): View|RedirectResponse
    {
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        if ($rental->status !== Penyewaan::STATUS_PENDING_PAYMENT) {
            return redirect()->route('tenant.rentals.show', $rental)
                ->with('info', 'Status pemesanan ini adalah: '.$rental->status_label);
        }

        $rental->load(['motor.pemilik']);

        return view('tenant.payments.create', [
            'rental' => $rental,
            'bank' => config('rental.bank'),
            'paymentMethods' => config('rental.payment_methods'),
        ]);
    }

    public function store(Request $request, Penyewaan $rental): RedirectResponse
    {
        abort_unless($rental->penyewa_id === $request->user()->id, 403);

        $data = $request->validate([
            'metode_pembayaran' => ['required', 'string', 'in:transfer,ewallet,tunai'],
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [], [
            'metode_pembayaran' => 'metode pembayaran',
            'bukti_pembayaran' => 'bukti transfer / pembayaran',
        ]);

        $this->paymentService->submit($rental, $data, $request->file('bukti_pembayaran'));

        return redirect()->route('tenant.rentals.show', $rental)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari admin.');
    }
}
