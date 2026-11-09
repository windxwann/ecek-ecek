# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# MOTORRENT – Motorcycle Rental Management Platform

**Versi:** 2.0  
**Platform:** Website Application  
**Backend:** Laravel 12  
**Database:** MySQL  
**Frontend:** Blade, Tailwind CSS, Alpine.js  
**API:** REST API  
**Design Concept:** Modern Minimalist SaaS  
**Status:** Draft – Development Reference

---

# 1. Project Overview

## 1.1 Deskripsi Produk

MOTORRENT merupakan aplikasi berbasis website yang menyediakan layanan penyewaan motor dengan sistem kemitraan antara pemilik kendaraan, admin, dan penyewa.

Aplikasi ini bertujuan mempermudah pemilik kendaraan dalam menitipkan dan mengelola motor untuk disewakan, serta memberikan kemudahan kepada penyewa dalam mencari, memilih, dan melakukan pemesanan motor secara online.

Admin bertindak sebagai pengelola utama yang bertanggung jawab terhadap verifikasi kendaraan, penentuan tarif rental, konfirmasi transaksi, pengembalian kendaraan, serta pengelolaan laporan pendapatan dan bagi hasil.

## 1.2 Tujuan Produk

1. Memfasilitasi pemilik motor untuk menyewakan kendaraannya.
2. Mempermudah penyewa dalam mencari dan memesan motor.
3. Menyediakan sistem pengelolaan penyewaan terpusat.
4. Mengotomatisasi perhitungan biaya penyewaan.
5. Mengelola transaksi pembayaran secara terstruktur.
6. Menghasilkan laporan pendapatan dan pembagian hasil.
7. Menyediakan transparansi aktivitas penyewaan kepada setiap pengguna.

## 1.3 Target Pengguna

| Role | Deskripsi |
|---|---|
| Admin | Pengelola utama seluruh aktivitas platform. |
| Pemilik | Mitra yang mendaftarkan motor untuk disewakan. |
| Penyewa | Pengguna yang mencari dan menyewa motor. |

---

# 2. Design System & UI/UX

## 2.1 Konsep Desain

Menggunakan konsep **Modern Minimalist SaaS Dashboard** dengan pendekatan desain yang berbeda untuk setiap role.

Karakteristik desain:
- Clean dan minimalis.
- Profesional dan modern.
- Navigasi sederhana.
- Informasi ditampilkan secara hierarkis.
- Mengutamakan kemudahan penggunaan.
- Responsive untuk desktop, tablet, dan mobile.

## 2.2 Color Palette

| Elemen | Warna | Hex |
|---|---|---|
| Primary | Royal Blue | #2563EB |
| Secondary | Dark Navy | #0F172A |
| Background | Light Slate | #F8FAFC |
| Surface | White | #FFFFFF |
| Success | Green | #16A34A |
| Warning | Amber | #F59E0B |
| Danger | Red | #EF4444 |
| Text | Slate | #334155 |

## 2.3 Typography

Menggunakan font **Inter** sebagai tipografi utama.

Hierarki:
- Heading: Semibold / Bold.
- Subheading: Medium.
- Body: Regular.
- Caption: Regular.

## 2.4 Komponen UI

Komponen yang digunakan secara konsisten:

- Sidebar Navigation.
- Top Navbar.
- Stat Cards.
- Data Tables.
- Search Input.
- Filter Dropdown.
- Modal Dialog.
- Status Badge.
- Pagination.
- Confirmation Dialog.
- Form Input.
- Chart Visualization.
- Empty State.
- Toast Notification.

## 2.5 Layout Berdasarkan Role

### Admin Dashboard

Menggunakan sidebar dengan navigasi lengkap untuk mengelola seluruh sistem.

### Owner Dashboard

Menggunakan sidebar khusus pemilik dengan fokus pengelolaan motor, penyewaan, dan pendapatan.

### Tenant Marketplace

Menggunakan tampilan marketplace dengan katalog motor, pencarian, detail kendaraan, dan proses pemesanan.

---

# 3. Functional Requirements

## 3.1 Authentication & Authorization

Fitur:
- Registrasi akun.
- Login.
- Logout.
- Validasi email.
- Manajemen profil.
- Role-based access control.
- Middleware berdasarkan role.

Ketentuan:
- Pengguna dapat melakukan registrasi sebagai pemilik atau penyewa.
- Admin memiliki akun yang dibuat atau dikelola oleh administrator sistem.
- Setiap pengguna hanya dapat mengakses fitur sesuai hak aksesnya.

## 3.2 User Management

