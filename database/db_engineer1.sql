-- ============================================================
-- SISTEM MANAJEMEN ORGANISASI MAHASISWA
-- DB Engineer 1 — Tabel Master & Anggota
-- Tabel: periode, bidang, jabatan, anggota
-- Branch: feat-database-schema
-- ============================================================

USE db_organisasi_mahasiswa;

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
