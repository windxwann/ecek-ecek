<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('penyewaans')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->string('metode_pembayaran', 30);
            $table->string('status', 20)->default('pending');
            $table->dateTime('tanggal');
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
