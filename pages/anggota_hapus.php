<?php
include '../config/koneksi.php';
require_login('../');

if ($ses_role !== 'Admin') {
    tab_redirect('anggota_tampil.php', [
        'error' => 'Akses ditolak! Hanya Admin yang bisa menghapus data.'
    ]);
}

$id   = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'soft';

if ($id <= 0) {
    tab_redirect('anggota_tampil.php', [
        'error' => 'ID anggota tidak valid!'
    ]);
}

// SOFT DELETE (masuk tong sampah)
if ($type === 'soft') {
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NOW() WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    tab_redirect('anggota_tampil.php', [
        'success' => 'Anggota berhasil dipindahkan ke tong sampah.'
    ]);
}

// RESTORE
if ($type === 'restore') {
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NULL WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    tab_redirect('anggota_tampil.php', [
        'success' => 'Anggota berhasil direstore.'
    ]);
}

// HARD DELETE (hapus permanen)
if ($type === 'hard') {
    // hapus relasi proker dulu
    $stmt = $conn->prepare("DELETE FROM anggota_proker WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // hapus user yg terkait (jika ada)
    $stmt = $conn->prepare("DELETE FROM users WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // hapus anggota
    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    tab_redirect('anggota_tampil.php', [
        'success' => 'Anggota berhasil dihapus permanen.'
    ]);
}

tab_redirect('anggota_tampil.php', [
    'error' => 'Tipe hapus tidak valid!'
]);
