<?php
<<<<<<< HEAD
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <meta http-equiv="refresh" content="2;url=login.php">
</head>
<body style="font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #2c3e50; color: white; margin: 0;">
    <div style="text-align: center;">
        <div style="font-size: 50px; margin-bottom: 20px;">👋</div>
        <h2>Berhasil Logout</h2>
        <p style="color: #bdc3c7;">Terima kasih bray! Mengalihkan ke halaman login...</p>
        <div style="margin-top: 20px; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; display: inline-block;"></div>
    </div>
    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</body>
</html>
=======
// ============================================================
// logout.php — Logout HANYA tab ini, tab lain tidak terpengaruh
// ============================================================
include 'config/koneksi.php';

$username = $ses_username ?? 'User';

// Hapus session HANYA untuk tsid tab ini
destroy_tab_session($tsid);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Logout — B-ORG</title>

  <!-- LINK CSS -->
  <link rel="stylesheet" href="logout.css">
</head>
<body>
  <div class="card">
    <span class="wave">👋</span>
    <h2>Sampai Jumpa, <?= htmlspecialchars($username) ?>!</h2>
    <p>Kamu telah logout dari tab ini.<br>Tab lain yang sedang aktif tidak terpengaruh.</p>

    <div class="spinner"></div>
    <div class="redirect-msg">Mengalihkan ke halaman login...</div>
    <div class="progress">
      <div class="progress-bar"></div>
    </div>
  </div>

  <script>
    // Hapus tsid dari sessionStorage tab ini saja
    sessionStorage.removeItem('b_org_tsid');
    setTimeout(() => { window.location.href = 'login.php'; }, 3000);
  </script>
</body>
</html>
>>>>>>> bismillah-acc
