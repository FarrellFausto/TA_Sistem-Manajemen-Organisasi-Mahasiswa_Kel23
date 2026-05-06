<?php
// ============================================================
// login.php — Login dengan Tab-Aware Session
// ============================================================
include 'config/koneksi.php';

if ($ses_valid) {
    tab_redirect('index.php');
}

$error = '';

// AUTO COUNT
$jml_bidang  = 0;
$jml_anggota = 0;
$status_tahun = date('Y');

$qBidang = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bidang");
if ($qBidang) {
    $row = mysqli_fetch_assoc($qBidang);
    $jml_bidang = $row['total'] ?? 0;
}

$qAnggota = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggota WHERE deleted_at IS NULL");
if ($qAnggota) {
    $row = mysqli_fetch_assoc($qAnggota);
    $jml_anggota = $row['total'] ?? 0;
}

// PROSES LOGIN
if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $new_tsid = trim($_POST['new_tsid'] ?? '');

    if (empty($new_tsid)) {
        $new_tsid = bin2hex(random_bytes(16));
    }

    $stmt = $conn->prepare("
        SELECT u.*, a.deleted_at 
        FROM users u 
        LEFT JOIN anggota a ON u.id_anggota = a.id_anggota 
        WHERE u.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($data && password_verify($password, $data['password'])) {
        // Cek apakah akun sedang di "Tong Sampah" (soft delete)
        if ($data['deleted_at'] !== null) {
            $error = "Akun Anda sedang dinonaktifkan (berada di Tong Sampah). Silakan hubungi Admin untuk restore!";
        } else {
            create_tab_session($new_tsid, $data['id_user'], $data['username'], $data['role']);

            global $tsid;
            $tsid = $new_tsid;

            tab_redirect('index.php', [
                'success' => "Selamat datang kembali, {$data['username']}!"
            ]);
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — B-ORG System</title>

  <!-- LINK CSS -->
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="split-container">

  <!-- LEFT -->
  <div class="left-panel">

    <div class="status-badge">
      <span class="dot"></span>
      Status Aktif — <?= htmlspecialchars($status_tahun) ?>
    </div>

    <div class="brand">
      <div class="brand-logo">🏢</div>
      <div class="brand-text">
        <h2>B-ORG SYSTEM</h2>
        <p>Sistem Manajemen Organisasi Mahasiswa</p>
      </div>
    </div>

    <div class="hero-text">
      <h1>
        Setiap nama,<br>
        setiap data,<br>
        <span class="highlight">tercatat rapi.</span>
      </h1>
      <p>Pantau data organisasi secara real-time, cepat dan akurat.</p>
    </div>

    <div class="stats">
      <div class="stat-box">
        <div class="stat-value"><?= $jml_bidang ?>+</div>
        <div class="stat-label">Bidang Organisasi</div>
      </div>
      <div class="stat-box">
        <div class="stat-value"><?= $jml_anggota ?>+</div>
        <div class="stat-label">Anggota Terdaftar</div>
      </div>
      <div class="stat-box">
        <div class="stat-value">24/7</div>
        <div class="stat-label">Akses Sistem</div>
      </div>
    </div>

    <div class="footer-note">
      © <?= date('Y') ?> B-ORG System — PHP & MySQL
    </div>

  </div>

  <!-- RIGHT -->
  <div class="right-panel">

    <div class="card">
      <div class="card-header">
        <div class="logo">🏢</div>
        <h1>B-ORG SYSTEM</h1>
        <p>Login untuk masuk ke dashboard</p>
      </div>

      <div class="card-body">

        <?php if($error): ?>
          <div class="alert-error"><span>⚠️</span><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
          <input type="hidden" name="new_tsid" id="newTsid">

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
          </div>

          <button type="submit" name="login" class="btn-submit" id="btnLogin">
            <span id="btnText">GAS LOGIN →</span>
            <div class="loader" id="loader"></div>
          </button>
        </form>

        <div class="divider">atau</div>
        <a href="register.php" class="link">Belum punya akun? Daftar di sini →</a>

      </div>
    </div>

  </div>

</div>

<script>
function generateTsid() {
    const arr = new Uint8Array(16);
    crypto.getRandomValues(arr);
    return Array.from(arr).map(b => b.toString(16).padStart(2,'0')).join('');
}

document.getElementById('loginForm').addEventListener('submit', function() {
    let tsid = generateTsid();
    sessionStorage.setItem('b_org_tsid', tsid);
    document.getElementById('newTsid').value = tsid;

    document.getElementById('btnText').style.display = 'none';
    document.getElementById('loader').style.display = 'block';
});
</script>

</body>
</html>
