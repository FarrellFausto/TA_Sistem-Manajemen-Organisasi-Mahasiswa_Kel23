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
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body{background:linear-gradient(135deg,#1a252f,#2c3e50);min-height:100vh;display:flex;align-items:center;justify-content:center;color:white}
    .card{text-align:center;padding:60px 50px;background:rgba(255,255,255,.06);backdrop-filter:blur(10px);border-radius:20px;border:1px solid rgba(255,255,255,.1);max-width:520px;width:90%}
    .code{font-size:6rem;font-weight:800;line-height:1;background:linear-gradient(135deg,#3498db,#9b59b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    h2{font-size:1.6rem;margin:16px 0 10px;color:#ecf0f1}
    p{color:#95a5a6;font-size:.95rem;line-height:1.7;margin-bottom:30px}
    .btn{display:inline-block;background:#3498db;color:white;padding:13px 30px;border-radius:8px;text-decoration:none;font-weight:600;transition:.3s;margin:5px}
    .btn:hover{background:#2980b9;transform:translateY(-2px)}
    .btn.outline{background:transparent;border:2px solid rgba(255,255,255,.3);color:#ecf0f1}
    .btn.outline:hover{background:rgba(255,255,255,.1)}
    .alert-db{background:rgba(231,76,60,.15);border:1px solid rgba(231,76,60,.4);border-radius:10px;padding:16px 20px;margin-bottom:24px;text-align:left;font-size:.85rem}
    .alert-db strong{color:#e74c3c}
    .alert-db code{background:rgba(0,0,0,.3);padding:2px 6px;border-radius:4px;font-family:monospace}
  </style>
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
  <p style="margin-top:30px;font-size:.75rem;color:#636e72">B-ORG System &copy; <?= date('Y') ?> — Kelompok 23</p>
</div>
</body>
</html>
