<?php
// ============================================================
// includes/log_helper.php - Helper untuk Logging Aktivitas
// ============================================================

/**
 * Catat aktivitas ke tabel log_aktivitas
 * @param mysqli $conn     - koneksi database
 * @param int    $id_user  - ID user yang melakukan aksi
 * @param string $aksi     - deskripsi aksi (teks bebas)
 */
function catat_log($conn, $id_user, $aksi) {
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (id_user, aksi) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("is", $id_user, $aksi);
        $stmt->execute();
        $stmt->close();
    }
}
