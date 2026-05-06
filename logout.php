<?php
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
  <link rel="stylesheet" href="assets/css/logout.css">
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
