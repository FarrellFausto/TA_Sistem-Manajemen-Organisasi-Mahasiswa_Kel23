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
  <style>
    body{margin:0;background:#f0f3f8}
    .main{padding:30px;max-width:1200px;margin:auto}
    .welcome{background:linear-gradient(135deg,#2c3e50,#3498db);color:white;border-radius:16px;padding:30px;margin-bottom:28px;position:relative;overflow:hidden}
    .welcome::after{content:'🏢';position:absolute;right:30px;top:50%;transform:translateY(-50%);font-size:5rem;opacity:.15;pointer-events:none}
    .welcome h1{font-size:1.8rem;margin:0 0 6px}
    .welcome p{opacity:.8;margin:0;font-size:.95rem}
    .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px}
    .card{background:white;padding:24px;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);border-top:4px solid}
    .card-blue{border-color:#3498db}.card-green{border-color:#27ae60}.card-orange{border-color:#f39c12}.card-purple{border-color:#9b59b6}
    .card .num{font-size:2.5rem;font-weight:800;margin:8px 0;line-height:1}
    .card .label{font-size:.8rem;color:#95a5a6;text-transform:uppercase;letter-spacing:.5px;font-weight:600}
    .card .icon{font-size:1.6rem;margin-bottom:8px}
    .card-blue .num{color:#3498db}.card-green .num{color:#27ae60}.card-orange .num{color:#f39c12}.card-purple .num{color:#9b59b6}
    .quick-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px}
    .qlink{background:white;border-radius:12px;padding:20px;text-decoration:none;color:#2c3e50;box-shadow:0 2px 8px rgba(0,0,0,.06);transition:.3s;display:flex;align-items:center;gap:14px}
    .qlink:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.12)}
    .qlink .q-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
    .qlink strong{display:block;font-size:.92rem;margin-bottom:2px}
    .qlink span{color:#95a5a6;font-size:.78rem}
    h2{color:#2c3e50;margin:0 0 16px;font-size:1.1rem}
  </style>
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
