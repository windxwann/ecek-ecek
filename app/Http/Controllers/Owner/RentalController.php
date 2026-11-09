<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Penyewaan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $rentals = Penyewaan::query()
            ->whereHas('motor', fn ($q) => $q->where('pemilik_id', $request->user()->id))
            ->with(['motor', 'penyewa', 'transaksiTerakhir', 'bagiHasil'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('owner.rentals.index', [
            'rentals' => $rentals,
            'currentStatus' => $status,
        ]);
    }
}
