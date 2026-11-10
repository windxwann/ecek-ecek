<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use App\Services\ProfitSharingService;
use App\Services\RentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function __construct(
        protected RentalService $rentalService,
        protected ProfitSharingService $profitSharingService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $rentals = Penyewaan::query()
            ->with(['motor.pemilik', 'penyewa', 'transaksiTerakhir', 'bagiHasil'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->whereHas('penyewa', fn ($u) => $u->where('nama', 'like', '%'.$search.'%'))
                        ->orWhereHas('motor', fn ($m) => $m->where('merk', 'like', '%'.$search.'%')->orWhere('no_plat', 'like', '%'.$search.'%'));
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rentals.index', [
            'rentals' => $rentals,
            'currentStatus' => $status,
            'search' => $search,
            'counts' => [
                'waiting' => Penyewaan::where('status', Penyewaan::STATUS_WAITING_CONFIRMATION)->count(),
                'berjalan' => Penyewaan::where('status', Penyewaan::STATUS_BERJALAN)->count(),
                'selesai' => Penyewaan::where('status', Penyewaan::STATUS_COMPLETED)->count(),
                'batal' => Penyewaan::where('status', Penyewaan::STATUS_CANCELLED)->count(),
                'total' => Penyewaan::count(),
            ],
        ]);
    }

    public function show(Penyewaan $rental): View
    {
        $rental->load(['motor.pemilik', 'penyewa', 'transaksis', 'bagiHasil']);

        return view('admin.rentals.show', [
            'rental' => $rental,
        ]);
    }

    public function markOngoing(Penyewaan $rental): RedirectResponse
    {
        $this->rentalService->markOngoing($rental);

        return back()->with('success', 'Status sewa telah diubah menjadi sedang berlangsung (motor disewa).');
    }

    public function confirmReturn(Penyewaan $rental): RedirectResponse
    {
        $this->rentalService->complete($rental, $this->profitSharingService);

        return back()->with('success', 'Pengembalian motor berhasil dikonfirmasi. Status sewa selesai & pembagian hasil telah dicatat!');
    }

    public function cancel(Request $request, Penyewaan $rental): RedirectResponse
    {
        $data = $request->validate([
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        $this->rentalService->cancel($rental, $data['alasan'] ?? 'Dibatalkan oleh admin.');

        return back()->with('info', 'Pemesanan sewa telah dibatalkan.');
    }
}
