<?php

namespace App\Services;

use App\Models\BagiHasil;
use App\Models\Penyewaan;

class ProfitSharingService
{
    public function ownerPercent(): float
    {
        return (float) config('rental.owner_share_percent', 70);
    }

    public function recordFor(Penyewaan $rental): BagiHasil
    {
        $total = (float) $rental->harga;
        $owner = round($total * $this->ownerPercent() / 100, 2);
        $admin = round($total - $owner, 2);

        // firstOrCreate membuat pencatatan idempoten, termasuk bila trigger
        // database sudah lebih dulu membuat baris bagi hasil.
        return BagiHasil::firstOrCreate(
            ['pemesanan_id' => $rental->id],
            [
                'bagi_hasil_pemilik' => $owner,
                'bagi_hasil_admin' => $admin,
                'tanggal' => now()->toDateString(),
            ]
        );
    }

    public function settle(BagiHasil $bagiHasil): void
    {
        if ($bagiHasil->settled_at) {
            return;
        }

        $bagiHasil->update(['settled_at' => now()]);
    }
}
