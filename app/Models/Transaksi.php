<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    // Status sesuai PRD v1 (Bagian 8.1).
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'berhasil';
    public const STATUS_FAILED = 'gagal';

    // Alias lama agar kode yang sudah ada tetap berjalan.
    public const STATUS_PAID = self::STATUS_SUCCESS;
    public const STATUS_REJECTED = self::STATUS_FAILED;

    protected $fillable = [
        'pemesanan_id',
        'jumlah',
        'metode_pembayaran',
        'status',
        'tanggal',
        'bukti_pembayaran',
        'catatan_admin',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'datetime',
    ];

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class, 'pemesanan_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_SUCCESS => 'Berhasil',
            self::STATUS_FAILED => 'Gagal',
            default => ucfirst((string) $this->status),
        };
    }

    public function getMetodeLabelAttribute(): string
    {
        return match ($this->metode_pembayaran) {
            'transfer' => 'Transfer',
            'ewallet' => 'E-Wallet',
            'tunai' => 'Tunai',
            default => ucfirst(str_replace('_', ' ', (string) $this->metode_pembayaran)),
        };
    }
}
