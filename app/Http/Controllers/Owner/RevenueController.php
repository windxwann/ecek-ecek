<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = BagiHasil::query()
            ->whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))
            ->with(['penyewaan.motor', 'penyewaan.penyewa']);

        $totalRevenue = (float) (clone $query)->sum('bagi_hasil_pemilik');
        $settledRevenue = (float) (clone $query)->whereNotNull('settled_at')->sum('bagi_hasil_pemilik');
        $pendingRevenue = $totalRevenue - $settledRevenue;

        $profitSharings = $query->orderBy('id', 'asc')->paginate(10);

        return view('owner.revenue.index', [
            'profitSharings' => $profitSharings,
            'totalRevenue' => $totalRevenue,
            'settledRevenue' => $settledRevenue,
            'pendingRevenue' => $pendingRevenue,
        ]);
    }
}