Admin dapat:
- Melihat seluruh pengguna.
- Menambahkan pengguna.
- Mengubah data pengguna.
- Menghapus atau menonaktifkan pengguna.
- Melihat detail pengguna.
- Mengelola role pengguna.

Data pengguna:
- ID
- Nama
- Email
- No. Telepon
- Role

## 3.3 Motorcycle Management

Pemilik dapat mendaftarkan motor dengan mengisi:

- Merk motor.
- Tipe CC (100, 125, 150).
- Nomor polisi.
- Foto motor.
- Dokumen kepemilikan.

Admin dapat:
- Melihat motor yang didaftarkan.
- Memverifikasi data motor.
- Menyetujui atau menolak pendaftaran.
- Menentukan tarif rental.
- Mengelola status motor.

Status motor:
- Pending Verification.
- Available.
- Rented.
- Rejected.
- Inactive.

Ketentuan:
- Motor tidak dapat disewakan sebelum diverifikasi admin.
- Motor yang sedang disewa tidak dapat dipesan kembali pada periode yang bertabrakan.
- Pemilik hanya dapat mengelola motor miliknya sendiri.

## 3.4 Rental Pricing Management

Admin bertanggung jawab menentukan harga rental setiap motor.

Jenis tarif:
- Tarif harian.
- Tarif mingguan.
- Tarif bulanan.

Admin dapat:
- Menambahkan tarif.
- Mengubah tarif.
- Menghapus tarif.
- Melihat tarif berdasarkan motor.

## 3.5 Rental Management

Penyewa dapat:
- Melihat motor tersedia.
- Memilih motor.
- Menentukan tanggal mulai.
- Menentukan tanggal selesai.
- Memilih tipe durasi.
- Melihat estimasi biaya.
- Melakukan pemesanan.
- Melihat riwayat penyewaan.

Admin dapat:
- Melihat seluruh pemesanan.
- Mengonfirmasi pesanan.
- Menolak pesanan.
- Melihat detail penyewaan.
- Mengonfirmasi pengembalian motor.

Status penyewaan:
- Pending Payment.
- Waiting Confirmation.
- Confirmed.
- Ongoing.
- Completed.
- Cancelled.

## 3.6 Payment Management

Penyewa dapat melakukan pembayaran sesuai metode yang tersedia.

Admin dapat:
- Melihat transaksi.
- Memeriksa pembayaran.
- Mengonfirmasi pembayaran.
- Menolak pembayaran jika tidak valid.
- Melihat riwayat transaksi.

Status pembayaran:
- Pending.
- Paid.
- Failed.
- Rejected.

## 3.7 Revenue Sharing Management

Sistem menghitung pembagian pendapatan antara pemilik dan admin.

Data yang dicatat:
- ID penyewaan.
- Pendapatan pemilik.
- Pendapatan admin.
- Tanggal pencatatan.
- Waktu penyelesaian bagi hasil.

Persentase pembagian hasil ditentukan oleh kebijakan platform.

Admin dapat:
- Melihat seluruh bagi hasil.
- Melihat detail pembagian.
- Mengelola penyelesaian bagi hasil.
- Menghasilkan laporan bagi hasil.

Pemilik dapat:
- Melihat pendapatan motor miliknya.
- Melihat riwayat bagi hasil.
- Melihat status penyelesaian pendapatan.

## 3.8 Reporting & Analytics

Dashboard menyediakan informasi statistik berdasarkan role.

**Admin:**
- Total pengguna.
- Total motor.
- Total motor tersedia.
- Total motor disewa.
- Total transaksi.
- Total pendapatan.
- Grafik penyewaan per periode.
- Laporan pembayaran.
- Laporan bagi hasil.

**Pemilik:**
- Total motor terdaftar.
- Motor tersedia.
- Motor sedang disewa.
- Total pendapatan.
- Grafik penyewaan.
- Riwayat bagi hasil.

**Penyewa:**
- Riwayat penyewaan.
- Riwayat pembayaran.
- Status pemesanan.

---

# 4. Database Design

