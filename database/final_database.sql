-- ============================================================
-- SISTEM MANAJEMEN ORGANISASI MAHASISWA
-- DB Engineer 1 — Tabel Master & Anggota
-- Tabel: periode, bidang, jabatan, anggota
-- Branch: feat-database-schema
-- ============================================================

USE db_organisasi_ta_prak_sbd;

-- 1. Tabel periode
CREATE TABLE periode (
  id_periode    INT          NOT NULL AUTO_INCREMENT,
  tahun_mulai   YEAR         NOT NULL,
  tahun_selesai YEAR         NOT NULL,
  label         VARCHAR(20)  NOT NULL,
  status        ENUM('aktif','tidak aktif') NOT NULL DEFAULT 'tidak aktif',
  PRIMARY KEY (id_periode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel bidang
CREATE TABLE bidang (
  id_bidang   INT          NOT NULL AUTO_INCREMENT,
  nama_bidang VARCHAR(100) NOT NULL,
  deskripsi   TEXT,
  PRIMARY KEY (id_bidang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel jabatan
CREATE TABLE jabatan (
  id_jabatan   INT          NOT NULL AUTO_INCREMENT,
  nama_jabatan VARCHAR(100) NOT NULL,
  id_bidang    INT          NULL,
  PRIMARY KEY (id_jabatan),
  CONSTRAINT fk_jabatan_bidang
    FOREIGN KEY (id_bidang) REFERENCES bidang (id_bidang)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel anggota
CREATE TABLE anggota (
  id_anggota     INT           NOT NULL AUTO_INCREMENT,
  nim            VARCHAR(20)   NOT NULL,
  nama_lengkap   VARCHAR(150)  NOT NULL,
  jenis_kelamin  ENUM('L','P') NOT NULL,
  tanggal_lahir  DATE          NULL,
  email          VARCHAR(100)  NULL,
  no_hp          VARCHAR(20)   NULL,
  prodi          VARCHAR(100)  NULL,
  fakultas       VARCHAR(100)  NULL,
  angkatan       YEAR          NULL,
  id_jabatan     INT           NOT NULL,
  id_bidang      INT           NULL,
  id_periode     INT           NOT NULL,
  status_anggota ENUM('aktif','tidak aktif','alumni') NOT NULL DEFAULT 'aktif',
  deleted_at     DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id_anggota),
  UNIQUE KEY uq_nim (nim),
  CONSTRAINT fk_anggota_jabatan
    FOREIGN KEY (id_jabatan) REFERENCES jabatan (id_jabatan)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_anggota_bidang
    FOREIGN KEY (id_bidang) REFERENCES bidang (id_bidang)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_anggota_periode
    FOREIGN KEY (id_periode) REFERENCES periode (id_periode)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA SAMPEL
-- ============================================================

-- Periode (5 data)
INSERT INTO periode (tahun_mulai, tahun_selesai, label, status) VALUES
(2021, 2022, '2021/2022', 'tidak aktif'),
(2022, 2023, '2022/2023', 'tidak aktif'),
(2023, 2024, '2023/2024', 'tidak aktif'),
(2024, 2025, '2024/2025', 'aktif'),
(2025, 2026, '2025/2026', 'tidak aktif');

-- Bidang (5 data)
INSERT INTO bidang (nama_bidang, deskripsi) VALUES
('Kaderisasi',        'Bertanggung jawab atas rekrutmen dan pengembangan anggota baru'),
('Akademik',          'Mengelola kegiatan akademik dan pengembangan prestasi mahasiswa'),
('Humas',             'Hubungan masyarakat, publikasi, dan media sosial organisasi'),
('Sosial Masyarakat', 'Kegiatan pengabdian dan kepedulian sosial kepada masyarakat'),
('Olahraga',          'Pembinaan kegiatan olahraga dan kesehatan anggota');

-- Jabatan (5 data)
INSERT INTO jabatan (nama_jabatan, id_bidang) VALUES
('Ketua Umum',              NULL),
('Sekretaris Umum',         NULL),
('Bendahara Umum',          NULL),
('Ketua Bidang Kaderisasi', 1),
('Anggota',                 NULL);

-- Anggota (5 data)
INSERT INTO anggota
  (nim, nama_lengkap, jenis_kelamin, tanggal_lahir, email, no_hp,
   prodi, fakultas, angkatan, id_jabatan, id_bidang, id_periode, status_anggota)
VALUES
('2201010001', 'Rizky Firmansyah', 'L', '2002-03-14', 'rizky.firmansyah@email.com', '081234567801', 'Teknik Informatika', 'Teknik',    2022, 1, NULL, 4, 'aktif'),
('2201020002', 'Siti Nurhaliza',   'P', '2002-07-21', 'siti.nurhaliza@email.com',   '081234567802', 'Manajemen',          'Ekonomi',   2022, 2, NULL, 4, 'aktif'),
('2201030003', 'Budi Santoso',     'L', '2002-11-05', 'budi.santoso@email.com',     '081234567803', 'Ilmu Komunikasi',    'FISIP',     2022, 3, NULL, 4, 'aktif'),
('2201040004', 'Ahmad Fauzi',      'L', '2003-04-22', 'ahmad.fauzi@email.com',      '081234567804', 'Psikologi',          'Psikologi', 2022, 4, 1,   4, 'aktif'),
('2201050005', 'Nurul Hidayah',    'P', '2003-06-30', 'nurul.hidayah@email.com',    '081234567805', 'Pendidikan Biologi', 'FKIP',      2022, 5, 2,   4, 'aktif');

-- ============================================================
-- SISTEM MANAJEMEN ORGANISASI MAHASISWA
-- DB Engineer 2 — Tabel Relasi & Sistem
-- Tabel: proker, anggota_proker, users, log_aktivitas
-- Branch: feat-database-relations
-- ⚠️ Jalankan SETELAH db_engineer1.sql berhasil di-merge
-- ============================================================

USE db_organisasi_ta_prak_sbd;

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
  id_user    INT         NULL,
  aksi       TEXT        NOT NULL,
  waktu      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) NULL,
  PRIMARY KEY (id_log),
  CONSTRAINT fk_log_user
    FOREIGN KEY (id_user) REFERENCES users (id_user)
    ON UPDATE CASCADE ON DELETE SET NULL
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
('superadmin',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  'superadmin', NULL),
('rizky_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  'admin',      1),
('budi_admin',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  'admin',      3),
('ahmad_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  'admin',      4),
('viewer1',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  'viewer',     5);

-- Log Aktivitas (5 data)
INSERT INTO log_aktivitas (id_user, aksi, waktu, ip_address) VALUES
(1, 'Superadmin menambahkan periode kepengurusan 2024/2025',              '2024-07-01 08:00:00', '192.168.1.1'),
(2, 'Admin Rizky menambahkan anggota baru: Ahmad Fauzi (NIM 2201040004)', '2024-07-15 09:30:00', '192.168.1.10'),
(3, 'Admin Budi menambahkan proker: Seminar Nasional Teknologi',          '2024-08-01 10:00:00', '192.168.1.11'),
(4, 'Admin Ahmad mengubah status proker OSMB menjadi selesai',            '2024-08-16 14:00:00', '192.168.1.12'),
(2, 'Admin Rizky menghapus anggota: Wahyu Nugroho',                      '2024-09-05 11:20:00', '192.168.1.10');