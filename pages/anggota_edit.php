<?php
include '../config/koneksi.php';
include '../includes/log_helper.php';
require_admin('../');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { tab_redirect('anggota_tampil.php'); }

$stmt_get = $conn->prepare("SELECT a.*,tp.id_proker,p.tahun_periode FROM anggota a LEFT JOIN tugas_proker tp ON a.id_anggota=tp.id_anggota LEFT JOIN periode p ON a.id_periode=p.id_periode WHERE a.id_anggota=?");
$stmt_get->bind_param("i",$id); $stmt_get->execute();
$d = $stmt_get->get_result()->fetch_assoc(); $stmt_get->close();
if (!$d) { tab_redirect('anggota_tampil.php', ['error'=>'Data tidak ditemukan.']); }

$errors = [];

if (isset($_POST['update'])) {
    $nama       = trim($_POST['nama_lengkap'] ?? '');
    $id_bidang  = (int)($_POST['id_bidang']  ?? 0);
    $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker  = (int)($_POST['id_proker']  ?? 0);
    $id_periode = (int)($_POST['id_periode'] ?? 0);

    if (empty($nama))      $errors[] = "Nama tidak boleh kosong.";
    if ($id_periode === 0) $errors[] = "Periode wajib dipilih.";

    if (empty($errors)) {
        $s1 = $conn->prepare("UPDATE anggota SET nama_lengkap=?,id_bidang=?,id_jabatan=?,id_periode=? WHERE id_anggota=?");
        $s1->bind_param("siiiii",$nama,$id_bidang,$id_jabatan,$id_periode,$id); $s1->execute(); $s1->close();

        $s2 = $conn->prepare("DELETE FROM tugas_proker WHERE id_anggota=?");
        $s2->bind_param("i",$id); $s2->execute(); $s2->close();

        if ($id_proker > 0) {
            $s3 = $conn->prepare("INSERT INTO tugas_proker (id_anggota,id_proker) VALUES (?,?)");
            $s3->bind_param("ii",$id,$id_proker); $s3->execute(); $s3->close();
        }

        $perubahan = "Nama:{$d['nama_lengkap']}→$nama | Bidang:{$d['id_bidang']}→$id_bidang | Jabatan:{$d['id_jabatan']}→$id_jabatan | Periode:{$d['id_periode']}→$id_periode";
        catat_log($conn, $ses_id_user, "Admin $ses_username mengedit data: {$d['nama_lengkap']} (ID:$id) — $perubahan");
        tab_redirect('anggota_tampil.php', ['success' => "Data $nama berhasil diperbarui!"]);
    }
}

