<?php
// ============================================================
// 404.php - Halaman Error / Koneksi Database Gagal
// ============================================================
$err = $_GET['err'] ?? '';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error - B-ORG System</title>

  <!-- LINK KE CSS -->
  <link rel="stylesheet" href="assets/css/404.css">
</head>
<body>
<div class="card">
  <?php if ($err === 'db'): ?>
    <div style="font-size:4rem;margin-bottom:16px">🔌</div>
    <h2>Koneksi Database Gagal</h2>
    <div class="alert-db">
      <strong>⚠️ Tidak dapat terhubung ke database.</strong><br><br>
      Pastikan langkah berikut sudah dilakukan:<br>
      1. XAMPP sudah berjalan (<strong>Apache</strong> & <strong>MySQL</strong> aktif)<br>
      2. Database <code>db_organisasi_ta_prak_sbd</code> sudah diimport<br>
      3. Kredensial di <code>config/koneksi.php</code> sudah benar
    </div>
    <p>Hubungi administrator sistem jika masalah berlanjut.</p>
    <a href="login.php" class="btn">🔄 Coba Lagi</a>
    <a href="javascript:history.back()" class="btn outline">← Kembali</a>
  <?php else: ?>
    <div class="code">404</div>
    <h2>Halaman Tidak Ditemukan</h2>
    <p>Halaman yang kamu cari tidak ada, mungkin sudah dipindah atau URL-nya salah ketik.</p>
    <a href="index.php" class="btn">🏠 Ke Dashboard</a>
    <a href="javascript:history.back()" class="btn outline">← Kembali</a>
  <?php endif; ?>
  <p class="footer">B-ORG System &copy; <?= date('Y') ?> — Kelompok 23</p>
</div>
</body>
</html>
