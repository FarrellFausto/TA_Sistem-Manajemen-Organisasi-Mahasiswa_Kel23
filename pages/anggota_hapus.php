<?php
include '../config/koneksi.php';
include '../includes/log_helper.php';
require_admin('../');

$id   = (int)($_GET['id']   ?? 0);
$type = trim($_GET['type']  ?? '');

if ($id <= 0 || empty($type)) {
    tab_redirect('anggota_tampil.php', ['error' => 'Parameter tidak valid.']);
}

// Ambil data anggota untuk log
$stmt_get = $conn->prepare("SELECT nama_lengkap, nim FROM anggota WHERE id_anggota = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$anggota = $stmt_get->get_result()->fetch_assoc();
$stmt_get->close();

if (!$anggota) {
    tab_redirect('anggota_tampil.php', ['error' => 'Data anggota tidak ditemukan.']);
}

$nama = $anggota['nama_lengkap'];
$nim  = $anggota['nim'];

if ($type === 'soft') {
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NOW() WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    catat_log($conn, $ses_id_user, "Admin $ses_username melakukan soft delete: $nama (NIM: $nim, ID: $id)");
    tab_redirect('anggota_tampil.php', ['warning' => "$nama dipindahkan ke tong sampah."]);

} elseif ($type === 'restore') {
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NULL WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    catat_log($conn, $ses_id_user, "Admin $ses_username merestore anggota: $nama (NIM: $nim, ID: $id)");
    tab_redirect('anggota_tampil.php', ['success' => "$nama berhasil di-restore!"]);

} elseif ($type === 'hard') {
    // 1. Hapus user terkait dulu (jika ada) agar tidak jadi data sampah
    $stmt_u = $conn->prepare("DELETE FROM users WHERE id_anggota = ?");
    $stmt_u->bind_param("i", $id);
    $stmt_u->execute();
    $stmt_u->close();

    // 2. Hapus data anggota secara permanen
    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "Admin $ses_username melakukan HARD DELETE: $nama (NIM: $nim, ID: $id) — DATA DIHAPUS PERMANEN BESERTA AKUNNYA");
    tab_redirect('anggota_tampil.php', ['success' => "$nama dan akunnya telah dihapus permanen dari sistem."]);

} else {
    tab_redirect('anggota_tampil.php', ['error' => 'Tipe operasi tidak dikenal.']);
}
