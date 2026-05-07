<?php
<<<<<<< HEAD
session_start();
if (!isset($_SESSION['username'])) header("Location: login.php");
include 'config/koneksi.php';

$total_anggota = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM anggota WHERE deleted_at IS NULL"));
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body style="margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa;">
    <?php include 'includes/navbar.php'; ?>
    <div style="padding: 40px; max-width: 1200px; margin: auto;">
        <h1 style="color: #2c3e50;">Selamat Datang, <span style="color: #3498db;"><?= $_SESSION['username'] ?></span>! 👋</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">Anda masuk sebagai <b><?= $_SESSION['role'] ?></b>. Kelola data organisasi Anda di sini.</p>
        
        <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #3498db;">
                <h3 style="margin: 0; color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase;">Total Anggota Aktif</h3>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: #2c3e50;"><?= $total_anggota ?></p>
            </div>
        </div>
    </div>
=======
include 'config/koneksi.php';
require_login();

$total_anggota = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM anggota WHERE deleted_at IS NULL"))[0];

$total_periode = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM periode"))[0];
$total_proker  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM proker"))[0];
$total_log     = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM log_aktivitas"))[0];
$periode_aktif = mysqli_fetch_assoc(mysqli_query($conn,"SELECT label FROM periode ORDER BY id_periode DESC LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — B-ORG System</title>

  <!-- LINK CSS -->
  <link rel="stylesheet" href="index.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="main">
  <div class="welcome">
    <h1>Selamat Datang, <?= htmlspecialchars($ses_username) ?>! 👋</h1>
    <p>Login sebagai <strong><?= $ses_role ?></strong> · Periode Aktif: <strong><?= $periode_aktif['label'] ?? '-' ?></strong></p>
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

    <?php if (strtolower($ses_role) === 'admin'): ?>
    <div class="card card-red">
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

    <?php if(strtolower($ses_role) === 'admin'): ?>
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

>>>>>>> bismillah-acc
</body>
</html>
