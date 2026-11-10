-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 10, 2026 at 08:29 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ujikom`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_penyewaan` (IN `p_penyewa_id` INT, IN `p_motor_id` INT, IN `p_tanggal_mulai` DATE, IN `p_tanggal_selesai` DATE, IN `p_tipe_durasi` VARCHAR(20), IN `p_catatan` TEXT, OUT `p_penyewaan_id` INT)   BEGIN
    DECLARE v_hari INT DEFAULT 1;
    DECLARE v_unit INT DEFAULT 1;
    DECLARE v_harga DECIMAL(12,2) DEFAULT 0;
    DECLARE v_bentrok INT DEFAULT 0;

    SET v_hari = GREATEST(1, DATEDIFF(p_tanggal_selesai, p_tanggal_mulai));
    SET v_unit = CASE p_tipe_durasi
        WHEN 'mingguan' THEN CEIL(v_hari / 7)
        WHEN 'bulanan' THEN CEIL(v_hari / 30)
        ELSE v_hari
    END;

    SELECT COUNT(*) INTO v_bentrok
    FROM penyewaans
    WHERE motor_id = p_motor_id
      AND status IN ('menunggu_pembayaran', 'menunggu_konfirmasi', 'berjalan')
      AND tanggal_mulai <= p_tanggal_selesai
      AND tanggal_selesai >= p_tanggal_mulai;

    IF v_bentrok > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jadwal motor sudah terisi pada periode tersebut.';
    END IF;

    SET v_harga = fn_hitung_biaya(p_motor_id, p_tipe_durasi, v_unit);

    INSERT INTO penyewaans
        (penyewa_id, motor_id, tanggal_mulai, tanggal_selesai, tipe_durasi, harga, status, catatan, created_at, updated_at)
    VALUES
        (p_penyewa_id, p_motor_id, p_tanggal_mulai, p_tanggal_selesai, p_tipe_durasi, v_harga, 'menunggu_pembayaran', p_catatan, NOW(), NOW());

    SET p_penyewaan_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_konfirmasi_pengembalian` (IN `p_penyewaan_id` INT)   BEGIN
    DECLARE v_motor_id INT;
    DECLARE v_harga DECIMAL(12,2);

    SELECT motor_id, harga INTO v_motor_id, v_harga
    FROM penyewaans
    WHERE id = p_penyewaan_id;

    UPDATE penyewaans
    SET status = 'selesai', dikembalikan_pada = NOW(), updated_at = NOW()
    WHERE id = p_penyewaan_id;

    UPDATE motors SET status = 'tersedia' WHERE id = v_motor_id;

    INSERT INTO bagi_hasils
        (pemesanan_id, bagi_hasil_pemilik, bagi_hasil_admin, tanggal, created_at, updated_at)
    SELECT p_penyewaan_id, ROUND(v_harga * 0.70, 2), ROUND(v_harga * 0.30, 2), CURDATE(), NOW(), NOW()
    WHERE NOT EXISTS (SELECT 1 FROM bagi_hasils WHERE pemesanan_id = p_penyewaan_id);
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_biaya` (`p_motor_id` INT, `p_tipe_durasi` VARCHAR(20), `p_jumlah` INT) RETURNS DECIMAL(12,2) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_tarif DECIMAL(12,2) DEFAULT 0;

    IF p_tipe_durasi = 'mingguan' THEN
        SELECT tarif_mingguan INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    ELSEIF p_tipe_durasi = 'bulanan' THEN
        SELECT tarif_bulanan INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    ELSE
        SELECT tarif_harian INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    END IF;

    RETURN ROUND(COALESCE(v_tarif, 0) * GREATEST(p_jumlah, 1), 2);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_pendapatan` (`p_periode` DATE) RETURNS DECIMAL(14,2) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_total DECIMAL(14,2) DEFAULT 0;

    SELECT COALESCE(SUM(jumlah), 0) INTO v_total
    FROM transaksis
    WHERE status = 'berhasil'
      AND YEAR(tanggal) = YEAR(p_periode)
      AND MONTH(tanggal) = MONTH(p_periode);

    RETURN v_total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bagi_hasils`
