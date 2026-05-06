<?php
include '../config/koneksi.php';
include '../includes/log_helper.php';

// ============================================================
// require_admin FIX: role admin huruf kecil
// ============================================================
if (!$ses_valid) {
    tab_redirect('../login.php');
}

if (!isset($ses_role) || $ses_role !== 'admin') {
    tab_redirect('../index.php', ['error' => 'Akses ditolak! Hanya admin yang bisa melakukan aksi ini.']);
}

$id   = (int)($_GET['id'] ?? 0);
$type = trim($_GET['type'] ?? '');

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

    catat_log($conn, $ses_id_user, "admin $ses_username melakukan soft delete: $nama (NIM: $nim, ID: $id)");

    tab_redirect('anggota_tampil.php', ['warning' => "$nama dipindahkan ke tong sampah."]);

} elseif ($type === 'restore') {

    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NULL WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "admin $ses_username merestore anggota: $nama (NIM: $nim, ID: $id)");

    tab_redirect('anggota_tampil.php', ['success' => "$nama berhasil di-restore!"]);

} elseif ($type === 'hard') {

    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "admin $ses_username melakukan HARD DELETE: $nama (NIM: $nim, ID: $id) — DATA DIHAPUS PERMANEN");

    tab_redirect('anggota_tampil.php', ['success' => "$nama berhasil dihapus permanen dari sistem."]);

} else {
    tab_redirect('anggota_tampil.php', ['error' => 'Tipe operasi tidak dikenal.']);
}
?>
