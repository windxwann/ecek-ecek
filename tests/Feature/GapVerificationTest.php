<?php

namespace Tests\Feature;

use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GapVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_view_user_detail_and_payment_report(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $pemilik = User::where('role', User::ROLE_PEMILIK)->first();

        $this->actingAs($admin)
            ->get(route('admin.users.show', $pemilik))
            ->assertOk()
            ->assertSee($pemilik->nama);

        $this->actingAs($admin)
            ->get(route('admin.reports.payments'))
            ->assertOk()
            ->assertSee('Laporan Pembayaran');
    }

    public function test_admin_can_view_and_delete_motor_tariff(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $motor = Motor::where('status', Motor::STATUS_AVAILABLE)->with('tarif')->first();

        $this->assertNotNull($motor->tarif);

        $this->actingAs($admin)
            ->get(route('admin.motors.show', $motor))
            ->assertOk()
            ->assertSee('Manajemen Tarif Rental');

        $this->actingAs($admin)
            ->delete(route('admin.motors.pricing.destroy', $motor))
            ->assertRedirect();

        $motor->refresh();

        $this->assertNull($motor->tarif);
        $this->assertEquals(Motor::STATUS_INACTIVE, $motor->status);
    }

    public function test_prd_v1_database_objects_exist_and_function_works(): void
    {
        $procedure = DB::select("SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'sp_buat_penyewaan'");
        $function = DB::select("SHOW FUNCTION STATUS WHERE Db = DATABASE() AND Name = 'fn_hitung_biaya'");
        $trigger = DB::select("SHOW TRIGGERS WHERE `Trigger` = 'trg_bagi_hasil'");
        $views = DB::select("SHOW FULL TABLES WHERE Table_type = 'VIEW'");

        $this->assertNotEmpty($procedure);
        $this->assertNotEmpty($function);
        $this->assertNotEmpty($trigger);
        $this->assertGreaterThanOrEqual(3, count($views));

        $motor = Motor::where('status', Motor::STATUS_AVAILABLE)->with('tarif')->first();
        $this->assertNotNull($motor);

        $result = DB::selectOne('SELECT fn_hitung_biaya(?, ?, ?) AS total', [$motor->id, 'harian', 3]);

        $this->assertEquals((float) $motor->tarif->tarif_harian * 3, (float) $result->total);
    }

    public function test_admin_can_manage_payments_manually(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $rental = Penyewaan::where('status', Penyewaan::STATUS_WAITING_CONFIRMATION)
            ->whereDoesntHave('transaksis', fn ($q) => $q->where('status', Transaksi::STATUS_SUCCESS))
            ->with('motor')
            ->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.payments.create'))
            ->assertOk()
            ->assertSee('Entri Transaksi Pembayaran');

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'pemesanan_id' => $rental->id,
                'jumlah' => 150000,
                'metode_pembayaran' => 'tunai',
                'status' => 'pending',
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'catatan_admin' => 'Diterima di kantor.',
            ])
            ->assertRedirect(route('admin.payments.index'));

        $transaksi = Transaksi::where('pemesanan_id', $rental->id)
            ->where('metode_pembayaran', 'tunai')
            ->latest('id')
            ->firstOrFail();

        $this->assertEquals(150000, (float) $transaksi->jumlah);
        $this->assertEquals(Transaksi::STATUS_PENDING, $transaksi->status);

        $this->actingAs($admin)
            ->put(route('admin.payments.update', $transaksi), [
                'pemesanan_id' => $rental->id,
                'jumlah' => 175000,
                'metode_pembayaran' => 'transfer',
                'status' => Transaksi::STATUS_SUCCESS,
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'catatan_admin' => 'Pembayaran diverifikasi.',
            ])
            ->assertRedirect(route('admin.payments.index'));

        $transaksi->refresh();
        $rental->refresh();

        $this->assertEquals(Transaksi::STATUS_SUCCESS, $transaksi->status);
        $this->assertEquals(Penyewaan::STATUS_BERJALAN, $rental->status);
        $this->assertEquals(Motor::STATUS_RENTED, $rental->motor->status);

        $this->actingAs($admin)
            ->delete(route('admin.payments.destroy', $transaksi))
            ->assertRedirect(route('admin.payments.index'));

        $this->assertDatabaseMissing('transaksis', ['id' => $transaksi->id]);
    }
}
