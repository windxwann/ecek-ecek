<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penyewaan extends Model
{
    use HasFactory;

    // Status sesuai PRD v1 (Bagian 5 & 8.1).
    public const STATUS_PENDING_PAYMENT = 'menunggu_pembayaran';
    public const STATUS_WAITING_CONFIRMATION = 'menunggu_konfirmasi';
    public const STATUS_BERJALAN = 'berjalan';
    public const STATUS_COMPLETED = 'selesai';
    public const STATUS_CANCELLED = 'dibatalkan';

    // Alias lama agar kode yang sudah ada tetap berjalan.
    public const STATUS_CONFIRMED = self::STATUS_BERJALAN;
    public const STATUS_ONGOING = self::STATUS_BERJALAN;

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING_PAYMENT,
        self::STATUS_WAITING_CONFIRMATION,
        self::STATUS_BERJALAN,
    ];

    protected $fillable = [
        'penyewa_id',
        'motor_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_durasi',
        'harga',
        'status',
        'catatan',
        'dikembalikan_pada',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'harga' => 'decimal:2',
        'dikembalikan_pada' => 'datetime',
    ];

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class);
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'pemesanan_id');
    }

    public function transaksiTerakhir(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'pemesanan_id')->latestOfMany();
    }

    public function bagiHasil(): HasOne
    {
        return $this->hasOne(BagiHasil::class, 'pemesanan_id');
    }

    public function scopeOverlapping(Builder $query, string $start, string $end): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES)
            ->where('tanggal_mulai', '<=', $end)
            ->where('tanggal_selesai', '>=', $start);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_WAITING_CONFIRMATION => 'Menunggu Konfirmasi',
            self::STATUS_BERJALAN => 'Berjalan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getTipeDurasiLabelAttribute(): string
    {
        return ucfirst((string) $this->tipe_durasi);
    }
}
