<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'motor_id',
        'tarif_harian',
        'tarif_mingguan',
        'tarif_bulanan',
    ];

    protected $casts = [
        'tarif_harian' => 'decimal:2',
        'tarif_mingguan' => 'decimal:2',
        'tarif_bulanan' => 'decimal:2',
    ];

    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class);
    }

    public function rateFor(string $tipeDurasi): float
    {
        return (float) match ($tipeDurasi) {
            'mingguan' => $this->tarif_mingguan,
            'bulanan' => $this->tarif_bulanan,
            default => $this->tarif_harian,
        };
    }
}
