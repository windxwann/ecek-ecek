<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(Request $request): View
    {
        $motors = Motor::query()
            ->available()
            ->with(['pemilik', 'tarif'])
            ->withCount('penyewaans')
            ->orderBy('id', 'asc')
            ->limit(6)
            ->get();

        return view('marketplace.home', [
            'motors' => $motors,
            'totalMotors' => Motor::available()->count(),
            'totalOwners' => Motor::available()->distinct()->count('pemilik_id'),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'cc']);

        $motors = Motor::query()
            ->available()
            ->with(['pemilik', 'tarif'])
            ->when($filters['q'] ?? null, function ($query, $q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('merk', 'like', '%'.$q.'%')
                        ->orWhere('no_plat', 'like', '%'.$q.'%')
                        ->orWhereHas('pemilik', fn ($owner) => $owner->where('nama', 'like', '%'.$q.'%'));
                });
            })
            ->when($filters['cc'] ?? null, fn ($query, $cc) => $query->where('tipe_cc', $cc))
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->string('sort')->toString()) {
                    'termurah' => $query->orderBy(
                        \App\Models\TarifRental::select('tarif_harian')->whereColumn('motor_id', 'motors.id')
                    ),
                    'termahal' => $query->orderByDesc(
                        \App\Models\TarifRental::select('tarif_harian')->whereColumn('motor_id', 'motors.id')
                    ),
                    default => $query->orderBy('id', 'asc'),
                };
            }, fn ($query) => $query->orderBy('id', 'asc'))
            ->paginate(9)
            ->withQueryString();

        return view('marketplace.index', [
            'motors' => $motors,
            'filters' => $filters,
            'totalAvailable' => Motor::available()->count(),
        ]);
    }

    public function show(Motor $motor): View
    {
        abort_unless($motor->status === Motor::STATUS_AVAILABLE, 404);

        $motor->load(['pemilik', 'tarif', 'penyewaans' => fn ($query) => $query->whereIn('status', [
            Penyewaan::STATUS_CONFIRMED,
            Penyewaan::STATUS_ONGOING,
        ])->orderBy('tanggal_mulai')]);

        return view('marketplace.show', [
            'motor' => $motor,
            'bookedRanges' => $motor->penyewaans->map(fn (Penyewaan $rental) => [
                'start' => $rental->tanggal_mulai->toDateString(),
                'end' => $rental->tanggal_selesai->toDateString(),
            ]),
        ]);
    }
}
