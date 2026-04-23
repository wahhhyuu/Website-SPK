-- ============================================================
--  SPK PEMILIHAN DOSEN TERBAIK — Database Schema
--  Engine: MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS spk_dosen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spk_dosen;

-- ─── Tabel users (login) ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(32)  NOT NULL COMMENT 'MD5 hash',
    nama_lengkap VARCHAR(100) NOT NULL DEFAULT '',
    role       ENUM('admin','user') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username=admin, password=admin123 (MD5)
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin');

-- ─── Tabel dosen (CRUD) ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS dosen (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    kode       VARCHAR(10)  NOT NULL UNIQUE,
    nama       VARCHAR(100) NOT NULL,
    c1         DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Kedisiplinan (kehadiran 1-250)',
    c2         DECIMAL(7,2) NOT NULL DEFAULT 0 COMMENT 'Penelitian (sitasi 100-700)',
    c3         DECIMAL(4,2) NOT NULL DEFAULT 0 COMMENT 'Penilaian Mahasiswa (1.00-4.00)',
    c4         DECIMAL(4,1) NOT NULL DEFAULT 0 COMMENT 'Masa Kerja (tahun)',
    c5         DECIMAL(5,2) NOT NULL DEFAULT 100 COMMENT 'Prestasi (0-100)',
    c6         DECIMAL(5,2) NOT NULL DEFAULT 100 COMMENT 'Sertifikasi (0-100)',
    c7         DECIMAL(5,2) NOT NULL DEFAULT 100 COMMENT 'Pengabdian (0-100)',
    aktif      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data default 10 dosen (Tabel 4.1 dari laporan)
INSERT INTO dosen (kode, nama, c1, c2, c3, c4, c5, c6, c7) VALUES
('A1',  'Dosen A1',  207, 276, 3.42, 11, 100, 100, 100),
('A2',  'Dosen A2',  187, 239, 3.31, 11, 100, 100, 100),
('A3',  'Dosen A3',  180, 260, 3.31, 11, 100, 100, 100),
('A4',  'Dosen A4',  176, 231, 3.32, 11, 100, 100, 100),
('A5',  'Dosen A5',  188, 334, 3.26, 11, 100, 100, 100),
('A6',  'Dosen A6',  169, 399, 3.47, 10, 100, 100, 100),
('A7',  'Dosen A7',  208, 359, 3.35, 10, 100, 100, 100),
('A8',  'Dosen A8',  194, 363, 3.31, 10, 100, 100, 100),
('A9',  'Dosen A9',  176, 536, 3.37, 10, 100, 100, 100),
('A10', 'Dosen A10', 171, 639, 3.24, 10, 100, 100, 100);

-- ─── Tabel hasil_perhitungan (riwayat) ─────────────────────────
CREATE TABLE IF NOT EXISTS hasil_perhitungan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    tanggal    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    jumlah_dosen INT NOT NULL,
    pemenang_kode VARCHAR(10),
    pemenang_nama VARCHAR(100),
    pemenang_vi   DECIMAL(5,4),
    detail_json TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
