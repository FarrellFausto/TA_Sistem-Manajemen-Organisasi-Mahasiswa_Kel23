<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] == 'viewer') {
    header("Location: login.php?pesan=restricted");
    exit();
}

if (isset($_GET['id'])) {
    $id_hapus = $_GET['id'];

    $sql = "DELETE FROM anggota WHERE id_anggota = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_hapus);

    if ($stmt->execute()) {
        header("Location: anggota_tampil.php?status=deleted");
        exit();
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }
} else {
    header("Location: anggota_tampil.php");
    exit();
}
?>