## 4.1 Users

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| nama | VARCHAR | Nama pengguna |
| email | VARCHAR | Email pengguna |
| no_telpn | VARCHAR | Nomor telepon |
| password | VARCHAR | Password terenkripsi |
| role | ENUM | pemilik, admin, penyewa |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.2 Motor

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| pemilik_id | BIGINT | FK Users |
| merk | VARCHAR | Merk motor |
| tipe_cc | ENUM | 100, 125, 150 |
| no_plat | VARCHAR | Nomor polisi |
| status | VARCHAR | Status motor |
| photo | VARCHAR | Foto motor |
| dokumen_kepemilikan | VARCHAR | Dokumen motor |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.3 Tarif_Rental

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| motor_id | BIGINT | FK Motor |
| tarif_harian | DECIMAL | Tarif harian |
| tarif_mingguan | DECIMAL | Tarif mingguan |
| tarif_bulanan | DECIMAL | Tarif bulanan |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.4 Penyewaan

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| penyewa_id | BIGINT | FK Users |
| motor_id | BIGINT | FK Motor |
| tanggal_mulai | DATE | Tanggal mulai |
| tanggal_selesai | DATE | Tanggal selesai |
| tipe_durasi | VARCHAR | Jenis durasi |
| harga | DECIMAL | Total harga |
| status | VARCHAR | Status penyewaan |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.5 Transaksi

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| pemesanan_id | BIGINT | FK Penyewaan |
| jumlah | DECIMAL | Jumlah pembayaran |
| metode_pembayaran | VARCHAR | Metode pembayaran |
| status | VARCHAR | Status transaksi |
| tanggal | DATETIME | Tanggal transaksi |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.6 Bagi_Hasil

| Field | Type | Keterangan |
|---|---|---|
| id | BIGINT | Primary Key |
| pemesanan_id | BIGINT | FK Penyewaan |
| bagi_hasil_pemilik | DECIMAL | Bagian pemilik |
| bagi_hasil_admin | DECIMAL | Bagian admin |
| settled_at | DATETIME | Waktu penyelesaian |
| tanggal | DATE | Tanggal pencatatan |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diperbarui |

## 4.7 Relasi Database

- Users memiliki banyak Motor.
- Users memiliki banyak Penyewaan sebagai penyewa.
- Motor dimiliki oleh satu User.
- Motor memiliki Tarif_Rental.
- Motor memiliki banyak Penyewaan.
- Penyewaan memiliki Transaksi.
- Penyewaan memiliki catatan Bagi_Hasil.

---

# 5. Business Process & Workflow

## 5.1 Pendaftaran Motor

1. Pemilik melakukan login.
2. Pemilik membuka menu Motor Saya.
3. Pemilik memilih Tambah Motor.
4. Pemilik mengisi informasi motor.
5. Pemilik mengunggah foto dan dokumen kepemilikan.
6. Sistem menyimpan data dengan status Pending Verification.
7. Admin melakukan pemeriksaan.
8. Admin menyetujui atau menolak pendaftaran.
9. Jika disetujui, admin menentukan tarif rental.
10. Motor muncul dalam katalog apabila sudah tersedia.

## 5.2 Proses Penyewaan

1. Penyewa membuka katalog motor.
2. Penyewa memilih motor.
3. Penyewa membuka detail motor.
4. Penyewa memilih tanggal penyewaan.
5. Sistem memeriksa ketersediaan motor.
6. Sistem menghitung estimasi harga.
7. Penyewa membuat pemesanan.
8. Penyewa melakukan pembayaran.
9. Admin memeriksa dan mengonfirmasi pembayaran.
10. Sistem mengubah status penyewaan menjadi Confirmed.
11. Ketika masa sewa dimulai, status menjadi Ongoing.
12. Setelah masa sewa selesai, penyewa mengembalikan motor.

## 5.3 Proses Pengembalian

1. Penyewa mengembalikan motor.
2. Admin memeriksa kondisi dan kelengkapan motor.
3. Admin mengonfirmasi pengembalian.
4. Sistem mengubah status penyewaan menjadi Completed.
5. Sistem mengubah status motor menjadi Available apabila tidak ada pemesanan lain yang sudah terkonfirmasi untuk periode berikutnya.
6. Sistem mencatat pembagian hasil.
7. Sistem memperbarui laporan pendapatan pemilik dan admin.

## 5.4 Proses Bagi Hasil

1. Penyewaan selesai.
2. Sistem mengambil total pendapatan penyewaan.
3. Sistem menghitung bagian pemilik dan admin sesuai persentase kebijakan.
4. Sistem menyimpan hasil perhitungan.
5. Admin melakukan penyelesaian bagi hasil.
6. Pemilik dapat melihat riwayat pendapatan.

---

# 6. Role & Permission Matrix

