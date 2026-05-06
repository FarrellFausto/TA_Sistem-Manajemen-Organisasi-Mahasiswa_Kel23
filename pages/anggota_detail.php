<?php
include '../config/koneksi.php';
require_login('../');

$id_anggota = (int)($_GET['id'] ?? 0);
if ($id_anggota <= 0) {
    tab_redirect('anggota_tampil.php', ['error' => 'ID Anggota tidak valid.']);
}

// Fetch detail anggota lengkap
$sql = "SELECT a.*, 
               b.nama_bidang, 
               j.nama_jabatan, 
               p.label AS tahun_periode,
               p.tahun_mulai, p.tahun_selesai,
               pr.nama_proker,
               u.username, u.role, u.created_at AS user_created
        FROM anggota a
        LEFT JOIN bidang b ON a.id_bidang = b.id_bidang
        LEFT JOIN jabatan j ON a.id_jabatan = j.id_jabatan
        LEFT JOIN periode p ON a.id_periode = p.id_periode
        LEFT JOIN anggota_proker ap ON a.id_anggota = ap.id_anggota
        LEFT JOIN proker pr ON ap.id_proker = pr.id_proker
        LEFT JOIN users u ON a.id_anggota = u.id_anggota
        WHERE a.id_anggota = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    tab_redirect('anggota_tampil.php', ['error' => 'Data anggota tidak ditemukan.']);
}

// Proteksi tambahan: jika bukan Admin, hanya bisa melihat detail diri sendiri? 
// Atau boleh melihat semua? User request bilang "kedua role bisa melihat data", 
// biasanya di sistem organisasi bisa melihat semua profil.
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Detail Anggota — <?= htmlspecialchars($data['nama_lengkap']) ?></title>
  <link rel="stylesheet" href="anggota_detail.css">
  <style>
    /* Inline style minimalis yang premium */
    .detail-container { max-width: 900px; margin: 30px auto; padding: 20px; }
    .profile-card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; flex-wrap: wrap; }
    .profile-sidebar { background: linear-gradient(135deg, #2c3e50, #34495e); color: #fff; padding: 40px; width: 300px; text-align: center; }
    .profile-main { flex: 1; padding: 40px; background: #fdfdfd; }
    .avatar-circle { width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem; border: 4px solid rgba(255,255,255,0.2); }
    .profile-sidebar h2 { margin: 0; font-size: 1.5rem; line-height: 1.2; }
    .profile-sidebar p { color: #bdc3c7; font-size: 0.9rem; margin-top: 10px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-top: 20px; }
    .info-item label { display: block; font-size: 0.75rem; color: #95a5a6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; font-weight: 700; }
    .info-item span { display: block; font-size: 1rem; color: #2c3e50; font-weight: 500; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #2c3e50; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; margin: 30px 0 20px; display: flex; align-items: center; gap: 10px; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-block; }
    .badge-blue { background: #e8f4fd; color: #3498db; }
    .badge-green { background: #eafaf1; color: #27ae60; }
    .badge-orange { background: #fff5e6; color: #f39c12; }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #7f8c8d; text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-bottom: 20px; transition: 0.3s; }
    .btn-back:hover { color: #2c3e50; transform: translateX(-5px); }
    @media (max-width: 768px) { .profile-card { flex-direction: column; } .profile-sidebar { width: 100%; padding: 30px; } }
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="main detail-container">
  <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn-back">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Kembali ke Daftar
  </a>

  <div class="profile-card">
    <div class="profile-sidebar">
      <div class="avatar-circle"><?= ($data['jenis_kelamin'] === 'L') ? '👨' : '👩' ?></div>
      <h2><?= htmlspecialchars($data['nama_lengkap']) ?></h2>
      <p><?= htmlspecialchars($data['nim']) ?></p>
      
      <div style="margin-top: 30px; text-align: left; font-size: 0.85rem; color: #bdc3c7;">
        <div style="margin-bottom: 10px;"><strong>Role:</strong> <span class="badge badge-orange" style="float:right"><?= strtoupper($data['role'] ?? 'VIEWER') ?></span></div>
        <div style="margin-bottom: 10px;"><strong>Status:</strong> <span class="badge badge-green" style="float:right"><?= strtoupper($data['status_anggota'] ?? 'AKTIF') ?></span></div>
        <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
           Bergabung sejak:<br>
           <span style="color: #fff;"><?= date('d F Y', strtotime($data['user_created'] ?? $data['created_at'] ?? 'now')) ?></span>
        </div>
      </div>
    </div>

    <div class="profile-main">
      <div class="section-title">👤 Informasi Pribadi</div>
      <div class="info-grid">
        <div class="info-item"><label>Nama Lengkap</label><span><?= htmlspecialchars($data['nama_lengkap']) ?></span></div>
        <div class="info-item"><label>NIM</label><span><?= htmlspecialchars($data['nim']) ?></span></div>
        <div class="info-item"><label>Jenis Kelamin</label><span><?= ($data['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan' ?></span></div>
        <div class="info-item"><label>Tanggal Lahir</label><span><?= $data['tanggal_lahir'] ? date('d M Y', strtotime($data['tanggal_lahir'])) : '-' ?></span></div>
        <div class="info-item"><label>Email</label><span><?= htmlspecialchars($data['email'] ?? '-') ?></span></div>
        <div class="info-item"><label>No. HP</label><span><?= htmlspecialchars($data['no_hp'] ?? '-') ?></span></div>
      </div>

      <div class="section-title">🎓 Pendidikan</div>
      <div class="info-grid">
        <div class="info-item"><label>Program Studi</label><span><?= htmlspecialchars($data['prodi'] ?? '-') ?></span></div>
        <div class="info-item"><label>Fakultas</label><span><?= htmlspecialchars($data['fakultas'] ?? '-') ?></span></div>
        <div class="info-item"><label>Angkatan</label><span><?= htmlspecialchars($data['angkatan'] ?? '-') ?></span></div>
      </div>

      <div class="section-title">🏢 Struktur Organisasi</div>
      <div class="info-grid">
        <div class="info-item"><label>Bidang</label><span class="badge badge-blue"><?= htmlspecialchars($data['nama_bidang'] ?? '-') ?></span></div>
        <div class="info-item"><label>Jabatan</label><span><?= htmlspecialchars($data['nama_jabatan'] ?? '-') ?></span></div>
        <div class="info-item"><label>Periode</label><span>📅 <?= htmlspecialchars($data['tahun_periode'] ?? '-') ?></span></div>
        <div class="info-item"><label>Program Kerja</label><span class="badge badge-orange"><?= htmlspecialchars($data['nama_proker'] ?? '-') ?></span></div>
      </div>
      
      <div style="margin-top: 40px; text-align: right;">
        <?php if(strtolower($ses_role) === 'admin'): ?>
          <a href="<?= tab_url('anggota_edit.php', ['id' => $data['id_anggota']]) ?>" class="badge badge-blue" style="text-decoration:none; padding: 10px 20px;">✏️ Edit Data</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
