-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: db_ujikom
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `db_ujikom`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `db_ujikom` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `db_ujikom`;

--
-- Table structure for table `bagi_hasils`
--

DROP TABLE IF EXISTS `bagi_hasils`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bagi_hasils` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pemesanan_id` bigint(20) unsigned NOT NULL,
  `bagi_hasil_pemilik` decimal(12,2) NOT NULL,
  `bagi_hasil_admin` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bagi_hasils_pemesanan_id_unique` (`pemesanan_id`),
  CONSTRAINT `bagi_hasils_pemesanan_id_foreign` FOREIGN KEY (`pemesanan_id`) REFERENCES `penyewaans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bagi_hasils`
--

LOCK TABLES `bagi_hasils` WRITE;
/*!40000 ALTER TABLE `bagi_hasils` DISABLE KEYS */;
INSERT INTO `bagi_hasils` VALUES (1,1,178500.00,76500.00,'2026-11-02','2026-11-02 23:59:53','2026-11-08 23:59:53','2026-11-08 23:59:53'),(2,4,119000.00,51000.00,'2026-11-09','2026-11-09 01:05:04','2026-11-09 00:54:51','2026-11-09 01:05:04'),(3,5,119000.00,51000.00,'2026-11-09',NULL,'2026-11-09 00:59:19','2026-11-09 00:59:19'),(4,6,119000.00,51000.00,'2026-11-09',NULL,'2026-11-09 00:59:57','2026-11-09 00:59:57'),(5,7,119000.00,51000.00,'2026-11-09',NULL,'2026-11-09 01:00:27','2026-11-09 01:00:27');
/*!40000 ALTER TABLE `bagi_hasils` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_01_000001_create_motors_table',1),(5,'2026_01_01_000002_create_tarif_rentals_table',1),(6,'2026_01_01_000003_create_penyewaans_table',1),(7,'2026_01_01_000004_create_transaksis_table',1),(8,'2026_01_01_000005_create_bagi_hasils_table',1),(9,'2026_11_09_075905_create_personal_access_tokens_table',2),(10,'2026_11_09_100000_align_values_to_prd_v1',3),(11,'2026_11_09_100001_create_database_objects_for_prd_v1',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `motors`
--

DROP TABLE IF EXISTS `motors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pemilik_id` bigint(20) unsigned NOT NULL,
  `merk` varchar(255) NOT NULL,
  `tipe_cc` enum('100','125','150') NOT NULL,
  `no_plat` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending_verification',
  `photo` varchar(255) DEFAULT NULL,
  `dokumen_kepemilikan` varchar(255) DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `motors_pemilik_id_foreign` (`pemilik_id`),
  KEY `motors_status_tipe_cc_index` (`status`,`tipe_cc`),
  CONSTRAINT `motors_pemilik_id_foreign` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motors`
--