| Fitur | Admin | Pemilik | Penyewa |
|---|:---:|:---:|:---:|
| Registrasi | ✓ | ✓ | ✓ |
| Login / Logout | ✓ | ✓ | ✓ |
| CRUD User | ✓ | - | - |
| CRUD Motor | ✓ | ✓ | - |
| Verifikasi Motor | ✓ | - | - |
| CRUD Tarif | ✓ | - | - |
| Melihat Katalog | ✓ | - | ✓ |
| CRUD Penyewaan | ✓ | - | ✓ |
| Konfirmasi Pesanan | ✓ | - | - |
| Konfirmasi Pembayaran | ✓ | - | - |
| Konfirmasi Pengembalian | ✓ | - | - |
| Riwayat Penyewaan | ✓ | ✓ | ✓ |
| Riwayat Bagi Hasil | ✓ | ✓ | - |
| Grafik Penyewaan | ✓ | ✓ | - |
| Laporan Pendapatan | ✓ | ✓ | - |
| Laporan Pembayaran | ✓ | - | - |

Catatan: CRUD pada penyewaan untuk penyewa dibatasi pada pemesanan miliknya sendiri dan pembatalan sesuai ketentuan sistem.

---

# 7. REST API Requirements

API menggunakan format JSON dengan autentikasi Laravel Sanctum.

Base URL:

`/api/v1`

## 7.1 Authentication

| Method | Endpoint | Fungsi |
|---|---|---|
| POST | /register | Registrasi |
| POST | /login | Login |
| POST | /logout | Logout |
| GET | /user | Profil pengguna |

## 7.2 Pemilik

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | /owner/motors | Daftar motor |
| POST | /owner/motors | Tambah motor |
| GET | /owner/motors/{id} | Detail motor |
| PATCH | /owner/motors/{id} | Ubah motor |
| DELETE | /owner/motors/{id} | Hapus motor |
| GET | /owner/rentals | Riwayat penyewaan |
| GET | /owner/revenue | Laporan pendapatan |
| GET | /owner/profit-sharing | Riwayat bagi hasil |

## 7.3 Penyewa

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | /motors | Katalog motor |
| GET | /motors/{id} | Detail motor |
| POST | /tenant/rentals | Membuat pemesanan |
| GET | /tenant/rentals | Riwayat penyewaan |
| GET | /tenant/rentals/{id} | Detail pemesanan |
| POST | /tenant/payments | Membuat pembayaran |
| GET | /tenant/payments | Riwayat pembayaran |

## 7.4 Admin

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | /admin/users | Daftar pengguna |
| GET | /admin/motors | Daftar motor |
| PATCH | /admin/motors/{id}/verify | Verifikasi motor |
| PATCH | /admin/motors/{id}/pricing | Menentukan tarif |
| GET | /admin/rentals | Daftar penyewaan |
| PATCH | /admin/rentals/{id}/confirm | Konfirmasi pesanan |
| PATCH | /admin/rentals/{id}/return | Konfirmasi pengembalian |
| GET | /admin/payments | Daftar pembayaran |
| PATCH | /admin/payments/{id}/confirm | Konfirmasi pembayaran |
| GET | /admin/reports/revenue | Laporan pendapatan |
| GET | /admin/reports/profit-sharing | Laporan bagi hasil |

Semua endpoint harus menerapkan autentikasi dan pemeriksaan otorisasi berdasarkan role.

---

# 8. Technical Requirements – Laravel 12

## 8.1 Technology Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 |
| Database | MySQL |
| Frontend | Blade |
| CSS | Tailwind CSS |
| JavaScript | Alpine.js |
| Authentication | Laravel Authentication |
| API Authentication | Laravel Sanctum |
| Chart | Chart.js |
| Icons | Lucide Icons |
| Notification | SweetAlert2 |
| Animation | GSAP (Opsional) |

