<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $payments = Transaksi::query()
            ->with(['penyewaan.penyewa', 'penyewaan.motor'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'currentStatus' => $status,
            'counts' => [
                'pending' => Transaksi::where('status', Transaksi::STATUS_PENDING)->count(),
                'berhasil' => Transaksi::where('status', Transaksi::STATUS_SUCCESS)->count(),
                'gagal' => Transaksi::where('status', Transaksi::STATUS_FAILED)->count(),
                'total' => Transaksi::count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $rentals = Penyewaan::query()
            ->with(['penyewa', 'motor'])
            ->whereIn('status', [
                Penyewaan::STATUS_PENDING_PAYMENT,
                Penyewaan::STATUS_WAITING_CONFIRMATION,
                Penyewaan::STATUS_BERJALAN,
            ])
            ->orderByDesc('id')
            ->get();

        return view('admin.payments.create', [
            'rentals' => $rentals,
            'selectedRentalId' => $request->query('pemesanan_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayment($request);

        $transaksi = DB::transaction(function () use ($request, $data) {
            $rental = Penyewaan::findOrFail($data['pemesanan_id']);

            $transaksi = $rental->transaksis()->create([
                'jumlah' => $data['jumlah'] ?? $rental->harga,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'status' => $data['status'],
                'tanggal' => $data['tanggal'],
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'bukti_pembayaran' => $this->storeProof($request),
            ]);

            if ($transaksi->status === Transaksi::STATUS_SUCCESS) {
                $this->markRentalAsRunning($rental);
            }

            return $transaksi;
        });

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Transaksi pembayaran #TRX-'.$transaksi->id.' berhasil dicatat.');
    }

    public function edit(Transaksi $transaksi): View
    {
        $transaksi->load(['penyewaan.penyewa', 'penyewaan.motor']);

        $rentals = Penyewaan::query()
            ->with(['penyewa', 'motor'])
            ->orderByDesc('id')
            ->get();

        return view('admin.payments.edit', [
            'transaksi' => $transaksi,
            'rentals' => $rentals,
        ]);
    }

    public function update(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $data = $this->validatePayment($request);

        DB::transaction(function () use ($request, $data, $transaksi) {
            $oldProof = $transaksi->bukti_pembayaran;
            $newProof = $this->storeProof($request);

            $transaksi->update([
                'pemesanan_id' => $data['pemesanan_id'],
                'jumlah' => $data['jumlah'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'status' => $data['status'],
                'tanggal' => $data['tanggal'],
                'catatan_admin' => $data['catatan_admin'] ?? null,
                ...($newProof ? ['bukti_pembayaran' => $newProof] : []),
            ]);

            if ($newProof && $oldProof) {
                Storage::disk('local')->delete($oldProof);
            }

            if ($data['status'] === Transaksi::STATUS_SUCCESS) {
                $this->markRentalAsRunning($transaksi->penyewaan);
            }
        });

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Transaksi pembayaran #TRX-'.$transaksi->id.' berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi): RedirectResponse
    {
        $this->paymentService->deleteProof($transaksi);
        $transaksi->delete();

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Transaksi pembayaran berhasil dihapus.');
    }

    public function confirm(Transaksi $transaksi): RedirectResponse
    {
        $this->paymentService->confirm($transaksi);

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan disetujui.');
    }

    public function reject(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $this->paymentService->reject($transaksi, $data['catatan'] ?? 'Bukti pembayaran tidak valid.');

        return back()->with('info', 'Pembayaran telah ditolak.');
    }

    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'pemesanan_id' => ['required', 'exists:penyewaans,id'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'in:transfer,ewallet,tunai'],
            'status' => ['required', 'in:pending,berhasil,gagal'],
            'tanggal' => ['required', 'date'],
            'catatan_admin' => ['nullable', 'string', 'max:255'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);
    }

    private function storeProof(Request $request): ?string
    {
        if (! $request->hasFile('bukti_pembayaran')) {
            return null;
        }

        return $request->file('bukti_pembayaran')->store('payment-proofs', 'local');
    }

    /**
     * Penyelarasan status saat admin mencatat pembayaran berhasil secara manual.
     * Hanya penyewaan yang belum berjalan/selesai yang ikut diubah.
     */
    private function markRentalAsRunning(?Penyewaan $rental): void
    {
        if (! $rental) {
            return;
        }

        if (in_array($rental->status, [Penyewaan::STATUS_PENDING_PAYMENT, Penyewaan::STATUS_WAITING_CONFIRMATION], true)) {
            $rental->update(['status' => Penyewaan::STATUS_BERJALAN]);
            $rental->motor()->update(['status' => Motor::STATUS_RENTED]);
        }
    }
}
