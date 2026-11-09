<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->unique()->constrained('motors')->cascadeOnDelete();
            $table->decimal('tarif_harian', 12, 2)->default(0);
            $table->decimal('tarif_mingguan', 12, 2)->default(0);
            $table->decimal('tarif_bulanan', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_rentals');
    }
};
