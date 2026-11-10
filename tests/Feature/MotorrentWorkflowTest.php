<?php

namespace Tests\Feature;

use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MotorrentWorkflowTest extends TestCase
{
    use DatabaseTransactions;
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('RideGo');
    }

    public function test_marketplace_catalog_displays_available_motors(): void
    {
        $response = $this->get('/motors');
        $response->assertStatus(200);
        $response->assertSee('Katalog Motor Disewakan');
    }

    public function test_login_and_role_redirect(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->assertNotNull($admin);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_api_login_returns_token(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->postJson('/api/auth/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['user', 'token'],
        ]);
    }

    public function test_complete_rental_workflow_and_profit_sharing(): void
    {
        $pemilik = User::where('role', User::ROLE_PEMILIK)->first();
        $penyewa = User::where('role', User::ROLE_PENYEWA)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        // 1. Motor available
        $motor = Motor::where('status', Motor::STATUS_AVAILABLE)->first();
        $this->assertNotNull($motor);

        // 2. Tenant creates rental
        $this->actingAs($penyewa);
        $response = $this->post(route('tenant.rentals.store', $motor), [
            'tanggal_mulai' => now()->addDays(15)->toDateString(),
            'tanggal_selesai' => now()->addDays(17)->toDateString(),
            'tipe_durasi' => 'harian',
            'catatan' => 'Test Rental Otomatis',
        ]);

        $rental = Penyewaan::where('penyewa_id', $penyewa->id)->latest()->first();
        $this->assertNotNull($rental);
        $this->assertEquals(Penyewaan::STATUS_PENDING_PAYMENT, $rental->status);

        // 3. Tenant submits payment
        Storage::fake('local');
        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->post(route('tenant.payments.store', $rental), [
            'metode_pembayaran' => 'transfer',
            'bukti_pembayaran' => $file,
        ]);

        $rental->refresh();
        $this->assertEquals(Penyewaan::STATUS_WAITING_CONFIRMATION, $rental->status);

        // 4. Admin confirms payment
        $this->actingAs($admin);
        $tx = $rental->transaksiTerakhir;
        $this->assertNotNull($tx);

        $response = $this->post(route('admin.payments.confirm', $tx));
        $rental->refresh();
        $this->assertEquals(Penyewaan::STATUS_CONFIRMED, $rental->status);

        // 5. Admin starts rental (ongoing)
        $this->post(route('admin.rentals.ongoing', $rental));
        $rental->refresh();
        $this->assertEquals(Penyewaan::STATUS_ONGOING, $rental->status);
        $this->assertEquals(Motor::STATUS_RENTED, $rental->motor->status);

        // 6. Admin confirms return (completed) -> Bagi Hasil automatically created!
        $this->post(route('admin.rentals.return', $rental));
        $rental->refresh();
        $this->assertEquals(Penyewaan::STATUS_COMPLETED, $rental->status);
        $this->assertEquals(Motor::STATUS_AVAILABLE, $rental->motor->status);

        $bagiHasil = $rental->bagiHasil;
        $this->assertNotNull($bagiHasil);
        // Owner 70%, Admin 30%
        $expectedOwner = round((float) $rental->harga * 0.70, 2);
        $this->assertEquals($expectedOwner, (float) $bagiHasil->bagi_hasil_pemilik);
    }

    public function test_owner_motor_registration_and_admin_verification(): void
    {
        $pemilik = User::where('role', User::ROLE_PEMILIK)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        Storage::fake('local');
        Storage::fake('public');

        $plat = 'B '.rand(1000, 9999).' '.strtoupper(substr(md5(uniqid()), 0, 3));

        $this->actingAs($pemilik);
        $response = $this->post(route('owner.motors.store'), [
            'merk' => 'Yamaha Fazzio 125 Hybrid',
            'tipe_cc' => '125',
            'no_plat' => $plat,
            'dokumen_kepemilikan' => UploadedFile::fake()->create('stnk.pdf', 500, 'application/pdf'),
        ]);

        $response->assertRedirect(route('owner.motors.index'));

        $motor = Motor::where('no_plat', $plat)->first();
        $this->assertNotNull($motor);
        $this->assertEquals(Motor::STATUS_PENDING, $motor->status);

        // Admin approves
        $this->actingAs($admin);
        $this->post(route('admin.motors.verify', $motor), [
            'action' => 'approve',
            'catatan_admin' => 'Unit diverifikasi dan siap disewa.',
        ]);

        $motor->refresh();
        $this->assertEquals(Motor::STATUS_AVAILABLE, $motor->status);
    }

    public function test_api_rest_endpoints(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $penyewa = User::where('role', User::ROLE_PENYEWA)->first();

        // Public Catalog
        $response = $this->getJson('/api/motors');
        $response->assertStatus(200);

        // Admin Endpoints
        $this->actingAs($admin, 'sanctum');
        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(200);

        $response = $this->getJson('/api/admin/reports/revenue');
        $response->assertStatus(200);

        // Tenant Endpoints
        $this->actingAs($penyewa, 'sanctum');
        $response = $this->getJson('/api/bookings/history');
        $response->assertStatus(200);

        // Backward-compatible alias
        $response = $this->getJson('/api/v1/motors');
        $response->assertStatus(200);
    }
}
