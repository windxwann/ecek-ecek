<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Motor extends Model
{
    use HasFactory;

    // Status sesuai PRD v1 (Bagian 5 & 8.1).
    public const STATUS_PENDING = 'menunggu_verifikasi';
    public const STATUS_AVAILABLE = 'tersedia';
    public const STATUS_RENTED = 'disewa';
    public const STATUS_REJECTED = 'ditolak';
    public const STATUS_MAINTENANCE = 'perawatan';

    // Alias lama agar kode yang sudah ada tetap berjalan.
    public const STATUS_INACTIVE = self::STATUS_MAINTENANCE;

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_AVAILABLE,
        self::STATUS_RENTED,
        self::STATUS_MAINTENANCE,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'pemilik_id',
        'merk',
        'tipe_cc',
        'no_plat',
        'status',
        'photo',
        'dokumen_kepemilikan',
        'catatan_admin',
    ];

    protected $casts = [
        'tipe_cc' => 'string',
    ];

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function tarif(): HasOne
    {
        return $this->hasOne(TarifRental::class);
    }

    public function penyewaans(): HasMany
    {
        return $this->hasMany(Penyewaan::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeOfOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('pemilik_id', $ownerId);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_AVAILABLE => 'Tersedia',
            self::STATUS_RENTED => 'Disewa',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_MAINTENANCE => 'Perawatan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