## 8.2 Struktur Folder

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Owner/
│   │   ├── Tenant/
│   │   └── Api/
│   │
│   ├── Middleware/
│   └── Requests/
│
├── Models/
│   ├── User.php
│   ├── Motor.php
│   ├── TarifRental.php
│   ├── Penyewaan.php
│   ├── Transaksi.php
│   └── BagiHasil.php
│
├── Services/
│   ├── RentalService.php
│   ├── PaymentService.php
│   └── ProfitSharingService.php
│
resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── admin/
│   ├── owner/
│   ├── tenant/
│   └── auth/
│
routes/
├── web.php
└── api.php
```

---

# 9. Non-Functional Requirements

## 9.1 Security
- Password harus di-hash.
- Menggunakan CSRF protection.
- Validasi input pada server.
- Middleware untuk setiap role.
- Pemilik hanya dapat mengakses data motor miliknya.
- Penyewa hanya dapat mengakses pemesanan miliknya.
- Dokumen kepemilikan tidak boleh diakses secara publik.
- API menggunakan token autentikasi.

## 9.2 Performance
- Menggunakan pagination pada tabel.
- Mengoptimalkan gambar motor.
- Menggunakan eager loading untuk mengurangi query berulang.
- Mengoptimalkan query laporan.
- Menghindari pemrosesan data besar secara langsung pada halaman dashboard.

## 9.3 Responsiveness
- Desktop: Sidebar dan konten utama.
- Tablet: Sidebar dapat diperkecil.
- Mobile: Navigasi menggunakan drawer atau menu mobile.
- Semua form dan tabel harus tetap dapat digunakan pada layar kecil.

## 9.4 Reliability
- Proses transaksi menggunakan database transaction.
- Mencegah pemesanan ganda pada periode yang sama.
- Perhitungan harga dan bagi hasil dilakukan di server.
- Perubahan status transaksi harus tercatat dengan benar.

---

# 10. Acceptance Criteria

Aplikasi dianggap memenuhi kebutuhan utama apabila:

1. Pengguna dapat melakukan registrasi dan login.
2. Setiap role hanya dapat mengakses fitur yang diizinkan.
3. Pemilik dapat mendaftarkan motor.
4. Admin dapat memverifikasi motor.
5. Admin dapat menentukan tarif rental.
6. Penyewa dapat melihat motor tersedia.
7. Sistem dapat menghitung biaya penyewaan.
8. Penyewa dapat membuat pemesanan.
9. Admin dapat mengonfirmasi pembayaran.
10. Sistem dapat memperbarui status motor dan penyewaan.
11. Admin dapat mengonfirmasi pengembalian.
12. Sistem dapat mencatat pembagian hasil.
13. Pemilik dapat melihat laporan pendapatan.
14. Admin dapat melihat laporan penyewaan dan pendapatan.
15. REST API dapat digunakan sesuai hak akses masing-masing role.
16. Tampilan aplikasi responsif pada desktop dan mobile.

---

# 11. Development Roadmap

Pengembangan dilakukan secara bertahap agar setiap modul dapat diuji sebelum melanjutkan ke modul berikutnya.

| Tahap | Modul | Prioritas |
|---|---|---|
| 1 | Setup Laravel 12 dan database | Tinggi |
| 2 | Authentication dan role management | Tinggi |
| 3 | Dashboard dan layout UI | Tinggi |
| 4 | CRUD motor | Tinggi |
| 5 | Verifikasi motor dan tarif | Tinggi |
| 6 | Katalog dan pemesanan | Tinggi |
| 7 | Pembayaran dan konfirmasi | Tinggi |
| 8 | Pengembalian motor | Tinggi |
| 9 | Bagi hasil | Tinggi |
| 10 | Dashboard analytics dan laporan | Menengah |
| 11 | REST API | Menengah |
| 12 | Testing dan deployment | Tinggi |

---

# 12. Catatan Pengembangan & Keputusan yang Perlu Ditetapkan

Beberapa kebutuhan perlu dipastikan sebelum implementasi database dan business logic:

1. **Persentase bagi hasil:** Tentukan persentase pemilik dan admin.
2. **Metode pembayaran:** Tentukan apakah pembayaran dilakukan melalui transfer manual, QRIS, atau payment gateway.
3. **Verifikasi pengguna:** Tentukan apakah penyewa perlu mengunggah identitas.
4. **Pembatalan:** Tentukan aturan pembatalan pesanan dan pengembalian pembayaran.
5. **Keterlambatan:** Tentukan aturan denda jika motor dikembalikan melewati tanggal selesai.
6. **Pengembalian dana:** Tentukan prosedur jika pembayaran sudah dilakukan tetapi pesanan ditolak atau dibatalkan.
7. **Ketersediaan motor:** Tentukan apakah status motor cukup menggunakan Available/Rented atau perlu pengelolaan jadwal berdasarkan periode.

Untuk implementasi, disarankan menambahkan tabel pendukung seperti `payment_proofs`, `motor_verifications`, dan `rental_status_histories` apabila kebutuhan operasionalnya sudah ditetapkan.

---

# 13. Final Product Vision

MOTORRENT dikembangkan sebagai platform penyewaan motor berbasis kemitraan yang menghubungkan pemilik kendaraan dengan penyewa melalui sistem pengelolaan terpusat.

Fokus utama produk adalah kemudahan penggunaan, transparansi transaksi, pengelolaan kendaraan, serta pencatatan pendapatan dan bagi hasil.

**Design Direction:** Modern Minimalist SaaS – Mobility Platform.

**Development Direction:** Laravel 12 + MySQL + Blade + Tailwind CSS + REST API.

**Product Goal:** Membangun platform rental motor yang terstruktur, mudah digunakan, aman, dan dapat dikembangkan untuk kebutuhan bisnis yang lebih besar.

---

*End of Product Requirements Document – MOTORRENT v2.0*