<?php
// ============================================================
// config/koneksi.php — Koneksi Database + Load Session Manager
// ============================================================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_organisasi_ta_prak_sbd";

// Koneksi DB dulu
$conn = mysqli_connect($host, $user, $pass, $db);

// Kalau gagal konek, redirect ke 404
if (!$conn) {
    $base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
    header("Location: {$base}404.php?err=db");
    exit();
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');

// Baru load tab-aware session manager setelah koneksi berhasil
require_once __DIR__ . '/session.php';