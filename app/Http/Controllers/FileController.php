<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function motorDocument(Request $request, Motor $motor): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || $user->id === $motor->pemilik_id, 403);
        abort_unless($motor->dokumen_kepemilikan && Storage::disk('local')->exists($motor->dokumen_kepemilikan), 404);

        return Storage::disk('local')->response($motor->dokumen_kepemilikan);
    }

    public function paymentProof(Request $request, Transaksi $transaksi): StreamedResponse
    {
        $user = $request->user();
        $transaksi->loadMissing('penyewaan');

        abort_unless(
            $user->isAdmin() || $transaksi->penyewaan?->penyewa_id === $user->id,
            403
        );
        abort_unless($transaksi->bukti_pembayaran && Storage::disk('local')->exists($transaksi->bukti_pembayaran), 404);

        return Storage::disk('local')->response($transaksi->bukti_pembayaran);
    }
}
