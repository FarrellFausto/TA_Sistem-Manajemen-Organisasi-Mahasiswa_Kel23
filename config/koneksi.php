<?php
// ============================================================
// config/koneksi.php — Koneksi Database + Load Session Manager
// ============================================================

// Load tab-aware session manager PERTAMA
require_once __DIR__ . '/session.php';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_organisasi_lama_backup";

$conn = mysqli_connect($host, $user, $pass, $db);
if ($conn) { mysqli_set_charset($conn, 'utf8mb4'); }

if (!$conn) {
    $base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
    header("Location: {$base}404.php?err=db");
    exit();
}
