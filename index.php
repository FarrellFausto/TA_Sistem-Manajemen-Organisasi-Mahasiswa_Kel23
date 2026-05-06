<?php
include 'config/koneksi.php';
require_login();

$total_anggota = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM anggota WHERE deleted_at IS NULL"))[0];
$total_periode = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM periode"))[0];
$total_proker  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM proker"))[0];
$total_log     = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM log_aktivitas"))[0];
$periode_aktif = mysqli_fetch_assoc(mysqli_query($conn,"SELECT tahun_periode FROM periode ORDER BY id_periode DESC LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — B-ORG System</title>

  <!-- LINK CSS -->
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="main">
  <div class="welcome">
    <h1>Selamat Datang, <?= htmlspecialchars($ses_username) ?>! 👋</h1>
    <p>Login sebagai <strong><?= $ses_role ?></strong> · Periode Aktif: <strong><?= $periode_aktif['tahun_periode'] ?? '-' ?></strong></p>
  </div>

  <div class="cards">
    <div class="card card-blue">
      <div class="icon">👥</div>
      <div class="label">Total Anggota Aktif</div>
      <div class="num"><?= $total_anggota ?></div>
    </div>

    <div class="card card-green">
      <div class="icon">📅</div>
      <div class="label">Periode Tercatat</div>
      <div class="num"><?= $total_periode ?></div>
    </div>

    <div class="card card-orange">
      <div class="icon">📋</div>
      <div class="label">Program Kerja</div>
      <div class="num"><?= $total_proker ?></div>
    </div>

    <?php if ($ses_role === 'Admin'): ?>
    <div class="card card-purple">
      <div class="icon">📝</div>
      <div class="label">Total Log Aktivitas</div>
      <div class="num"><?= $total_log ?></div>
    </div>
    <?php endif; ?>
  </div>

  <h2>⚡ Akses Cepat</h2>

  <div class="quick-links">
    <a href="<?= tab_url('pages/anggota_tampil.php') ?>" class="qlink">
      <div class="q-icon" style="background:#e8f4fd">👥</div>
      <div><strong>Data Anggota</strong><span>Lihat daftar semua anggota</span></div>
    </a>

    <?php if($ses_role === 'Admin'): ?>
    <a href="<?= tab_url('pages/anggota_tambah.php') ?>" class="qlink">
      <div class="q-icon" style="background:#eafaf1">➕</div>
      <div><strong>Tambah Anggota</strong><span>Daftarkan anggota baru</span></div>
    </a>

    <a href="<?= tab_url('pages/audit_log.php') ?>" class="qlink">
      <div class="q-icon" style="background:#f8f0ff">📝</div>
      <div><strong>Audit Log</strong><span>Histori aktivitas sistem</span></div>
    </a>
    <?php endif; ?>

    <a href="logout.php?tsid=<?= urlencode($tsid) ?>" class="qlink">
      <div class="q-icon" style="background:#fdf0f0">🚪</div>
      <div><strong>Logout Tab Ini</strong><span>Keluar dari sesi tab ini</span></div>
    </a>
  </div>
</div>

</body>
</html>
