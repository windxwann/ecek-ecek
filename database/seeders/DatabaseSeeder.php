<?php

namespace Database\Seeders;

use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\TarifRental;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@motorrent.test'],
            [
                'nama' => 'Administrator RideGo',
                'no_tlpn' => '081234567890',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        $pemilik1 = User::firstOrCreate(
            ['email' => 'budi.pemilik@motorrent.test'],
            [
                'nama' => 'Budi Santoso',
                'no_tlpn' => '081298765432',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'is_active' => true,
            ]
        );

        $pemilik2 = User::firstOrCreate(
            ['email' => 'siti.pemilik@motorrent.test'],
            [
                'nama' => 'Siti Nurhaliza',
                'no_tlpn' => '081345678901',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PEMILIK,
                'is_active' => true,
            ]
        );

        $penyewa1 = User::firstOrCreate(
            ['email' => 'andi.penyewa@motorrent.test'],
            [
                'nama' => 'Andi Wijaya',
                'no_tlpn' => '081567890123',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PENYEWA,
                'is_active' => true,
            ]
        );

        $penyewa2 = User::firstOrCreate(
            ['email' => 'dewi.penyewa@motorrent.test'],
            [
                'nama' => 'Dewi Lestari',
                'no_tlpn' => '081678901234',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PENYEWA,
                'is_active' => true,
            ]
        );

        // 2. Motors & Tarif
        $motorsData = [
            [
                'pemilik_id' => $pemilik1->id,
                'merk' => 'Honda Vario 125 CBS ISS',
                'tipe_cc' => '125',
                'no_plat' => 'B 1234 SAA',
                'status' => Motor::STATUS_AVAILABLE,
                'tarif' => ['harian' => 85000, 'mingguan' => 500000, 'bulanan' => 1700000],
            ],
            [
                'pemilik_id' => $pemilik1->id,
                'merk' => 'Yamaha NMAX 155 Connected',
                'tipe_cc' => '150',
                'no_plat' => 'B 5678 SBB',
                'status' => Motor::STATUS_AVAILABLE,
                'tarif' => ['harian' => 140000, 'mingguan' => 850000, 'bulanan' => 2800000],
            ],
            [
                'pemilik_id' => $pemilik1->id,
                'merk' => 'Honda Beat Deluxe',
                'tipe_cc' => '100',
                'no_plat' => 'B 9012 SCC',
                'status' => Motor::STATUS_RENTED,
                'tarif' => ['harian' => 70000, 'mingguan' => 420000, 'bulanan' => 1400000],
            ],
            [
                'pemilik_id' => $pemilik2->id,
                'merk' => 'Yamaha Aerox 155 VVA',
                'tipe_cc' => '150',
                'no_plat' => 'D 3456 SDD',
                'status' => Motor::STATUS_AVAILABLE,
                'tarif' => ['harian' => 135000, 'mingguan' => 800000, 'bulanan' => 2700000],
            ],
            [
                'pemilik_id' => $pemilik2->id,
                'merk' => 'Honda Scoopy Prestige',
                'tipe_cc' => '100',
                'no_plat' => 'D 7890 SEE',
                'status' => Motor::STATUS_PENDING,
                'tarif' => ['harian' => 80000, 'mingguan' => 480000, 'bulanan' => 1600000],
            ],
            [
                'pemilik_id' => $pemilik2->id,
                'merk' => 'Honda PCX 160 ABS',
                'tipe_cc' => '150',
                'no_plat' => 'D 1122 SFF',
                'status' => Motor::STATUS_AVAILABLE,
                'tarif' => ['harian' => 150000, 'mingguan' => 900000, 'bulanan' => 3000000],
            ],
        ];

        $createdMotors = [];
        foreach ($motorsData as $mData) {
            $tarif = $mData['tarif'];
            unset($mData['tarif']);

            $motor = Motor::firstOrCreate(
                ['no_plat' => $mData['no_plat']],
                $mData
            );

            TarifRental::updateOrCreate(
                ['motor_id' => $motor->id],
                [
                    'tarif_harian' => $tarif['harian'],
                    'tarif_mingguan' => $tarif['mingguan'],
                    'tarif_bulanan' => $tarif['bulanan'],
                ]
            );

            $createdMotors[] = $motor;
        }

        // 3. Sample Rentals, Payments, and Profit Sharing
        $rentedMotor = $createdMotors[2]; // Beat Deluxe
        $availableMotor1 = $createdMotors[0]; // Vario 125
        $availableMotor2 = $createdMotors[1]; // NMAX 155

        // Completed Rental
        $completedRental = Penyewaan::firstOrCreate(
            [
                'penyewa_id' => $penyewa1->id,
                'motor_id' => $availableMotor1->id,
                'tanggal_mulai' => Carbon::now()->subDays(10)->toDateString(),
            ],
            [
                'tanggal_selesai' => Carbon::now()->subDays(7)->toDateString(),
                'tipe_durasi' => 'harian',
                'harga' => 255000,
                'status' => Penyewaan::STATUS_COMPLETED,
                'catatan' => 'Sewa 3 hari keliling kota',
                'dikembalikan_pada' => Carbon::now()->subDays(7)->addHours(2),
            ]
        );

        $transaksiCompleted = Transaksi::firstOrCreate(
            ['pemesanan_id' => $completedRental->id],
            [
                'jumlah' => 255000,
                'metode_pembayaran' => 'transfer',
                'status' => Transaksi::STATUS_PAID,
                'tanggal' => Carbon::now()->subDays(10),
            ]
        );

        BagiHasil::firstOrCreate(
            ['pemesanan_id' => $completedRental->id],
            [
                'bagi_hasil_pemilik' => 178500, // 70%
                'bagi_hasil_admin' => 76500,    // 30%
                'tanggal' => Carbon::now()->subDays(7)->toDateString(),
                'settled_at' => Carbon::now()->subDays(6),
            ]
        );

        // Ongoing Rental
        $ongoingRental = Penyewaan::firstOrCreate(
            [
                'penyewa_id' => $penyewa2->id,
                'motor_id' => $rentedMotor->id,
                'tanggal_mulai' => Carbon::now()->subDays(2)->toDateString(),
            ],
            [
                'tanggal_selesai' => Carbon::now()->addDays(3)->toDateString(),
                'tipe_durasi' => 'harian',
                'harga' => 350000,
                'status' => Penyewaan::STATUS_ONGOING,
                'catatan' => 'Sewa keperluan kerja seminggu',
            ]
        );

        Transaksi::firstOrCreate(
            ['pemesanan_id' => $ongoingRental->id],
            [
                'jumlah' => 350000,
                'metode_pembayaran' => 'ewallet',
                'status' => Transaksi::STATUS_PAID,
                'tanggal' => Carbon::now()->subDays(2),
            ]
        );

        // Waiting Confirmation Rental
        $waitingRental = Penyewaan::firstOrCreate(
            [
                'penyewa_id' => $penyewa1->id,
                'motor_id' => $availableMotor2->id,
                'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            ],
            [
                'tanggal_selesai' => Carbon::now()->addDays(5)->toDateString(),
                'tipe_durasi' => 'harian',
                'harga' => 420000,
                'status' => Penyewaan::STATUS_WAITING_CONFIRMATION,
                'catatan' => 'Trip akhir pekan ke luar kota',
            ]
        );

        Transaksi::firstOrCreate(
            ['pemesanan_id' => $waitingRental->id],
            [
                'jumlah' => 420000,
                'metode_pembayaran' => 'transfer',
                'status' => Transaksi::STATUS_PENDING,
                'tanggal' => Carbon::now(),
            ]
        );
    }
}
