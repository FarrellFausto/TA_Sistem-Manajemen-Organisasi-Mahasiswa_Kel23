<?php
// ============================================================
// logout.php — Logout HANYA tab ini, tab lain tidak terpengaruh
// ============================================================
include 'config/koneksi.php';

$username = $ses_username ?? 'User';

// Hapus session HANYA untuk tsid tab ini
// Tab lain yang login dengan akun berbeda tetap aman
destroy_tab_session($tsid);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Logout — B-ORG</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body{background:linear-gradient(135deg,#1a252f,#2c3e50);min-height:100vh;display:flex;align-items:center;justify-content:center;color:white}
    .card{text-align:center;padding:50px 40px;background:rgba(255,255,255,.07);backdrop-filter:blur(12px);border-radius:20px;border:1px solid rgba(255,255,255,.1);box-shadow:0 20px 60px rgba(0,0,0,.3)}
    .wave{font-size:4rem;display:block;margin-bottom:20px;animation:wave 1.5s ease-in-out infinite}
    @keyframes wave{0%,100%{transform:rotate(0deg)}25%{transform:rotate(20deg)}75%{transform:rotate(-10deg)}}
    h2{font-size:1.6rem;font-weight:700;margin-bottom:8px}
    p{color:#95a5a6;margin-bottom:30px}
    .spinner{width:36px;height:36px;border:3px solid rgba(255,255,255,.15);border-top:3px solid #3498db;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 16px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .redirect-msg{font-size:.82rem;color:#636e72}
    .progress{width:200px;height:3px;background:rgba(255,255,255,.1);border-radius:2px;margin:12px auto 0;overflow:hidden}
    .progress-bar{height:100%;background:#3498db;border-radius:2px;animation:fill 3s linear forwards}
    @keyframes fill{from{width:0}to{width:100%}}
  </style>
</head>
<body>
  <div class="card">
    <span class="wave">👋</span>
    <h2>Sampai Jumpa, <?= htmlspecialchars($username) ?>!</h2>
    <p>Kamu telah logout dari tab ini.<br>Tab lain yang sedang aktif tidak terpengaruh.</p>
    <div class="spinner"></div>
    <div class="redirect-msg">Mengalihkan ke halaman login...</div>
    <div class="progress"><div class="progress-bar"></div></div>
  </div>
  <script>
    // Hapus tsid dari sessionStorage tab ini saja
    sessionStorage.removeItem('b_org_tsid');
    setTimeout(() => { window.location.href = 'login.php'; }, 3000);
  </script>
</body>
</html>
