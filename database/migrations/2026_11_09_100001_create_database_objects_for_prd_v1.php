<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Objek database sesuai PRD v1 Bagian 8.3:
     * function, stored procedure, trigger, dan view.
     */
    public function up(): void
    {
        $this->dropObjects();

        // ------------------------------------------------------------------
        // FUNCTION
        // ------------------------------------------------------------------
        DB::unprepared(<<<'SQL'
CREATE FUNCTION fn_hitung_biaya(p_motor_id INT, p_tipe_durasi VARCHAR(20), p_jumlah INT)
RETURNS DECIMAL(12,2)
DETERMINISTIC
READS SQL DATA
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
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE FUNCTION fn_total_pendapatan(p_periode DATE)
RETURNS DECIMAL(14,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(14,2) DEFAULT 0;

    SELECT COALESCE(SUM(jumlah), 0) INTO v_total
    FROM transaksis
    WHERE status = 'berhasil'
      AND YEAR(tanggal) = YEAR(p_periode)
      AND MONTH(tanggal) = MONTH(p_periode);

    RETURN v_total;
END
SQL);

        // ------------------------------------------------------------------
        // STORED PROCEDURE
        // ------------------------------------------------------------------
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_buat_penyewaan(
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
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_konfirmasi_pengembalian(IN p_penyewaan_id INT)
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
END
SQL);

        // ------------------------------------------------------------------
        // TRIGGER
        // ------------------------------------------------------------------
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_motor_disewa
AFTER UPDATE ON transaksis
FOR EACH ROW
BEGIN
    IF NEW.status = 'berhasil' AND OLD.status <> 'berhasil' THEN
        UPDATE motors
        SET status = 'disewa'
        WHERE id = (SELECT motor_id FROM penyewaans WHERE id = NEW.pemesanan_id);
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_bagi_hasil
AFTER UPDATE ON penyewaans
FOR EACH ROW
BEGIN
    IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
        INSERT INTO bagi_hasils
            (pemesanan_id, bagi_hasil_pemilik, bagi_hasil_admin, tanggal, created_at, updated_at)
        SELECT NEW.id, ROUND(NEW.harga * 0.70, 2), ROUND(NEW.harga * 0.30, 2), CURDATE(), NOW(), NOW()
        WHERE NOT EXISTS (SELECT 1 FROM bagi_hasils WHERE pemesanan_id = NEW.id);
    END IF;
END
SQL);

        // ------------------------------------------------------------------
        // VIEW
        // ------------------------------------------------------------------
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_laporan_admin AS
SELECT
    p.id AS pemesanan_id,
    u.nama AS penyewa,
    m.merk,
    m.no_plat,
    p.tanggal_mulai,
    p.tanggal_selesai,
    p.tipe_durasi,
    p.harga,
    p.status AS status_penyewaan,
    t.metode_pembayaran,
    t.status AS status_pembayaran,
    COALESCE(b.bagi_hasil_pemilik, 0) AS bagi_hasil_pemilik,
    COALESCE(b.bagi_hasil_admin, 0) AS bagi_hasil_admin
FROM penyewaans p
JOIN users u ON u.id = p.penyewa_id
JOIN motors m ON m.id = p.motor_id
LEFT JOIN transaksis t ON t.pemesanan_id = p.id
LEFT JOIN bagi_hasils b ON b.pemesanan_id = p.id
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_laporan_pemilik AS
SELECT
    m.pemilik_id,
    m.id AS motor_id,
    m.merk,
    m.no_plat,
    COUNT(p.id) AS total_penyewaan,
    COALESCE(SUM(b.bagi_hasil_pemilik), 0) AS total_bagi_hasil
FROM motors m
LEFT JOIN penyewaans p ON p.motor_id = m.id
LEFT JOIN bagi_hasils b ON b.pemesanan_id = p.id
GROUP BY m.pemilik_id, m.id, m.merk, m.no_plat
SQL);

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE VIEW v_riwayat_penyewa AS
SELECT
    p.penyewa_id,
    p.id AS pemesanan_id,
    m.merk,
    m.no_plat,
    p.tanggal_mulai,
    p.tanggal_selesai,
    p.tipe_durasi,
    p.harga,
    p.status AS status_penyewaan,
    t.metode_pembayaran,
    t.status AS status_pembayaran
FROM penyewaans p
JOIN motors m ON m.id = p.motor_id
LEFT JOIN transaksis t ON t.pemesanan_id = p.id
SQL);
    }

    public function down(): void
    {
        $this->dropObjects();
    }

    private function dropObjects(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_motor_disewa');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_bagi_hasil');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_buat_penyewaan');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_konfirmasi_pengembalian');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_hitung_biaya');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_total_pendapatan');
        DB::unprepared('DROP VIEW IF EXISTS v_laporan_admin');
        DB::unprepared('DROP VIEW IF EXISTS v_laporan_pemilik');
        DB::unprepared('DROP VIEW IF EXISTS v_riwayat_penyewa');
    }
};
