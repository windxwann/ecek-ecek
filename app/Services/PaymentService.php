<?php

namespace App\Services;

use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function submit(Penyewaan $rental, array $data, UploadedFile $proof): Transaksi
    {
        if ($rental->status !== Penyewaan::STATUS_PENDING_PAYMENT) {
            throw ValidationException::withMessages([
                'status' => 'Pembayaran hanya dapat dikirim untuk pesanan yang menunggu pembayaran.',
            ]);
        }

        return DB::transaction(function () use ($rental, $data, $proof) {
            $path = $proof->store('payment-proofs', 'local');

            $transaksi = $rental->transaksis()->create([
                'jumlah' => $data['jumlah'] ?? $rental->harga,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'status' => Transaksi::STATUS_PENDING,
                'tanggal' => now(),
                'bukti_pembayaran' => $path,
            ]);

            $rental->update(['status' => Penyewaan::STATUS_WAITING_CONFIRMATION]);

            return $transaksi;
        });
    }

    public function confirm(Transaksi $transaksi): void
    {
        $rental = $transaksi->penyewaan;

        if ($transaksi->status !== Transaksi::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Transaksi ini sudah diproses sebelumnya.',
            ]);
        }

        if (! in_array($rental->status, [Penyewaan::STATUS_WAITING_CONFIRMATION, Penyewaan::STATUS_PENDING_PAYMENT], true)) {
            throw ValidationException::withMessages([
                'status' => 'Status penyewaan tidak memungkinkan konfirmasi pembayaran.',
            ]);
        }

        DB::transaction(function () use ($transaksi, $rental) {
            $transaksi->update([
                'status' => Transaksi::STATUS_SUCCESS,
                'catatan_admin' => null,
            ]);

            // PRD v1 (alur 4): setelah admin mengonfirmasi pembayaran,
            // status penyewaan menjadi berjalan dan motor menjadi disewa.
            $rental->update(['status' => Penyewaan::STATUS_BERJALAN]);
            $rental->motor()->update(['status' => Motor::STATUS_RENTED]);
        });
    }

    public function reject(Transaksi $transaksi, string $note = ''): void
    {
        if ($transaksi->status !== Transaksi::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Transaksi ini sudah diproses sebelumnya.',
            ]);
        }

        DB::transaction(function () use ($transaksi, $note) {
            $transaksi->update([
                'status' => Transaksi::STATUS_FAILED,
                'catatan_admin' => $note ?: 'Bukti pembayaran tidak valid.',
            ]);

            if ($transaksi->penyewaan->status === Penyewaan::STATUS_WAITING_CONFIRMATION) {
                $transaksi->penyewaan->update(['status' => Penyewaan::STATUS_PENDING_PAYMENT]);
            }
        });
    }

    public function deleteProof(Transaksi $transaksi): void
    {
        if ($transaksi->bukti_pembayaran) {
            Storage::disk('local')->delete($transaksi->bukti_pembayaran);
        }
    }
}