LOCK TABLES `motors` WRITE;
/*!40000 ALTER TABLE `motors` DISABLE KEYS */;
INSERT INTO `motors` VALUES (1,2,'Honda Vario 125 CBS ISS','125','B 1234 SAA','tersedia',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-09 01:00:27'),(2,2,'Yamaha NMAX 155 Connected','150','B 5678 SBB','tersedia',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(3,2,'Honda Beat Deluxe','100','B 9012 SCC','disewa',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(4,3,'Yamaha Aerox 155 VVA','150','D 3456 SDD','tersedia',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(5,3,'Honda Scoopy Prestige','100','D 7890 SEE','menunggu_verifikasi',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(6,3,'Honda PCX 160 ABS','150','D 1122 SFF','tersedia',NULL,NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(7,2,'Yamaha Fazzio 125 Hybrid','125','B 9988 XYZ','tersedia',NULL,'documents/w1ttrgcMLDZsR7tBPQfPP6CPRYhKbeEWrOyo0g4l.pdf','Unit diverifikasi dan siap disewa.','2026-11-09 00:59:57','2026-11-09 00:59:57');
/*!40000 ALTER TABLE `motors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penyewaans`
--

DROP TABLE IF EXISTS `penyewaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penyewaans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penyewa_id` bigint(20) unsigned NOT NULL,
  `motor_id` bigint(20) unsigned NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `tipe_durasi` varchar(20) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending_payment',
  `catatan` text DEFAULT NULL,
  `dikembalikan_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penyewaans_motor_id_status_index` (`motor_id`,`status`),
  KEY `penyewaans_penyewa_id_status_index` (`penyewa_id`,`status`),
  CONSTRAINT `penyewaans_motor_id_foreign` FOREIGN KEY (`motor_id`) REFERENCES `motors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penyewaans_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penyewaans`
--

LOCK TABLES `penyewaans` WRITE;
/*!40000 ALTER TABLE `penyewaans` DISABLE KEYS */;
INSERT INTO `penyewaans` VALUES (1,4,1,'2026-10-30','2026-11-02','harian',255000.00,'selesai','Sewa 3 hari keliling kota','2026-11-02 01:59:53','2026-11-08 23:59:53','2026-11-08 23:59:53'),(2,5,3,'2026-11-07','2026-11-12','harian',350000.00,'berjalan','Sewa keperluan kerja seminggu',NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(3,4,2,'2026-11-11','2026-11-14','harian',420000.00,'menunggu_konfirmasi','Trip akhir pekan ke luar kota',NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(4,4,1,'2026-11-19','2026-11-21','harian',170000.00,'selesai','Test Rental Otomatis','2026-11-09 00:54:51','2026-11-09 00:54:51','2026-11-09 00:54:51'),(5,4,1,'2026-11-19','2026-11-21','harian',170000.00,'selesai','Test Rental Otomatis','2026-11-09 00:59:19','2026-11-09 00:59:19','2026-11-09 00:59:19'),(6,4,1,'2026-11-24','2026-11-26','harian',170000.00,'selesai','Test Rental Otomatis','2026-11-09 00:59:57','2026-11-09 00:59:57','2026-11-09 00:59:57'),(7,4,1,'2026-11-24','2026-11-26','harian',170000.00,'selesai','Test Rental Otomatis','2026-11-09 01:00:27','2026-11-09 01:00:27','2026-11-09 01:00:27');
/*!40000 ALTER TABLE `penyewaans` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_bagi_hasil
AFTER UPDATE ON penyewaans
FOR EACH ROW
BEGIN
    IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
        INSERT INTO bagi_hasils
            (pemesanan_id, bagi_hasil_pemilik, bagi_hasil_admin, tanggal, created_at, updated_at)
        SELECT NEW.id, ROUND(NEW.harga * 0.70, 2), ROUND(NEW.harga * 0.30, 2), CURDATE(), NOW(), NOW()
        WHERE NOT EXISTS (SELECT 1 FROM bagi_hasils WHERE pemesanan_id = NEW.id);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'api_token','3814921936b86978021f817364887d8c5826c8f2ec437b6b06aeb46782734719','[\"*\"]',NULL,NULL,'2026-11-09 00:59:19','2026-11-09 00:59:19'),(2,'App\\Models\\User',1,'api_token','e38a66d7a83c6f37c949698c142d42a87ec50698fe4804cf9a66e09a542f9bea','[\"*\"]',NULL,NULL,'2026-11-09 00:59:57','2026-11-09 00:59:57'),(3,'App\\Models\\User',1,'admin_test','ff206518d2f920c2864223abce6c5ef5ab2c59d177a246cf16466f0f532bbe00','[\"*\"]','2026-11-09 00:59:57',NULL,'2026-11-09 00:59:57','2026-11-09 00:59:57'),(4,'App\\Models\\User',4,'penyewa_test','610736b40aef37b34e64cab4b7a92894f4b665d12ce1d5e9ffe8d9af7b6fea00','[\"*\"]',NULL,NULL,'2026-11-09 00:59:57','2026-11-09 00:59:57'),(5,'App\\Models\\User',1,'api_token','95d8bc5a66647d146c0e146e60934dd9f502868cf61444e8cea5beb3c92fcf1d','[\"*\"]',NULL,NULL,'2026-11-09 01:00:26','2026-11-09 01:00:26');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('A9x3DDmFWf0UZ3yRTP5GyZ51jtFPtrstT6rsasKR',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVnFtREE4TzZxckY5YWxMUlFzb3pId2RnRERwN3pEUXJkTUZzUkhNUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tb3RvcnMiO3M6NToicm91dGUiO3M6MTc6Im1hcmtldHBsYWNlLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDYzODA1MjFjNjhmZWQ2NzAyMjk0YTA1YWVlN2JkMDZkNWMxZjkxM2VkNDdhZTkwYmRlY2UzNTk3ZjU2YmFhNCI7fQ==',1794247131),('huuwC2vWUvskwI3JhL84EIjreiCGvUiq1rJYNeYs',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicmd6TmV5Z2txcEpZaEJjRUNQdHVlSVptNXN5M2RRUFdER1NWR09vWiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=',1794248832);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarif_rentals`
--

DROP TABLE IF EXISTS `tarif_rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarif_rentals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motor_id` bigint(20) unsigned NOT NULL,
  `tarif_harian` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tarif_mingguan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tarif_bulanan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarif_rentals_motor_id_unique` (`motor_id`),
  CONSTRAINT `tarif_rentals_motor_id_foreign` FOREIGN KEY (`motor_id`) REFERENCES `motors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarif_rentals`
--

LOCK TABLES `tarif_rentals` WRITE;
/*!40000 ALTER TABLE `tarif_rentals` DISABLE KEYS */;
INSERT INTO `tarif_rentals` VALUES (1,1,85000.00,500000.00,1700000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(2,2,140000.00,850000.00,2800000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(3,3,70000.00,420000.00,1400000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(4,4,135000.00,800000.00,2700000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(5,5,80000.00,480000.00,1600000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(6,6,150000.00,900000.00,3000000.00,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(7,7,95000.00,570000.00,1900000.00,'2026-11-09 00:59:57','2026-11-09 00:59:57');
/*!40000 ALTER TABLE `tarif_rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pemesanan_id` bigint(20) unsigned NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(30) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `tanggal` datetime NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksis_pemesanan_id_foreign` (`pemesanan_id`),
  KEY `transaksis_status_tanggal_index` (`status`,`tanggal`),
  CONSTRAINT `transaksis_pemesanan_id_foreign` FOREIGN KEY (`pemesanan_id`) REFERENCES `penyewaans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
INSERT INTO `transaksis` VALUES (1,1,255000.00,'transfer','berhasil','2026-10-30 06:59:53',NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(2,2,350000.00,'ewallet','berhasil','2026-11-07 06:59:53',NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(3,3,420000.00,'transfer','pending','2026-11-09 06:59:53',NULL,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(4,4,170000.00,'transfer','berhasil','2026-11-09 07:54:51','payment-proofs/A2cibYw9WTtoENiYM6DFIReuvmSV2J0X0epKAng0.jpg',NULL,'2026-11-09 00:54:51','2026-11-09 00:54:51'),(5,5,170000.00,'transfer','berhasil','2026-11-09 07:59:19','payment-proofs/FT2vQkcMyDJLwlQbCUZV1tnPPt1io7Prhe6rmL4K.jpg',NULL,'2026-11-09 00:59:19','2026-11-09 00:59:19'),(6,6,170000.00,'transfer','berhasil','2026-11-09 07:59:57','payment-proofs/HJuYAEdgjGWqy9YUsHE0GptVcCDMWU0IvouhDLcD.jpg',NULL,'2026-11-09 00:59:57','2026-11-09 00:59:57'),(7,7,170000.00,'transfer','berhasil','2026-11-09 08:00:27','payment-proofs/jTIFCN3FVrIvHhc6gwKdtHriUPN7pbJ5MYqxDWjd.jpg',NULL,'2026-11-09 01:00:27','2026-11-09 01:00:27');
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_motor_disewa
AFTER UPDATE ON transaksis
FOR EACH ROW
BEGIN
    IF NEW.status = 'berhasil' AND OLD.status <> 'berhasil' THEN
        UPDATE motors
        SET status = 'disewa'
        WHERE id = (SELECT motor_id FROM penyewaans WHERE id = NEW.pemesanan_id);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_tlpn` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pemilik','penyewa') NOT NULL DEFAULT 'penyewa',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator RideGo','admin@motorrent.test','081234567890',NULL,'$2y$12$BnsnVs0nfkBB4OlTmtL6WOaJiTG9O.Wo.2adBppy3Kcg3aULTPds6','admin',1,NULL,'2026-11-08 23:59:52','2026-11-09 11:37:04'),(2,'Budi Santoso','budi.pemilik@motorrent.test','081298765432',NULL,'$2y$12$t667vWG1FCB0vePWmEAsw.EnRnD1VZjDtymsxHvFxa1R1G90Dotla','pemilik',1,NULL,'2026-11-08 23:59:52','2026-11-08 23:59:52'),(3,'Siti Nurhaliza','siti.pemilik@motorrent.test','081345678901',NULL,'$2y$12$7eZLH52kFdKiybD4F56YWefrCuEvq4oSIvtdsQrC.QxzSWv/t0hby','pemilik',1,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(4,'Andi Wijaya','andi.penyewa@motorrent.test','081567890123',NULL,'$2y$12$My8SCnIOGEXsqqzavHLsRuHbQ6EwBcgC4RimQquK2pn2.Cg2TUlMW','penyewa',1,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53'),(5,'Dewi Lestari','dewi.penyewa@motorrent.test','081678901234',NULL,'$2y$12$nF9Hmw9QCAJD5uhJiG0uXOiUCHvpKxQqXuuEH0wsg7K9iCUeu4AVG','penyewa',1,NULL,'2026-11-08 23:59:53','2026-11-08 23:59:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_laporan_admin`
--

DROP TABLE IF EXISTS `v_laporan_admin`;
/*!50001 DROP VIEW IF EXISTS `v_laporan_admin`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_laporan_admin` AS SELECT
 1 AS `pemesanan_id`,
  1 AS `penyewa`,
  1 AS `merk`,
  1 AS `no_plat`,
  1 AS `tanggal_mulai`,
  1 AS `tanggal_selesai`,
  1 AS `tipe_durasi`,
  1 AS `harga`,
  1 AS `status_penyewaan`,
  1 AS `metode_pembayaran`,
  1 AS `status_pembayaran`,
  1 AS `bagi_hasil_pemilik`,
  1 AS `bagi_hasil_admin` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_laporan_pemilik`
--

DROP TABLE IF EXISTS `v_laporan_pemilik`;
/*!50001 DROP VIEW IF EXISTS `v_laporan_pemilik`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_laporan_pemilik` AS SELECT
 1 AS `pemilik_id`,
  1 AS `motor_id`,
  1 AS `merk`,
  1 AS `no_plat`,
  1 AS `total_penyewaan`,
  1 AS `total_bagi_hasil` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_riwayat_penyewa`
--

DROP TABLE IF EXISTS `v_riwayat_penyewa`;
/*!50001 DROP VIEW IF EXISTS `v_riwayat_penyewa`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_riwayat_penyewa` AS SELECT
 1 AS `penyewa_id`,
  1 AS `pemesanan_id`,
  1 AS `merk`,
  1 AS `no_plat`,
  1 AS `tanggal_mulai`,
  1 AS `tanggal_selesai`,
  1 AS `tipe_durasi`,
  1 AS `harga`,
  1 AS `status_penyewaan`,
  1 AS `metode_pembayaran`,
  1 AS `status_pembayaran` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'db_ujikom'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_hitung_biaya` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_biaya`(p_motor_id INT, p_tipe_durasi VARCHAR(20), p_jumlah INT) RETURNS decimal(12,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_tarif DECIMAL(12,2) DEFAULT 0;

    IF p_tipe_durasi = 'mingguan' THEN
        SELECT tarif_mingguan INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    ELSEIF p_tipe_durasi = 'bulanan' THEN
        SELECT tarif_bulanan INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    ELSE
        SELECT tarif_harian INTO v_tarif FROM tarif_rentals WHERE motor_id = p_motor_id;
    END IF;

    RETURN ROUND(COALESCE(v_tarif, 0) * GREATEST(p_jumlah, 1), 2);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_total_pendapatan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_pendapatan`(p_periode DATE) RETURNS decimal(14,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(14,2) DEFAULT 0;

    SELECT COALESCE(SUM(jumlah), 0) INTO v_total
    FROM transaksis
    WHERE status = 'berhasil'
      AND YEAR(tanggal) = YEAR(p_periode)
      AND MONTH(tanggal) = MONTH(p_periode);

    RETURN v_total;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_buat_penyewaan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_penyewaan`(
    IN p_penyewa_id INT,
    IN p_motor_id INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_selesai DATE,
    IN p_tipe_durasi VARCHAR(20),
    IN p_catatan TEXT,
    OUT p_penyewaan_id INT
)
BEGIN
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_konfirmasi_pengembalian` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_konfirmasi_pengembalian`(IN p_penyewaan_id INT)
BEGIN
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Current Database: `db_ujikom`
--

USE `db_ujikom`;

--
-- Final view structure for view `v_laporan_admin`
--

/*!50001 DROP VIEW IF EXISTS `v_laporan_admin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_laporan_admin` AS select `p`.`id` AS `pemesanan_id`,`u`.`nama` AS `penyewa`,`m`.`merk` AS `merk`,`m`.`no_plat` AS `no_plat`,`p`.`tanggal_mulai` AS `tanggal_mulai`,`p`.`tanggal_selesai` AS `tanggal_selesai`,`p`.`tipe_durasi` AS `tipe_durasi`,`p`.`harga` AS `harga`,`p`.`status` AS `status_penyewaan`,`t`.`metode_pembayaran` AS `metode_pembayaran`,`t`.`status` AS `status_pembayaran`,coalesce(`b`.`bagi_hasil_pemilik`,0) AS `bagi_hasil_pemilik`,coalesce(`b`.`bagi_hasil_admin`,0) AS `bagi_hasil_admin` from ((((`penyewaans` `p` join `users` `u` on(`u`.`id` = `p`.`penyewa_id`)) join `motors` `m` on(`m`.`id` = `p`.`motor_id`)) left join `transaksis` `t` on(`t`.`pemesanan_id` = `p`.`id`)) left join `bagi_hasils` `b` on(`b`.`pemesanan_id` = `p`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_laporan_pemilik`
--

/*!50001 DROP VIEW IF EXISTS `v_laporan_pemilik`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_laporan_pemilik` AS select `m`.`pemilik_id` AS `pemilik_id`,`m`.`id` AS `motor_id`,`m`.`merk` AS `merk`,`m`.`no_plat` AS `no_plat`,count(`p`.`id`) AS `total_penyewaan`,coalesce(sum(`b`.`bagi_hasil_pemilik`),0) AS `total_bagi_hasil` from ((`motors` `m` left join `penyewaans` `p` on(`p`.`motor_id` = `m`.`id`)) left join `bagi_hasils` `b` on(`b`.`pemesanan_id` = `p`.`id`)) group by `m`.`pemilik_id`,`m`.`id`,`m`.`merk`,`m`.`no_plat` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_riwayat_penyewa`
--

/*!50001 DROP VIEW IF EXISTS `v_riwayat_penyewa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_riwayat_penyewa` AS select `p`.`penyewa_id` AS `penyewa_id`,`p`.`id` AS `pemesanan_id`,`m`.`merk` AS `merk`,`m`.`no_plat` AS `no_plat`,`p`.`tanggal_mulai` AS `tanggal_mulai`,`p`.`tanggal_selesai` AS `tanggal_selesai`,`p`.`tipe_durasi` AS `tipe_durasi`,`p`.`harga` AS `harga`,`p`.`status` AS `status_penyewaan`,`t`.`metode_pembayaran` AS `metode_pembayaran`,`t`.`status` AS `status_pembayaran` from ((`penyewaans` `p` join `motors` `m` on(`m`.`id` = `p`.`motor_id`)) left join `transaksis` `t` on(`t`.`pemesanan_id` = `p`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-10  1:37:29
