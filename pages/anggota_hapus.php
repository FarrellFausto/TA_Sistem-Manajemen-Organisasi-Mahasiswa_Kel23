<?php
include '../config/koneksi.php';
session_start();
if ($_SESSION['role'] != 'Admin') die("Akses Ditolak!");

$id   = $_GET['id'];
$type = $_GET['type'];

if ($type == 'soft') {
    mysqli_query($conn, "UPDATE anggota SET deleted_at = NOW() WHERE id_anggota = $id");
} elseif ($type == 'restore') {
    // Balikin data dengan menset NULL pada deleted_at
    mysqli_query($conn, "UPDATE anggota SET deleted_at = NULL WHERE id_anggota = $id");
} else {
    // Hard Delete
    mysqli_query($conn, "DELETE FROM anggota WHERE id_anggota = $id");
}

header("Location: anggota_tampil.php");
?>