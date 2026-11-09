<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selaraskan nilai status & metode pembayaran lama (v2) ke PRD v1.
     */
    public function up(): void
    {
        // Users: PRD v1 memakai kolom no_tlpn.
        if (Schema::hasColumn('users', 'no_telpn') && ! Schema::hasColumn('users', 'no_tlpn')) {
            Schema::table('users', function ($table) {
                $table->renameColumn('no_telpn', 'no_tlpn');
            });
        }

        // Status motor.
        foreach ([
            'pending_verification' => 'menunggu_verifikasi',
            'available' => 'tersedia',
            'rented' => 'disewa',
            'rejected' => 'ditolak',
            'inactive' => 'perawatan',
        ] as $old => $new) {
            DB::table('motors')->where('status', $old)->update(['status' => $new]);
        }

        // Status penyewaan.
        foreach ([
            'pending_payment' => 'menunggu_pembayaran',
            'waiting_confirmation' => 'menunggu_konfirmasi',
            'confirmed' => 'berjalan',
            'ongoing' => 'berjalan',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan',
        ] as $old => $new) {
            DB::table('penyewaans')->where('status', $old)->update(['status' => $new]);
        }

        // Status transaksi.
        foreach ([
            'paid' => 'berhasil',
            'failed' => 'gagal',
            'rejected' => 'gagal',
        ] as $old => $new) {
            DB::table('transaksis')->where('status', $old)->update(['status' => $new]);
        }

        // Metode pembayaran.
        foreach ([
            'transfer_bank' => 'transfer',
            'qris' => 'ewallet',
            'cash' => 'tunai',
        ] as $old => $new) {
            DB::table('transaksis')->where('metode_pembayaran', $old)->update(['metode_pembayaran' => $new]);
        }
    }

    public function down(): void
    {
        foreach ([
            'menunggu_verifikasi' => 'pending_verification',
            'tersedia' => 'available',
            'disewa' => 'rented',
            'ditolak' => 'rejected',
            'perawatan' => 'inactive',
        ] as $old => $new) {
            DB::table('motors')->where('status', $old)->update(['status' => $new]);
        }

        foreach ([
            'menunggu_pembayaran' => 'pending_payment',
            'menunggu_konfirmasi' => 'waiting_confirmation',
            'berjalan' => 'ongoing',
            'selesai' => 'completed',
            'dibatalkan' => 'cancelled',
        ] as $old => $new) {
            DB::table('penyewaans')->where('status', $old)->update(['status' => $new]);
        }

        foreach ([
            'berhasil' => 'paid',
            'gagal' => 'failed',
        ] as $old => $new) {
            DB::table('transaksis')->where('status', $old)->update(['status' => $new]);
        }

        foreach ([
            'transfer' => 'transfer_bank',
            'ewallet' => 'qris',
            'tunai' => 'cash',
        ] as $old => $new) {
            DB::table('transaksis')->where('metode_pembayaran', $old)->update(['metode_pembayaran' => $new]);
        }

        if (Schema::hasColumn('users', 'no_tlpn') && ! Schema::hasColumn('users', 'no_telpn')) {
            Schema::table('users', function ($table) {
                $table->renameColumn('no_tlpn', 'no_telpn');
            });
        }
    }
};
