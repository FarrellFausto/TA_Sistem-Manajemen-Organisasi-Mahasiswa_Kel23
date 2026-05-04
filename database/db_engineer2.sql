-- ============================================================
-- SISTEM MANAJEMEN ORGANISASI MAHASISWA
-- DB Engineer 2 — Tabel Relasi & Sistem
-- Tabel: proker, anggota_proker, users, log_aktivitas
-- Branch: feat-database-relations
-- ⚠️ Jalankan SETELAH db_engineer1.sql berhasil di-merge
-- ============================================================

USE db_organisasi_mahasiswa;

-- 5. Tabel proker
CREATE TABLE proker (
  id_proker       INT           NOT NULL AUTO_INCREMENT,
  nama_proker     VARCHAR(200)  NOT NULL,
  deskripsi       TEXT,
  id_bidang       INT           NOT NULL,
  id_periode      INT           NOT NULL,
  tanggal_mulai   DATE          NULL,
  tanggal_selesai DATE          NULL,
  status_proker   ENUM('rencana','berjalan','selesai','dibatalkan') NOT NULL DEFAULT 'rencana',
  anggaran        DECIMAL(15,2) NULL DEFAULT 0,
  PRIMARY KEY (id_proker),
  CONSTRAINT fk_proker_bidang
    FOREIGN KEY (id_bidang) REFERENCES bidang (id_bidang)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_proker_periode
    FOREIGN KEY (id_periode) REFERENCES periode (id_periode)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel anggota_proker (bridge table)
CREATE TABLE anggota_proker (
  id_anggota_proker INT          NOT NULL AUTO_INCREMENT,
  id_anggota        INT          NOT NULL,
  id_proker         INT          NOT NULL,
  peran             VARCHAR(100) NOT NULL DEFAULT 'Panitia',
  PRIMARY KEY (id_anggota_proker),
  UNIQUE KEY uq_anggota_proker (id_anggota, id_proker),
  CONSTRAINT fk_ap_anggota
    FOREIGN KEY (id_anggota) REFERENCES anggota (id_anggota)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ap_proker
    FOREIGN KEY (id_proker) REFERENCES proker (id_proker)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabel users
CREATE TABLE users (
  id_user    INT          NOT NULL AUTO_INCREMENT,
  username   VARCHAR(50)  NOT NULL,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('superadmin','admin','viewer') NOT NULL DEFAULT 'admin',
  id_anggota INT          NULL,
  last_login DATETIME     NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_user),
  UNIQUE KEY uq_username (username),
  CONSTRAINT fk_user_anggota
    FOREIGN KEY (id_anggota) REFERENCES anggota (id_anggota)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Tabel log_aktivitas
CREATE TABLE log_aktivitas (
  id_log     INT         NOT NULL AUTO_INCREMENT,
  id_user    INT         NOT NULL,
  aksi       TEXT        NOT NULL,
  waktu      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) NULL,
  PRIMARY KEY (id_log),
  CONSTRAINT fk_log_user
    FOREIGN KEY (id_user) REFERENCES users (id_user)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA SAMPEL
-- ============================================================

-- Proker (5 data)
INSERT INTO proker
  (nama_proker, deskripsi, id_bidang, id_periode, tanggal_mulai, tanggal_selesai, status_proker, anggaran)
VALUES
('OSMB - Orientasi Mahasiswa Baru', 'Program pengenalan kampus bagi mahasiswa baru angkatan 2024', 1, 4, '2024-08-01', '2024-08-15', 'selesai',  5000000),
('Seminar Nasional Teknologi',      'Seminar dengan pembicara dari industri teknologi nasional',   2, 4, '2024-10-05', '2024-10-05', 'selesai',  8000000),
('Bimbingan Belajar Gratis',        'Program bimbel gratis untuk mahasiswa yang membutuhkan',      2, 4, '2024-09-01', '2024-12-31', 'berjalan', 2000000),
('Bakti Sosial Panti Asuhan',       'Kunjungan dan donasi ke panti asuhan sekitar kampus',         4, 4, '2024-12-15', '2024-12-15', 'selesai',  6000000),
('Lomba Futsal Mahasiswa',          'Turnamen futsal antar angkatan dan prodi',                    5, 4, '2025-01-15', '2025-01-20', 'rencana',  5000000);

-- Anggota Proker (5 data)
INSERT INTO anggota_proker (id_anggota, id_proker, peran) VALUES
(4, 1, 'Ketua Pelaksana'),
(5, 1, 'Sekretaris'),
(1, 2, 'Pengarah'),
(3, 2, 'MC'),
(2, 3, 'Bendahara');

-- Users (5 data)
INSERT INTO users (username, password, role, id_anggota) VALUES
('superadmin',  '$2b$12$examplehashSuperAdmin001',  'superadmin', NULL),
('rizky_admin', '$2b$12$examplehashRizky00123456',  'admin',      1),
('budi_admin',  '$2b$12$examplehashBudi001234567',  'admin',      3),
('ahmad_admin', '$2b$12$examplehashAhmad01234567',  'admin',      4),
('viewer1',     '$2b$12$examplehashViewer01234567', 'viewer',     5);

-- Log Aktivitas (5 data)
INSERT INTO log_aktivitas (id_user, aksi, waktu, ip_address) VALUES
(1, 'Superadmin menambahkan periode kepengurusan 2024/2025',              '2024-07-01 08:00:00', '192.168.1.1'),
(2, 'Admin Rizky menambahkan anggota baru: Ahmad Fauzi (NIM 2201040004)', '2024-07-15 09:30:00', '192.168.1.10'),
(3, 'Admin Budi menambahkan proker: Seminar Nasional Teknologi',          '2024-08-01 10:00:00', '192.168.1.11'),
(4, 'Admin Ahmad mengubah status proker OSMB menjadi selesai',            '2024-08-16 14:00:00', '192.168.1.12'),
(2, 'Admin Rizky menghapus anggota: Wahyu Nugroho',                      '2024-09-05 11:20:00', '192.168.1.10');
