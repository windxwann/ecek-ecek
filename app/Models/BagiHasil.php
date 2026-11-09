<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BagiHasil extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_id',
        'bagi_hasil_pemilik',
        'bagi_hasil_admin',
        'tanggal',
        'settled_at',
    ];

    protected $casts = [
        'bagi_hasil_pemilik' => 'decimal:2',
        'bagi_hasil_admin' => 'decimal:2',
        'tanggal' => 'date',
        'settled_at' => 'datetime',
    ];

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class, 'pemesanan_id');
    }

    public function isSettled(): bool
    {
        return $this->settled_at !== null;
    }
}
