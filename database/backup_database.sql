-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 05:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_organisasi_lama_backup`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_bidang` int(11) DEFAULT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `id_periode` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `id_user`, `id_bidang`, `id_jabatan`, `id_periode`, `nama_lengkap`, `nim`, `deleted_at`) VALUES
(1, 3, 1, 4, 1, 'Budi Santoso', '2023011001', NULL),
(2, 4, 2, 4, 1, 'Siti Rahayu', '2023021002', NULL),
(3, 5, 3, 4, 1, 'Andi Firmansyah', '2023031003', NULL),
(4, 6, 4, 4, 1, 'Dewi Lestari', '2023041004', NULL),
(5, 7, 1, 5, 1, 'Reza Maulana', '2023011005', NULL),
(6, 8, 2, 5, 1, 'Novi Andriani', '2023021006', NULL),
(7, 9, 1, 4, 2, 'Fajar Nugroho', '2024011007', NULL),
(8, 10, 2, 4, 2, 'Maya Putri', '2024021008', NULL),
(9, 11, 3, 4, 2, 'Dimas Pratama', '2024031009', NULL),
(10, 12, 4, 4, 2, 'Ratna Sari', '2024041010', NULL),
(11, 13, 1, 1, 2, 'Hendra Wijaya', '2024011011', NULL),
(12, 14, 2, 5, 2, 'Fitri Amalia', '2024021012', NULL),
(13, 15, 3, 5, 2, 'Galih Saputro', '2024031013', NULL),
(15, 17, 1, 1, 3, 'Joko Susilo', '2025011015', NULL),
(16, 18, 2, 2, 3, 'Karina Dewi', '2025021016', NULL),
(17, 19, 3, 3, 3, 'Lukman Hakim', '2025031017', NULL),
(18, 20, 1, 4, 3, 'Mira Kusuma', '2025011018', NULL),
(19, 21, 2, 4, 3, 'Nanda Prasetyo', '2025021019', NULL),
(20, 22, 3, 4, 3, 'Okta Safitri', '2025031020', NULL),
(21, 23, 4, 4, 3, 'Pandu Wibowo', '2025041021', NULL),
(22, 24, 1, 5, 3, 'Qori Nabila', '2025011022', NULL),
(23, 25, 2, 5, 3, 'Rizky Aditya', '2025021023', NULL),
(24, 26, 3, 5, 3, 'wowok el pedri', '111', NULL),
(25, 27, 1, 5, 2, 'del poke mon', '90817', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bidang`
--

CREATE TABLE `bidang` (
  `id_bidang` int(11) NOT NULL,
  `nama_bidang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bidang`
--

INSERT INTO `bidang` (`id_bidang`, `nama_bidang`) VALUES
(1, 'Ristek'),
(2, 'Humas'),
(3, 'PSDM'),
(4, 'Medinfo');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`) VALUES
(1, 'Ketua Umum'),
(2, 'Sekretaris Umum'),
(3, 'Bendahara Umum'),
(4, 'Kepala Bidang'),
(5, 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `aksi` text NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aksi`, `waktu`) VALUES
(1, 1, 'Admin admin_utama menambahkan anggota baru: Budi Santoso (NIM: 2023011001)', '2024-08-01 08:00:00'),
(2, 1, 'Admin admin_utama menambahkan anggota baru: Siti Rahayu (NIM: 2023021002)', '2024-08-01 08:05:00'),
(3, 1, 'Admin admin_utama menambahkan anggota baru: Andi Firmansyah (NIM: 2023031003)', '2024-08-01 08:10:00'),
(4, 1, 'Admin admin_utama menambahkan anggota baru: Dewi Lestari (NIM: 2023041004)', '2024-08-01 08:15:00'),
(5, 1, 'Admin admin_utama menambahkan anggota baru: Reza Maulana (NIM: 2023011005)', '2024-08-02 09:00:00'),
(6, 1, 'Admin admin_utama menambahkan anggota baru: Novi Andriani (NIM: 2023021006)', '2024-08-02 09:05:00'),
(7, 2, 'Admin admin_sekre mengedit data anggota: Budi Santoso (ID: 1)', '2024-09-10 10:30:00'),
(8, 2, 'Admin admin_sekre menambahkan anggota baru: Fajar Nugroho (NIM: 2024011007)', '2025-08-01 08:00:00'),
(9, 2, 'Admin admin_sekre menambahkan anggota baru: Maya Putri (NIM: 2024021008)', '2025-08-01 08:10:00'),
(10, 2, 'Admin admin_sekre menambahkan anggota baru: Dimas Pratama (NIM: 2024031009)', '2025-08-01 08:20:00'),
(11, 1, 'Admin admin_utama melakukan soft delete anggota: Galih Saputro (ID: 13)', '2025-09-15 14:00:00'),
(12, 1, 'Admin admin_utama merestore anggota: Galih Saputro (ID: 13)', '2025-09-16 09:00:00'),
(13, 1, 'Admin admin_utama menambahkan anggota baru: Joko Susilo (NIM: 2025011015)', '2026-08-01 08:00:00'),
(14, 1, 'Admin admin_utama menambahkan anggota baru: Karina Dewi (NIM: 2025021016)', '2026-08-01 08:10:00'),
(15, 1, 'Admin admin_utama menambahkan anggota baru: Lukman Hakim (NIM: 2025031017)', '2026-08-01 08:20:00'),
(16, 1, 'Admin admin_utama menambahkan anggota baru: wowok el pedri (NIM: 111)', '2026-05-04 07:05:26'),
(17, 1, 'Admin admin_utama melakukan soft delete: Joko Susilo (NIM: 2025011015, ID: 15)', '2026-05-04 07:15:13'),
(18, 1, 'Admin admin_utama merestore anggota: Joko Susilo (NIM: 2025011015, ID: 15)', '2026-05-04 07:15:23'),
(19, 1, 'Admin admin_utama melakukan soft delete: Indri Wahyuni (NIM: 2024041014, ID: 14)', '2026-05-04 07:17:12'),
(20, 1, 'Admin admin_utama melakukan HARD DELETE: Indri Wahyuni (NIM: 2024041014, ID: 14) — DATA DIHAPUS PERMANEN', '2026-05-04 07:18:02'),
(21, 1, 'Admin admin_utama menambahkan anggota baru: del poke mon (NIM: 90817)', '2026-05-04 07:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `periode`
--

CREATE TABLE `periode` (
  `id_periode` int(11) NOT NULL,
  `tahun_periode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `periode`
--

INSERT INTO `periode` (`id_periode`, `tahun_periode`) VALUES
(1, '2023/2024'),
(2, '2024/2025'),
(3, '2025/2026');

-- --------------------------------------------------------

--
-- Table structure for table `proker`
--

CREATE TABLE `proker` (
  `id_proker` int(11) NOT NULL,
  `id_bidang` int(11) DEFAULT NULL,
  `nama_proker` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kuota_maksimal` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proker`
--

INSERT INTO `proker` (`id_proker`, `id_bidang`, `nama_proker`, `deskripsi`, `kuota_maksimal`) VALUES
(1, 1, 'Workshop IoT', 'Workshop Internet of Things untuk anggota', 2),
(2, 1, 'Kompetisi Coding', 'Persiapan lomba coding tingkat nasional', 2),
(3, 2, 'Social Media Branding', 'Pengelolaan media sosial organisasi', 2),
(4, 2, 'Open Recruitment', 'Rekrutmen anggota baru periode berjalan', 2),
(5, 3, 'Training Kepemimpinan', 'Pelatihan soft skill dan kepemimpinan', 2),
(6, 3, 'Mentoring Anggota Baru', 'Program mentor-mentee untuk anggota baru', 2),
(7, 4, 'Desain Konten Grafis', 'Pembuatan konten visual untuk organisasi', 2),
(8, 4, 'Manajemen Website', 'Pengelolaan website resmi organisasi', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tugas_proker`
--

CREATE TABLE `tugas_proker` (
  `id_tugas` int(11) NOT NULL,
  `id_anggota` int(11) DEFAULT NULL,
  `id_proker` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas_proker`
--

INSERT INTO `tugas_proker` (`id_tugas`, `id_anggota`, `id_proker`) VALUES
(1, 1, 1),
(2, 2, 3),
(3, 5, 2),
(4, 6, 4),
(5, 7, 1),
(6, 8, 3),
(7, 9, 5),
(8, 10, 7),
(9, 11, 2),
(10, 12, 4),
(11, 13, 6),
(13, 15, 2),
(14, 16, 3),
(15, 18, 1),
(16, 19, 4),
(17, 20, 5),
(18, 21, 7),
(19, 22, 2),
(20, 23, 3),
(21, 24, 6),
(22, 25, 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','Anggota') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'admin_utama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'admin_sekre', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(3, 'budi_santoso', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(4, 'siti_rahayu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(5, 'andi_firmansyah', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(6, 'dewi_lestari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(7, 'reza_maulana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(8, 'novi_andriani', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(9, 'fajar_nugroho', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(10, 'maya_putri', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(11, 'dimas_pratama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(12, 'ratna_sari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(13, 'hendra_wijaya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(14, 'fitri_amalia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(15, 'galih_saputro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(16, 'indri_wahyuni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(17, 'joko_susilo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(18, 'karina_dewi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(19, 'lukman_hakim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(20, 'mira_kusuma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(21, 'nanda_prasetyo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(22, 'okta_safitri', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(23, 'pandu_wibowo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(24, 'qori_nabila', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(25, 'rizky_aditya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anggota'),
(26, 'wok', '$2y$10$sUpRBd02C1iiI9D8Y4ZBvecoy1WR8yLf2tLg1kMZLei9JWqgf3jiC', 'Anggota'),
(27, 'poke', '$2y$10$oa4rn3WrwL1IjkeCFedLb.L5EOC25nIJD4wkzeNd56KTxOGHvO2uK', 'Anggota');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `uq_nim` (`nim`),
  ADD KEY `idx_id_user` (`id_user`),
  ADD KEY `idx_id_bidang` (`id_bidang`),
  ADD KEY `idx_id_jabatan` (`id_jabatan`),
  ADD KEY `idx_id_periode` (`id_periode`);

--
-- Indexes for table `bidang`
--
ALTER TABLE `bidang`
  ADD PRIMARY KEY (`id_bidang`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_id_user` (`id_user`);

--
-- Indexes for table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`id_periode`),
  ADD UNIQUE KEY `uq_tahun_periode` (`tahun_periode`);

--
-- Indexes for table `proker`
--
ALTER TABLE `proker`
  ADD PRIMARY KEY (`id_proker`),
  ADD KEY `idx_id_bidang` (`id_bidang`);

--
-- Indexes for table `tugas_proker`
--
ALTER TABLE `tugas_proker`
  ADD PRIMARY KEY (`id_tugas`),
  ADD UNIQUE KEY `uq_id_anggota` (`id_anggota`),
  ADD KEY `idx_id_proker` (`id_proker`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `uq_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `bidang`
--
ALTER TABLE `bidang`
  MODIFY `id_bidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `periode`
--
ALTER TABLE `periode`
  MODIFY `id_periode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `proker`
--
ALTER TABLE `proker`
  MODIFY `id_proker` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tugas_proker`
--
ALTER TABLE `tugas_proker`
  MODIFY `id_tugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `fk_anggota_bidang` FOREIGN KEY (`id_bidang`) REFERENCES `bidang` (`id_bidang`),
  ADD CONSTRAINT `fk_anggota_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`),
  ADD CONSTRAINT `fk_anggota_periode` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`),
  ADD CONSTRAINT `fk_anggota_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `proker`
--
ALTER TABLE `proker`
  ADD CONSTRAINT `fk_proker_bidang` FOREIGN KEY (`id_bidang`) REFERENCES `bidang` (`id_bidang`) ON DELETE CASCADE;

--
-- Constraints for table `tugas_proker`
--
ALTER TABLE `tugas_proker`
  ADD CONSTRAINT `fk_tugas_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_proker` FOREIGN KEY (`id_proker`) REFERENCES `proker` (`id_proker`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