$bidang  = mysqli_query($conn,"SELECT * FROM bidang ORDER BY nama_bidang");
$jabatan = mysqli_query($conn,"SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn,"SELECT * FROM periode ORDER BY id_periode DESC");
$proker  = mysqli_query($conn,"SELECT p.*,COUNT(tp.id_tugas) AS terisi FROM proker p LEFT JOIN tugas_proker tp ON p.id_proker=tp.id_proker GROUP BY p.id_proker HAVING terisi<p.kuota_maksimal OR p.id_proker='".(int)$d['id_proker']."' ORDER BY p.nama_proker");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Anggota — B-ORG</title>
  <style>
    body{margin:0;background:#f0f3f8}
    .main{max-width:640px;margin:30px auto;padding:0 20px}
    .card{background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .card-header{background:linear-gradient(135deg,#2c3e50,#3498db);padding:24px 30px;color:white}
    .card-header h2{margin:0 0 4px;font-size:1.3rem}
    .card-header p{margin:0;opacity:.8;font-size:.85rem}
    .card-body{padding:30px}
    .info-bar{background:#e8f4fd;border:1px solid #bee3f8;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:.84rem;color:#2980b9}
    .sec{font-size:.74rem;font-weight:700;color:#3498db;text-transform:uppercase;letter-spacing:1px;margin:20px 0 10px;padding-bottom:6px;border-bottom:2px solid #e8f4fd}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .fg{margin-bottom:14px}
    label{display:block;font-size:.82rem;font-weight:600;color:#5f6368;margin-bottom:5px}
    .req{color:#e74c3c}
    input,select{width:100%;padding:11px 14px;border:2px solid #e8eaed;border-radius:9px;font-size:.9rem;transition:.3s;color:#2c3e50;background:#fafafa;-webkit-appearance:none}
    input:focus,select:focus{outline:none;border-color:#3498db;background:white}
    input[readonly]{background:#f0f3f8;color:#7f8c8d;cursor:not-allowed}
    .errors{background:#fdf0f0;border:1px solid #f5c6cb;border-radius:10px;padding:14px 18px;margin-bottom:18px;font-size:.85rem;color:#c0392b}
    .errors ul{margin:6px 0 0 16px}.errors li{margin-bottom:3px}
    .btn-row{display:flex;gap:12px;margin-top:22px}
    .btn{padding:12px 22px;border-radius:9px;font-weight:700;font-size:.9rem;cursor:pointer;border:none;text-decoration:none;transition:.3s;display:inline-flex;align-items:center;gap:6px}
    .btn-blue{background:#3498db;color:white;flex:1;justify-content:center}.btn-blue:hover{background:#2980b9}
    .btn-outline{background:white;color:#7f8c8d;border:2px solid #e8eaed}.btn-outline:hover{border-color:#bdc3c7}
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Data Anggota</h2>
      <p>Perubahan akan dicatat di log aktivitas</p>
    </div>
    <div class="card-body">
      <div class="info-bar">ℹ️ Mengedit: <strong><?=htmlspecialchars($d['nama_lengkap'])?></strong> — NIM: <code><?=htmlspecialchars($d['nim'])?></code></div>
      <?php if(!empty($errors)): ?>
        <div class="errors"><strong>⚠️ Terdapat kesalahan:</strong>
          <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="tsid" value="<?=htmlspecialchars($tsid)?>">
        <div class="sec">📋 Biodata</div>
        <div class="row2">
          <div class="fg"><label>Nama Lengkap <span class="req">*</span></label>
            <input type="text" name="nama_lengkap" required value="<?=htmlspecialchars($_POST['nama_lengkap']??$d['nama_lengkap'])?>"></div>
          <div class="fg"><label>NIM (tidak dapat diubah)</label>
            <input type="text" value="<?=htmlspecialchars($d['nim'])?>" readonly></div>
        </div>
        <div class="sec">🏢 Organisasi</div>
        <div class="fg"><label>Periode <span class="req">*</span></label>
          <select name="id_periode" required>
            <option value="">-- Pilih Periode --</option>
            <?php while($p=mysqli_fetch_assoc($periode)): $sel=(($_POST['id_periode']??$d['id_periode'])==$p['id_periode'])?'selected':''; ?>
              <option value="<?=$p['id_periode']?>" <?=$sel?>>📅 <?=htmlspecialchars($p['tahun_periode'])?></option>
            <?php endwhile; ?>
          </select></div>
        <div class="row2">
          <div class="fg"><label>Bidang</label>
            <select name="id_bidang" required>
              <?php while($b=mysqli_fetch_assoc($bidang)): $sel=(($_POST['id_bidang']??$d['id_bidang'])==$b['id_bidang'])?'selected':''; ?>
                <option value="<?=$b['id_bidang']?>" <?=$sel?>><?=htmlspecialchars($b['nama_bidang'])?></option>
              <?php endwhile; ?></select></div>
          <div class="fg"><label>Jabatan</label>
            <select name="id_jabatan" required>
              <?php while($j=mysqli_fetch_assoc($jabatan)): $sel=(($_POST['id_jabatan']??$d['id_jabatan'])==$j['id_jabatan'])?'selected':''; ?>
                <option value="<?=$j['id_jabatan']?>" <?=$sel?>><?=htmlspecialchars($j['nama_jabatan'])?></option>
              <?php endwhile; ?></select></div>
        </div>
        <div class="fg"><label>Program Kerja</label>
          <select name="id_proker">
            <option value="">-- Tanpa Proker --</option>
            <?php while($pk=mysqli_fetch_assoc($proker)): $sel=(($_POST['id_proker']??$d['id_proker'])==$pk['id_proker'])?'selected':''; ?>
              <option value="<?=$pk['id_proker']?>" <?=$sel?>><?=htmlspecialchars($pk['nama_proker'])?></option>
            <?php endwhile; ?></select></div>
        <div class="btn-row">
          <a href="<?=tab_url('anggota_tampil.php')?>" class="btn btn-outline">← Batal</a>
          <button type="submit" name="update" class="btn btn-blue">💾 Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
