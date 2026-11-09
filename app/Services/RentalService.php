<?php

namespace App\Services;

use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalService
{
    /**
     * Hitung estimasi biaya sewa di server.
     */
    public function quote(Motor $motor, string $start, string $end, string $tipeDurasi): array
    {
        $mulai = Carbon::parse($start)->startOfDay();
        $selesai = Carbon::parse($end)->startOfDay();

        if ($selesai->lt($mulai)) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            ]);
        }

        if (! in_array($tipeDurasi, ['harian', 'mingguan', 'bulanan'], true)) {
            throw ValidationException::withMessages([
                'tipe_durasi' => 'Tipe durasi tidak valid.',
            ]);
        }

        if (! $motor->tarif) {
            throw ValidationException::withMessages([
                'motor_id' => 'Tarif motor belum ditentukan oleh admin.',
            ]);
        }

        $days = max(1, $mulai->diffInDays($selesai));
        $units = match ($tipeDurasi) {
            'mingguan' => (int) ceil($days / 7),
            'bulanan' => (int) ceil($days / 30),
            default => $days,
        };
        $rate = $motor->tarif->rateFor($tipeDurasi);
        $total = round($rate * $units, 2);

        return [
            'days' => $days,
            'units' => $units,
            'rate' => $rate,
            'total' => $total,
            'tipe_durasi' => $tipeDurasi,
            'unit_label' => match ($tipeDurasi) {
                'mingguan' => $units.' minggu',
                'bulanan' => $units.' bulan',
                default => $units.' hari',
            },
        ];
    }

    public function isAvailable(Motor $motor, string $start, string $end, ?int $exceptRentalId = null): bool
    {
        if ($motor->status !== Motor::STATUS_AVAILABLE) {
            return false;
        }

        return ! Penyewaan::query()
            ->where('motor_id', $motor->id)
            ->overlapping($start, $end)
            ->when($exceptRentalId, fn ($q) => $q->whereKeyNot($exceptRentalId))
            ->exists();
    }

    /**
     * Buat pemesanan baru. Harga selalu dihitung ulang di server.
     */
    public function create(User $penyewa, Motor $motor, array $data): Penyewaan
    {
        return DB::transaction(function () use ($penyewa, $motor, $data) {
            $motor = Motor::query()->with('tarif')->lockForUpdate()->findOrFail($motor->id);

            if ($motor->status !== Motor::STATUS_AVAILABLE) {
                throw ValidationException::withMessages([
                    'motor_id' => 'Motor ini sedang tidak tersedia untuk disewa.',
                ]);
            }

            if (! $this->isAvailable($motor, $data['tanggal_mulai'], $data['tanggal_selesai'])) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Motor sudah dipesan pada periode tersebut. Silakan pilih tanggal lain.',
                ]);
            }

            $quote = $this->quote($motor, $data['tanggal_mulai'], $data['tanggal_selesai'], $data['tipe_durasi']);

            return Penyewaan::create([
                'penyewa_id' => $penyewa->id,
                'motor_id' => $motor->id,
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'tipe_durasi' => $data['tipe_durasi'],
                'harga' => $quote['total'],
                'status' => Penyewaan::STATUS_PENDING_PAYMENT,
                'catatan' => $data['catatan'] ?? null,
            ]);
        });
    }

    /**
     * Penyewaan yang sudah dikonfirmasi otomatis menjadi berlangsung saat tanggal mulai tiba.
     */
    public function syncOngoing(): int
    {
        $rentals = Penyewaan::query()
            ->where('status', Penyewaan::STATUS_BERJALAN)
            ->get();

        foreach ($rentals as $rental) {
            DB::transaction(function () use ($rental) {
                $rental->motor()->update(['status' => Motor::STATUS_RENTED]);
            });
        }

        return $rentals->count();
    }

    public function markOngoing(Penyewaan $rental): void
    {
        if ($rental->status !== Penyewaan::STATUS_BERJALAN) {
            throw ValidationException::withMessages([
                'status' => 'Hanya penyewaan yang sudah berjalan yang dapat dimulai.',
            ]);
        }

        DB::transaction(function () use ($rental) {
            $rental->update(['status' => Penyewaan::STATUS_BERJALAN]);
            $rental->motor()->update(['status' => Motor::STATUS_RENTED]);
        });
    }

    public function complete(Penyewaan $rental, ProfitSharingService $profitSharing): void
    {
        if (! in_array($rental->status, [Penyewaan::STATUS_BERJALAN], true)) {
            throw ValidationException::withMessages([
                'status' => 'Penyewaan ini belum dapat diselesaikan.',
            ]);
        }

        DB::transaction(function () use ($rental, $profitSharing) {
            $rental->update([
                'status' => Penyewaan::STATUS_COMPLETED,
                'dikembalikan_pada' => now(),
            ]);

            // PRD v1 (alur 5): setelah pengembalian dikonfirmasi, motor kembali tersedia.
            $rental->motor()->update(['status' => Motor::STATUS_AVAILABLE]);

            $profitSharing->recordFor($rental);
        });
    }

    public function cancel(Penyewaan $rental, string $reason = ''): void
    {
        if (! in_array($rental->status, [Penyewaan::STATUS_PENDING_PAYMENT, Penyewaan::STATUS_WAITING_CONFIRMATION], true)) {
            throw ValidationException::withMessages([
                'status' => 'Pesanan ini tidak dapat dibatalkan. Silakan hubungi admin.',
            ]);
        }

        DB::transaction(function () use ($rental, $reason) {
            $rental->transaksis()->where('status', Transaksi::STATUS_PENDING)->update([
                'status' => Transaksi::STATUS_REJECTED,
                'catatan_admin' => $reason ?: 'Pesanan dibatalkan.',
            ]);

            $rental->update([
                'status' => Penyewaan::STATUS_CANCELLED,
                'catatan' => trim(($rental->catatan ? $rental->catatan.' ' : '').($reason ?: 'Dibatalkan oleh penyewa.')),
            ]);
        });
    }
}
