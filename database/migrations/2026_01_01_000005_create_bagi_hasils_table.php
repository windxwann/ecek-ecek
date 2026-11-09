<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bagi_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->unique()->constrained('penyewaans')->cascadeOnDelete();
            $table->decimal('bagi_hasil_pemilik', 12, 2);
            $table->decimal('bagi_hasil_admin', 12, 2);
            $table->date('tanggal');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bagi_hasils');
    }
};
