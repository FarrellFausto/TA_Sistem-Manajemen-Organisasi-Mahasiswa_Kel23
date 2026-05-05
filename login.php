<?php
// ============================================================
// login.php — Login dengan Tab-Aware Session
// ============================================================
include 'config/koneksi.php';
// (session.php sudah di-load via koneksi.php)

// Kalau sudah login di tab ini, langsung ke dashboard
if ($ses_valid) {
    tab_redirect('pages/anggota_tampil.php');
}

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    // tsid dari form hidden field (di-generate JS sebelum submit)
    $new_tsid = trim($_POST['new_tsid'] ?? '');

    if (empty($new_tsid)) {
        $new_tsid = bin2hex(random_bytes(16));
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($data && password_verify($password, $data['password'])) {
        // Buat tab session baru untuk tab ini
        create_tab_session($new_tsid, $data['id_user'], $data['username'], $data['role']);

        // Set tsid global agar tab_redirect() bisa pakai
        global $tsid;
        $tsid = $new_tsid;

        tab_redirect('pages/anggota_tampil.php', [
            'success' => "Selamat datang, {$data['username']}!"
        ]);
    } else {
        $error = "Username atau password salah, bray!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — B-ORG System</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body{background:linear-gradient(135deg,#1a252f 0%,#2c3e50 60%,#1a252f 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .card{background:white;width:100%;max-width:420px;border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.4)}
    .card-header{background:linear-gradient(135deg,#2c3e50,#3498db);padding:40px 40px 30px;text-align:center;color:white}
    .logo{width:60px;height:60px;background:rgba(255,255,255,.2);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;margin-bottom:16px}
    .card-header h1{font-size:1.6rem;font-weight:800;margin-bottom:4px}
    .card-header p{opacity:.75;font-size:.85rem}
    .card-body{padding:35px 40px}
    .form-group{margin-bottom:18px}
    label{display:block;font-size:.82rem;font-weight:600;color:#5f6368;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
    input[type=text],input[type=password]{width:100%;padding:12px 15px;border:2px solid #e8eaed;border-radius:10px;font-size:.95rem;transition:.3s;color:#2c3e50;background:#fafafa}
    input:focus{outline:none;border-color:#3498db;background:white;box-shadow:0 0 0 4px rgba(52,152,219,.08)}
    .btn-submit{width:100%;padding:14px;background:linear-gradient(135deg,#2c3e50,#3498db);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:.3s;margin-top:8px}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(52,152,219,.4)}
    .alert-error{background:#fdf0f0;border:1px solid #f5c6cb;color:#c0392b;padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:18px;display:flex;align-items:center;gap:10px}
    .divider{text-align:center;color:#bdc3c7;font-size:.83rem;margin:20px 0;position:relative}
    .divider::before,.divider::after{content:'';position:absolute;top:50%;width:40%;height:1px;background:#e8eaed}
    .divider::before{left:0}.divider::after{right:0}
    .link{display:block;text-align:center;color:#3498db;text-decoration:none;font-size:.88rem;font-weight:500}
    .link:hover{text-decoration:underline}
    .loader{display:none;width:18px;height:18px;border:2px solid rgba(255,255,255,.4);border-top:2px solid white;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto}
    @keyframes spin{to{transform:rotate(360deg)}}
    .demo-box{background:#f0f8ff;border:1px solid #bee3f8;border-radius:10px;padding:12px 16px;margin-top:16px;font-size:.8rem;color:#2b6cb0}
    .demo-box strong{display:block;margin-bottom:4px;color:#1a365d}
    .tab-info{background:#fff8e1;border:1px solid #ffe082;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:.78rem;color:#f57f17;display:flex;align-items:center;gap:8px}
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <div class="logo">🏢</div>
    <h1>B-ORG SYSTEM</h1>
    <p>Sistem Manajemen Organisasi Mahasiswa</p>
  </div>
  <div class="card-body">
    <div class="tab-info">
      🔖 Setiap tab browser punya sesi independen — Admin &amp; Anggota bisa login bersamaan
    </div>

    <?php if($error): ?>
      <div class="alert-error"><span>⚠️</span><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
      <!-- tsid di-generate oleh JS sebelum submit, bukan dari cookie/localStorage -->
      <input type="hidden" name="new_tsid" id="newTsid">

      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" name="login" class="btn-submit" id="btnLogin">
        <span id="btnText">GAS LOGIN →</span>
        <div class="loader" id="loader"></div>
      </button>
    </form>

    <div class="divider">atau</div>
    <a href="register.php" class="link">Belum punya akun? Daftar di sini →</a>

    <div class="demo-box">
      <strong>📋 Demo Akun:</strong>
      Admin: <code>admin_utama</code> / <code>password</code><br>
      Anggota: <code>budi_santoso</code> / <code>password</code>
    </div>
  </div>
</div>
<script>
// Generate tsid unik untuk tab ini sebelum form submit
// Gunakan sessionStorage: per-tab, otomatis hilang saat tab ditutup
function generateTsid() {
    const arr = new Uint8Array(16);
    crypto.getRandomValues(arr);
    return Array.from(arr).map(b => b.toString(16).padStart(2,'0')).join('');
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    // Ambil tsid yang sudah ada di sessionStorage tab ini,
    // atau buat baru jika tab fresh (belum pernah login)
    let tsid = sessionStorage.getItem('b_org_tsid') || generateTsid();
    // Selalu buat tsid baru saat login baru di tab ini
    tsid = generateTsid();
    sessionStorage.setItem('b_org_tsid', tsid);
    document.getElementById('newTsid').value = tsid;

    // Loading state
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('loader').style.display = 'block';
});
</script>
</body>
</html>
