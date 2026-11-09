<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('motor_id')->constrained('motors')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('tipe_durasi', 20);
            $table->decimal('harga', 12, 2);
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->text('catatan')->nullable();
            $table->timestamp('dikembalikan_pada')->nullable();
            $table->timestamps();

            $table->index(['motor_id', 'status']);
            $table->index(['penyewa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewaans');
    }
};
