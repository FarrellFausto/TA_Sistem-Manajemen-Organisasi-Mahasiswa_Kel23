<?php
include '../config/koneksi.php';
session_start();
if ($_SESSION['role'] != 'Admin') die("Akses Ditolak!");

$id   = $_GET['id'];
$type = $_GET['type'];
$user_log = $_SESSION['username'] ?? 'Admin'; 

$aksi = ""; $ket = "";

// Menggunakan blok Try-Catch untuk Error Handling Database 
try {
    if ($type == 'soft') {
        $stmt = mysqli_prepare($conn, "UPDATE anggota SET deleted_at = NOW() WHERE id_anggota = ?");
        $aksi = "SOFT DELETE"; 
        $ket  = "Pindahkan anggota ID $id ke recycle bin";
    } elseif ($type == 'restore') {
        $stmt = mysqli_prepare($conn, "UPDATE anggota SET deleted_at = NULL WHERE id_anggota = ?");
        $aksi = "RESTORE"; 
        $ket  = "Mengembalikan anggota ID $id";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM anggota WHERE id_anggota = ?");
        $aksi = "DELETE"; 
        $ket  = "Hapus permanen anggota ID $id";
    }

    // Eksekusi Prepared Statement
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal mengeksekusi aksi hapus!");
    }

    // Insert ke Audit Log menggunakan Prepared Statement
    $stmt_log = mysqli_prepare($conn, "INSERT INTO audit_log (username, aktivitas, keterangan) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt_log, "sss", $user_log, $aksi, $ket);
    mysqli_stmt_execute($stmt_log);

} catch (Exception $e) {
    die("Sistem Error: " . $e->getMessage()); // Jika gagal, munculkan pesan error
}

header("Location: anggota_tampil.php");
?>