--

CREATE TABLE `bagi_hasils` (
  `id` bigint UNSIGNED NOT NULL,
  `pemesanan_id` bigint UNSIGNED NOT NULL,
  `bagi_hasil_pemilik` decimal(12,2) NOT NULL,
  `bagi_hasil_admin` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bagi_hasils`
--

INSERT INTO `bagi_hasils` (`id`, `pemesanan_id`, `bagi_hasil_pemilik`, `bagi_hasil_admin`, `tanggal`, `settled_at`, `created_at`, `updated_at`) VALUES
(1, 1, '178500.00', '76500.00', '2026-11-02', '2026-11-02 23:59:53', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(2, 4, '119000.00', '51000.00', '2026-11-09', '2026-11-09 01:05:04', '2026-11-09 00:54:51', '2026-11-09 01:05:04'),
(3, 5, '119000.00', '51000.00', '2026-11-09', '2026-11-09 21:13:47', '2026-11-09 00:59:19', '2026-11-09 21:13:47'),
(4, 6, '119000.00', '51000.00', '2026-11-09', '2026-11-09 21:13:49', '2026-11-09 00:59:57', '2026-11-09 21:13:49'),
(5, 7, '119000.00', '51000.00', '2026-11-09', '2026-11-09 21:09:45', '2026-11-09 01:00:27', '2026-11-09 21:09:45'),
(10, 12, '178500.00', '76500.00', '2026-11-03', '2026-11-03 18:32:55', '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(11, 18, '94500.00', '40500.00', '2026-11-10', '2026-11-09 18:49:15', '2026-11-10 01:48:10', '2026-11-09 18:49:15');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_motors_table', 1),
(5, '2026_01_01_000002_create_tarif_rentals_table', 1),
(6, '2026_01_01_000003_create_penyewaans_table', 1),
(7, '2026_01_01_000004_create_transaksis_table', 1),
(8, '2026_01_01_000005_create_bagi_hasils_table', 1),
(9, '2026_11_09_075905_create_personal_access_tokens_table', 2),
(10, '2026_11_09_100000_align_values_to_prd_v1', 3),
(11, '2026_11_09_100001_create_database_objects_for_prd_v1', 3);

-- --------------------------------------------------------

--
-- Table structure for table `motors`
--

CREATE TABLE `motors` (
  `id` bigint UNSIGNED NOT NULL,
  `pemilik_id` bigint UNSIGNED NOT NULL,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_cc` enum('100','125','150') COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_plat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_verification',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dokumen_kepemilikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `motors`
--

INSERT INTO `motors` (`id`, `pemilik_id`, `merk`, `tipe_cc`, `no_plat`, `status`, `photo`, `dokumen_kepemilikan`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 2, 'Honda Vario 125 CBS ISS', '125', 'B 1234 SAA', 'tersedia', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-09 01:00:27'),
(2, 2, 'Yamaha NMAX 155 Connected', '150', 'B 5678 SBB', 'disewa', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-09 22:53:18'),
(3, 2, 'Honda Beat Deluxe', '100', 'B 9012 SCC', 'disewa', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(4, 3, 'Yamaha Aerox 155 VVA', '150', 'D 3456 SDD', 'tersedia', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-09 18:48:10'),
(5, 3, 'Honda Scoopy Prestige', '100', 'D 7890 SEE', 'menunggu_verifikasi', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(6, 3, 'Honda PCX 160 ABS', '150', 'D 1122 SFF', 'tersedia', NULL, NULL, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(7, 2, 'Yamaha Fazzio 125 Hybrid', '125', 'B 9988 XYZ', 'perawatan', NULL, 'documents/w1ttrgcMLDZsR7tBPQfPP6CPRYhKbeEWrOyo0g4l.pdf', 'Unit diverifikasi dan siap disewa.', '2026-11-09 00:59:57', '2026-11-09 22:33:28'),
(12, 2, 'Honda sonic 125', '125', 'Z 1234 YJ', 'tersedia', NULL, 'documents/RPgqsq2RjTHjpRaUzNN6NJcRC0PE4uznzwpT51PI.png', 'Disetujui oleh admin.', '2026-11-09 21:02:45', '2026-11-09 21:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penyewaans`
--

CREATE TABLE `penyewaans` (
  `id` bigint UNSIGNED NOT NULL,
  `penyewa_id` bigint UNSIGNED NOT NULL,
  `motor_id` bigint UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `tipe_durasi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dikembalikan_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penyewaans`
--

INSERT INTO `penyewaans` (`id`, `penyewa_id`, `motor_id`, `tanggal_mulai`, `tanggal_selesai`, `tipe_durasi`, `harga`, `status`, `catatan`, `dikembalikan_pada`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2026-10-30', '2026-11-02', 'harian', '255000.00', 'selesai', 'Sewa 3 hari keliling kota', '2026-11-02 01:59:53', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(2, 5, 3, '2026-11-07', '2026-11-12', 'harian', '350000.00', 'berjalan', 'Sewa keperluan kerja seminggu', NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(3, 4, 2, '2026-11-11', '2026-11-14', 'harian', '420000.00', 'berjalan', 'Trip akhir pekan ke luar kota', NULL, '2026-11-08 23:59:53', '2026-11-09 22:53:18'),
(4, 4, 1, '2026-11-19', '2026-11-21', 'harian', '170000.00', 'selesai', 'Test Rental Otomatis', '2026-11-09 00:54:51', '2026-11-09 00:54:51', '2026-11-09 00:54:51'),
(5, 4, 1, '2026-11-19', '2026-11-21', 'harian', '170000.00', 'selesai', 'Test Rental Otomatis', '2026-11-09 00:59:19', '2026-11-09 00:59:19', '2026-11-09 00:59:19'),
(6, 4, 1, '2026-11-24', '2026-11-26', 'harian', '170000.00', 'selesai', 'Test Rental Otomatis', '2026-11-09 00:59:57', '2026-11-09 00:59:57', '2026-11-09 00:59:57'),
(7, 4, 1, '2026-11-24', '2026-11-26', 'harian', '170000.00', 'selesai', 'Test Rental Otomatis', '2026-11-09 01:00:27', '2026-11-09 01:00:27', '2026-11-09 01:00:27'),
(12, 4, 1, '2026-10-31', '2026-11-03', 'harian', '255000.00', 'selesai', 'Sewa 3 hari keliling kota', '2026-11-02 20:32:55', '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(13, 5, 3, '2026-11-08', '2026-11-13', 'harian', '350000.00', 'berjalan', 'Sewa keperluan kerja seminggu', NULL, '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(14, 4, 2, '2026-11-12', '2026-11-15', 'harian', '420000.00', 'berjalan', 'Trip akhir pekan ke luar kota', NULL, '2026-11-09 18:32:55', '2026-11-09 21:08:47'),
(15, 4, 1, '2026-11-20', '2026-11-23', 'harian', '280000.00', 'menunggu_konfirmasi', 'Sewa harian ke bandung', NULL, '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(16, 5, 5, '2026-11-25', '2026-11-28', 'harian', '320000.00', 'berjalan', 'Sewa mingguan ke jogja', NULL, '2026-11-09 18:33:55', '2026-11-09 18:33:55'),
(17, 4, 2, '2026-11-30', '2026-12-03', 'harian', '450000.00', 'menunggu_pembayaran', 'Pembayaran ditolak - bukti tidak valid', NULL, '2026-11-09 18:33:55', '2026-11-09 18:33:55'),
(18, 4, 4, '2026-11-10', '2026-11-11', 'harian', '135000.00', 'selesai', NULL, '2026-11-09 18:48:10', '2026-11-09 18:34:33', '2026-11-09 18:48:10'),
(19, 4, 1, '2026-11-10', '2026-11-11', 'harian', '85000.00', 'menunggu_pembayaran', NULL, NULL, '2026-11-09 21:01:03', '2026-11-09 21:01:03');

--
-- Triggers `penyewaans`
--
DELIMITER $$
CREATE TRIGGER `trg_bagi_hasil` AFTER UPDATE ON `penyewaans` FOR EACH ROW BEGIN
    IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
        INSERT INTO bagi_hasils
            (pemesanan_id, bagi_hasil_pemilik, bagi_hasil_admin, tanggal, created_at, updated_at)
        SELECT NEW.id, ROUND(NEW.harga * 0.70, 2), ROUND(NEW.harga * 0.30, 2), CURDATE(), NOW(), NOW()
        WHERE NOT EXISTS (SELECT 1 FROM bagi_hasils WHERE pemesanan_id = NEW.id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api_token', '3814921936b86978021f817364887d8c5826c8f2ec437b6b06aeb46782734719', '[\"*\"]', NULL, NULL, '2026-11-09 00:59:19', '2026-11-09 00:59:19'),
(2, 'App\\Models\\User', 1, 'api_token', 'e38a66d7a83c6f37c949698c142d42a87ec50698fe4804cf9a66e09a542f9bea', '[\"*\"]', NULL, NULL, '2026-11-09 00:59:57', '2026-11-09 00:59:57'),
(3, 'App\\Models\\User', 1, 'admin_test', 'ff206518d2f920c2864223abce6c5ef5ab2c59d177a246cf16466f0f532bbe00', '[\"*\"]', '2026-11-09 00:59:57', NULL, '2026-11-09 00:59:57', '2026-11-09 00:59:57'),
(4, 'App\\Models\\User', 4, 'penyewa_test', '610736b40aef37b34e64cab4b7a92894f4b665d12ce1d5e9ffe8d9af7b6fea00', '[\"*\"]', NULL, NULL, '2026-11-09 00:59:57', '2026-11-09 00:59:57'),
(5, 'App\\Models\\User', 1, 'api_token', '95d8bc5a66647d146c0e146e60934dd9f502868cf61444e8cea5beb3c92fcf1d', '[\"*\"]', NULL, NULL, '2026-11-09 01:00:26', '2026-11-09 01:00:26');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qfSKDweYKeJZOabkIbf7Rd7kuO59uYIRLbZT2Dyc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2prZnlabXFWRUxHbExrWEljWjBDQVZWQkxXUTdBNHB3QURRZFRvTyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9fQ==', 1794299300);

-- --------------------------------------------------------

--
-- Table structure for table `tarif_rentals`
--

CREATE TABLE `tarif_rentals` (
  `id` bigint UNSIGNED NOT NULL,
  `motor_id` bigint UNSIGNED NOT NULL,
  `tarif_harian` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tarif_mingguan` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tarif_bulanan` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tarif_rentals`
--

INSERT INTO `tarif_rentals` (`id`, `motor_id`, `tarif_harian`, `tarif_mingguan`, `tarif_bulanan`, `created_at`, `updated_at`) VALUES
(1, 1, '85000.00', '500000.00', '1700000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(2, 2, '140000.00', '850000.00', '2800000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(3, 3, '70000.00', '420000.00', '1400000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(4, 4, '135000.00', '800000.00', '2700000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(5, 5, '80000.00', '480000.00', '1600000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(6, 6, '150000.00', '900000.00', '3000000.00', '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(12, 12, '80000.00', '480000.00', '1600000.00', '2026-11-09 21:07:13', '2026-11-09 21:07:13'),
(13, 7, '80000.00', '480000.00', '1600000.00', '2026-11-09 22:33:46', '2026-11-09 22:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `transaksis`
--

CREATE TABLE `transaksis` (
  `id` bigint UNSIGNED NOT NULL,
  `pemesanan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `tanggal` datetime NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksis`
--

INSERT INTO `transaksis` (`id`, `pemesanan_id`, `jumlah`, `metode_pembayaran`, `status`, `tanggal`, `bukti_pembayaran`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 1, '255000.00', 'transfer', 'berhasil', '2026-10-30 06:59:53', NULL, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(2, 2, '350000.00', 'ewallet', 'berhasil', '2026-11-07 06:59:53', NULL, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(3, 3, '420000.00', 'transfer', 'berhasil', '2026-11-09 06:59:53', NULL, NULL, '2026-11-08 23:59:53', '2026-11-09 22:53:18'),
(4, 4, '170000.00', 'transfer', 'berhasil', '2026-11-09 07:54:51', 'payment-proofs/A2cibYw9WTtoENiYM6DFIReuvmSV2J0X0epKAng0.jpg', NULL, '2026-11-09 00:54:51', '2026-11-09 00:54:51'),
(5, 5, '170000.00', 'transfer', 'berhasil', '2026-11-09 07:59:19', 'payment-proofs/FT2vQkcMyDJLwlQbCUZV1tnPPt1io7Prhe6rmL4K.jpg', NULL, '2026-11-09 00:59:19', '2026-11-09 00:59:19'),
(6, 6, '170000.00', 'transfer', 'berhasil', '2026-11-09 07:59:57', 'payment-proofs/HJuYAEdgjGWqy9YUsHE0GptVcCDMWU0IvouhDLcD.jpg', NULL, '2026-11-09 00:59:57', '2026-11-09 00:59:57'),
(7, 7, '170000.00', 'transfer', 'berhasil', '2026-11-09 08:00:27', 'payment-proofs/jTIFCN3FVrIvHhc6gwKdtHriUPN7pbJ5MYqxDWjd.jpg', NULL, '2026-11-09 01:00:27', '2026-11-09 01:00:27'),
(12, 12, '255000.00', 'transfer', 'berhasil', '2026-10-31 01:32:55', NULL, NULL, '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(13, 13, '350000.00', 'ewallet', 'berhasil', '2026-11-08 01:32:55', NULL, NULL, '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(14, 14, '420000.00', 'transfer', 'berhasil', '2026-11-10 01:32:55', NULL, NULL, '2026-11-09 18:32:55', '2026-11-09 21:08:47'),
(15, 15, '280000.00', 'transfer', 'pending', '2026-11-19 01:32:55', 'payment-proofs/honda-vario-125-01.jpg', NULL, '2026-11-09 18:32:55', '2026-11-09 18:32:55'),
(16, 16, '320000.00', 'ewallet', 'berhasil', '2026-11-24 01:33:55', 'payment-proofs/yamaha-aerox-155-02.jpg', NULL, '2026-11-09 18:33:55', '2026-11-09 18:33:55'),
(17, 17, '450000.00', 'transfer', 'gagal', '2026-11-29 01:33:55', 'payment-proofs/failed-proof.jpg', 'Bukti pembayaran tidak valid', '2026-11-09 18:33:55', '2026-11-09 18:33:55'),
(18, 18, '135000.00', 'transfer', 'berhasil', '2026-11-10 01:34:49', 'payment-proofs/cV7x8OQOZE5L5CjztfG7JY7PkO5gRwfDMXWFApCj.png', NULL, '2026-11-09 18:34:49', '2026-11-09 18:41:23');

--
-- Triggers `transaksis`
--
DELIMITER $$
CREATE TRIGGER `trg_motor_disewa` AFTER UPDATE ON `transaksis` FOR EACH ROW BEGIN
    IF NEW.status = 'berhasil' AND OLD.status <> 'berhasil' THEN
        UPDATE motors
        SET status = 'disewa'
        WHERE id = (SELECT motor_id FROM penyewaans WHERE id = NEW.pemesanan_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_tlpn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','pemilik','penyewa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'penyewa',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_tlpn`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin RideGo', 'admin@motorrent.test', '081234567890', NULL, '$2y$12$BnsnVs0nfkBB4OlTmtL6WOaJiTG9O.Wo.2adBppy3Kcg3aULTPds6', 'admin', 1, NULL, '2026-11-08 23:59:52', '2026-11-10 00:03:20'),
(2, 'Budi Santoso', 'budi.pemilik@motorrent.test', '081298765432', NULL, '$2y$12$t667vWG1FCB0vePWmEAsw.EnRnD1VZjDtymsxHvFxa1R1G90Dotla', 'pemilik', 1, NULL, '2026-11-08 23:59:52', '2026-11-08 23:59:52'),
(3, 'Siti Nurhaliza', 'siti.pemilik@motorrent.test', '081345678901', NULL, '$2y$12$7eZLH52kFdKiybD4F56YWefrCuEvq4oSIvtdsQrC.QxzSWv/t0hby', 'pemilik', 1, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(4, 'Andi Wijaya', 'andi.penyewa@motorrent.test', '081567890123', NULL, '$2y$12$My8SCnIOGEXsqqzavHLsRuHbQ6EwBcgC4RimQquK2pn2.Cg2TUlMW', 'penyewa', 1, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53'),
(5, 'Dewi Lestari', 'dewi.penyewa@motorrent.test', '081678901234', NULL, '$2y$12$nF9Hmw9QCAJD5uhJiG0uXOiUCHvpKxQqXuuEH0wsg7K9iCUeu4AVG', 'penyewa', 1, NULL, '2026-11-08 23:59:53', '2026-11-08 23:59:53');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_laporan_admin`
-- (See below for the actual view)
--
CREATE TABLE `v_laporan_admin` (
`bagi_hasil_admin` decimal(12,2)
,`bagi_hasil_pemilik` decimal(12,2)
,`harga` decimal(12,2)
,`merk` varchar(255)
,`metode_pembayaran` varchar(30)
,`no_plat` varchar(20)
,`pemesanan_id` bigint unsigned
,`penyewa` varchar(255)
,`status_pembayaran` varchar(20)
,`status_penyewaan` varchar(30)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`tipe_durasi` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_laporan_pemilik`
-- (See below for the actual view)
--
CREATE TABLE `v_laporan_pemilik` (
`merk` varchar(255)
,`motor_id` bigint unsigned
,`no_plat` varchar(20)
,`pemilik_id` bigint unsigned
,`total_bagi_hasil` decimal(34,2)
,`total_penyewaan` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_riwayat_penyewa`
-- (See below for the actual view)
--
CREATE TABLE `v_riwayat_penyewa` (
`harga` decimal(12,2)
,`merk` varchar(255)
,`metode_pembayaran` varchar(30)
,`no_plat` varchar(20)
,`pemesanan_id` bigint unsigned
,`penyewa_id` bigint unsigned
,`status_pembayaran` varchar(20)
,`status_penyewaan` varchar(30)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`tipe_durasi` varchar(20)
);

-- --------------------------------------------------------

--
-- Structure for view `v_laporan_admin`
--
DROP TABLE IF EXISTS `v_laporan_admin`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_admin`  AS SELECT `p`.`id` AS `pemesanan_id`, `u`.`nama` AS `penyewa`, `m`.`merk` AS `merk`, `m`.`no_plat` AS `no_plat`, `p`.`tanggal_mulai` AS `tanggal_mulai`, `p`.`tanggal_selesai` AS `tanggal_selesai`, `p`.`tipe_durasi` AS `tipe_durasi`, `p`.`harga` AS `harga`, `p`.`status` AS `status_penyewaan`, `t`.`metode_pembayaran` AS `metode_pembayaran`, `t`.`status` AS `status_pembayaran`, coalesce(`b`.`bagi_hasil_pemilik`,0) AS `bagi_hasil_pemilik`, coalesce(`b`.`bagi_hasil_admin`,0) AS `bagi_hasil_admin` FROM ((((`penyewaans` `p` join `users` `u` on((`u`.`id` = `p`.`penyewa_id`))) join `motors` `m` on((`m`.`id` = `p`.`motor_id`))) left join `transaksis` `t` on((`t`.`pemesanan_id` = `p`.`id`))) left join `bagi_hasils` `b` on((`b`.`pemesanan_id` = `p`.`id`)))  ;

-- --------------------------------------------------------

--
-- Structure for view `v_laporan_pemilik`
--
DROP TABLE IF EXISTS `v_laporan_pemilik`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_pemilik`  AS SELECT `m`.`pemilik_id` AS `pemilik_id`, `m`.`id` AS `motor_id`, `m`.`merk` AS `merk`, `m`.`no_plat` AS `no_plat`, count(`p`.`id`) AS `total_penyewaan`, coalesce(sum(`b`.`bagi_hasil_pemilik`),0) AS `total_bagi_hasil` FROM ((`motors` `m` left join `penyewaans` `p` on((`p`.`motor_id` = `m`.`id`))) left join `bagi_hasils` `b` on((`b`.`pemesanan_id` = `p`.`id`))) GROUP BY `m`.`pemilik_id`, `m`.`id`, `m`.`merk`, `m`.`no_plat``no_plat`  ;

-- --------------------------------------------------------

--
-- Structure for view `v_riwayat_penyewa`
--
DROP TABLE IF EXISTS `v_riwayat_penyewa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_riwayat_penyewa`  AS SELECT `p`.`penyewa_id` AS `penyewa_id`, `p`.`id` AS `pemesanan_id`, `m`.`merk` AS `merk`, `m`.`no_plat` AS `no_plat`, `p`.`tanggal_mulai` AS `tanggal_mulai`, `p`.`tanggal_selesai` AS `tanggal_selesai`, `p`.`tipe_durasi` AS `tipe_durasi`, `p`.`harga` AS `harga`, `p`.`status` AS `status_penyewaan`, `t`.`metode_pembayaran` AS `metode_pembayaran`, `t`.`status` AS `status_pembayaran` FROM ((`penyewaans` `p` join `motors` `m` on((`m`.`id` = `p`.`motor_id`))) left join `transaksis` `t` on((`t`.`pemesanan_id` = `p`.`id`)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bagi_hasils`
--
ALTER TABLE `bagi_hasils`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bagi_hasils_pemesanan_id_unique` (`pemesanan_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `motors`
--
ALTER TABLE `motors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `motors_pemilik_id_foreign` (`pemilik_id`),
  ADD KEY `motors_status_tipe_cc_index` (`status`,`tipe_cc`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `penyewaans`
--
ALTER TABLE `penyewaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penyewaans_motor_id_status_index` (`motor_id`,`status`),
  ADD KEY `penyewaans_penyewa_id_status_index` (`penyewa_id`,`status`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tarif_rentals`
--
ALTER TABLE `tarif_rentals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tarif_rentals_motor_id_unique` (`motor_id`);

--
-- Indexes for table `transaksis`
--
ALTER TABLE `transaksis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksis_pemesanan_id_foreign` (`pemesanan_id`),
  ADD KEY `transaksis_status_tanggal_index` (`status`,`tanggal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bagi_hasils`
--
ALTER TABLE `bagi_hasils`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `motors`
--
ALTER TABLE `motors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `penyewaans`
--
ALTER TABLE `penyewaans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tarif_rentals`
--
ALTER TABLE `tarif_rentals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaksis`
--
ALTER TABLE `transaksis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bagi_hasils`
--
ALTER TABLE `bagi_hasils`
  ADD CONSTRAINT `bagi_hasils_pemesanan_id_foreign` FOREIGN KEY (`pemesanan_id`) REFERENCES `penyewaans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `motors`
--
ALTER TABLE `motors`
  ADD CONSTRAINT `motors_pemilik_id_foreign` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penyewaans`
--
ALTER TABLE `penyewaans`
  ADD CONSTRAINT `penyewaans_motor_id_foreign` FOREIGN KEY (`motor_id`) REFERENCES `motors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penyewaans_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tarif_rentals`
--
ALTER TABLE `tarif_rentals`
  ADD CONSTRAINT `tarif_rentals_motor_id_foreign` FOREIGN KEY (`motor_id`) REFERENCES `motors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksis`
--
ALTER TABLE `transaksis`
  ADD CONSTRAINT `transaksis_pemesanan_id_foreign` FOREIGN KEY (`pemesanan_id`) REFERENCES `penyewaans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
