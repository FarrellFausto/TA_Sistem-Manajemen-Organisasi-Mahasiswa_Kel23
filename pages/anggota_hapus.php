<?php
include '../config/koneksi.php';
include '../includes/log_helper.php';

// Proteksi Admin
if (!$ses_valid) {
    tab_redirect('../login.php');
}

if (!isset($ses_role) || $ses_role !== 'admin') {
    tab_redirect('../index.php', ['error' => 'Akses ditolak!']);
}

$id   = (int)($_GET['id'] ?? 0);
$type = trim($_GET['type'] ?? '');

if ($id <= 0 || empty($type)) {
    tab_redirect('anggota_tampil.php', ['error' => 'Parameter tidak valid.']);
}

// Ambil data anggota untuk keperluan logging
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

// Penanganan berdasarkan Tipe
if ($type === 'soft') {
    // Soft Delete: Isi kolom deleted_at dengan waktu sekarang
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NOW() WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "admin $ses_username melakukan soft delete: $nama (NIM: $nim)");
    tab_redirect('anggota_tampil.php', ['warning' => "$nama berhasil dinonaktifkan."]);

} elseif ($type === 'restore') {
    // Restore: Kosongkan kembali kolom deleted_at
    $stmt = $conn->prepare("UPDATE anggota SET deleted_at = NULL WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "admin $ses_username merestore anggota: $nama");
    tab_redirect('anggota_tampil.php', ['success' => "$nama berhasil diaktifkan kembali!"]);

} elseif ($type === 'hard') {
    // Hard Delete: Hapus baris dari tabel secara permanen
    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    catat_log($conn, $ses_id_user, "admin $ses_username melakukan HARD DELETE: $nama");
    tab_redirect('anggota_tampil.php', ['success' => "$nama dihapus permanen."]);

} else {
    tab_redirect('anggota_tampil.php', ['error' => 'Tipe operasi tidak dikenal.']);
}
?>
