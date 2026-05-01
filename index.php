<?php
// index.php - Halaman Utama / Login
session_start();

// Judul Proyek: Sistem Manajemen Organisasi Mahasiswa Kel23
echo "<h1>Selamat Datang di Sistem Manajemen Organisasi</h1>";
echo "<p>Silakan gunakan menu navigasi untuk mengelola data anggota dan log aktivitas.</p>";

// Link contoh untuk teman-temanmu (nanti diganti Navbar)
echo "<ul>
        <li><a href='pages/anggota_tampil.php'>Data Anggota</a></li>
        <li><a href='pages/audit_log.php'>Log Aktivitas</a></li>
      </ul>";

// lanjutin lagi logika lainnya
?>

