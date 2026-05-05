<?php
include '../config/koneksi.php';
include '../includes/log_helper.php';
require_admin('../');

$errors = [];

if (isset($_POST['tambah'])) {
    $nama       = trim($_POST['nama_lengkap'] ?? '');
    $nim        = trim($_POST['nim'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';
    $id_bidang  = (int)($_POST['id_bidang'] ?? 0);
    $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker  = (int)($_POST['id_proker'] ?? 0);
    $id_periode = (int)($_POST['id_periode'] ?? 0);

    if (empty($nama))        $errors[] = "Nama lengkap wajib diisi.";
    if (empty($nim))         $errors[] = "NIM wajib diisi.";
    if (empty($username))    $errors[] = "Username wajib diisi.";
    if (strlen($password)<6) $errors[] = "Password minimal 6 karakter.";
    if ($id_periode === 0)   $errors[] = "Periode wajib dipilih.";

    if (empty($errors)) {
        $cek = $conn->prepare("SELECT id_anggota FROM anggota WHERE nim=?");
        $cek->bind_param("s", $nim); $cek->execute(); $cek->store_result();
        if ($cek->num_rows > 0) $errors[] = "NIM <b>$nim</b> sudah terdaftar!";
        $cek->close();

        $cek2 = $conn->prepare("SELECT id_user FROM users WHERE username=?");
        $cek2->bind_param("s", $username); $cek2->execute(); $cek2->store_result();
        if ($cek2->num_rows > 0) $errors[] = "Username <b>$username</b> sudah dipakai.";
        $cek2->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $s1 = $conn->prepare("INSERT INTO users (username,password,role) VALUES (?,?,'Anggota')");
        $s1->bind_param("ss",$username,$hash); $s1->execute();
        $id_u = $conn->insert_id; $s1->close();

        $s2 = $conn->prepare("INSERT INTO anggota (id_user,id_bidang,id_jabatan,id_periode,nama_lengkap,nim) VALUES (?,?,?,?,?,?)");
        $s2->bind_param("iiiiss",$id_u,$id_bidang,$id_jabatan,$id_periode,$nama,$nim); $s2->execute();
        $id_a = $conn->insert_id; $s2->close();

        if ($id_proker > 0) {
            $s3 = $conn->prepare("INSERT INTO tugas_proker (id_anggota,id_proker) VALUES (?,?)");
            $s3->bind_param("ii",$id_a,$id_proker); $s3->execute(); $s3->close();
        }

        catat_log($conn, $ses_id_user, "Admin $ses_username menambahkan anggota baru: $nama (NIM: $nim)");
        tab_redirect('anggota_tampil.php', ['success' => "Data anggota $nama berhasil ditambahkan!"]);
    }
}

$bidang  = mysqli_query($conn,"SELECT * FROM bidang ORDER BY nama_bidang");
$jabatan = mysqli_query($conn,"SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn,"SELECT * FROM periode ORDER BY id_periode DESC");
$proker  = mysqli_query($conn,"SELECT p.*,COUNT(tp.id_tugas) AS terisi FROM proker p LEFT JOIN tugas_proker tp ON p.id_proker=tp.id_proker GROUP BY p.id_proker HAVING terisi<p.kuota_maksimal ORDER BY p.nama_proker");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Anggota — B-ORG</title>
  <style>
    body{margin:0;background:#f0f3f8}
    .main{max-width:640px;margin:30px auto;padding:0 20px}
    .card{background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .card-header{background:linear-gradient(135deg,#27ae60,#2ecc71);padding:24px 30px;color:white}
    .card-header h2{margin:0 0 4px;font-size:1.3rem}
    .card-header p{margin:0;opacity:.8;font-size:.85rem}
    .card-body{padding:30px}
    .sec{font-size:.74rem;font-weight:700;color:#27ae60;text-transform:uppercase;letter-spacing:1px;margin:20px 0 10px;padding-bottom:6px;border-bottom:2px solid #eafaf1}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .fg{margin-bottom:14px}
    label{display:block;font-size:.82rem;font-weight:600;color:#5f6368;margin-bottom:5px}
    .req{color:#e74c3c}
    input,select{width:100%;padding:11px 14px;border:2px solid #e8eaed;border-radius:9px;font-size:.9rem;transition:.3s;color:#2c3e50;background:#fafafa;-webkit-appearance:none}
    input:focus,select:focus{outline:none;border-color:#27ae60;background:white}
    .errors{background:#fdf0f0;border:1px solid #f5c6cb;border-radius:10px;padding:14px 18px;margin-bottom:18px;font-size:.85rem;color:#c0392b}
    .errors ul{margin:6px 0 0 16px}.errors li{margin-bottom:3px}
    .btn-row{display:flex;gap:12px;margin-top:22px}
    .btn{padding:12px 22px;border-radius:9px;font-weight:700;font-size:.9rem;cursor:pointer;border:none;text-decoration:none;transition:.3s;display:inline-flex;align-items:center;gap:6px}
    .btn-green{background:#27ae60;color:white;flex:1;justify-content:center}.btn-green:hover{background:#229954}
    .btn-outline{background:white;color:#7f8c8d;border:2px solid #e8eaed}.btn-outline:hover{border-color:#bdc3c7}
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">
  <div class="card">
    <div class="card-header">
      <h2>➕ Tambah Anggota Baru</h2>
      <p>Lengkapi formulir berikut untuk mendaftarkan anggota baru</p>
    </div>
    <div class="card-body">
      <?php if(!empty($errors)): ?>
        <div class="errors"><strong>⚠️ Terdapat kesalahan:</strong>
          <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">
        <div class="sec">📋 Biodata</div>
        <div class="row2">
          <div class="fg"><label>Nama Lengkap <span class="req">*</span></label>
            <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($_POST['nama_lengkap']??'') ?>"></div>
          <div class="fg"><label>NIM <span class="req">*</span></label>
            <input type="text" name="nim" required value="<?= htmlspecialchars($_POST['nim']??'') ?>"></div>
        </div>
        <div class="sec">🔐 Akun Login</div>
        <div class="row2">
          <div class="fg"><label>Username <span class="req">*</span></label>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username']??'') ?>"></div>
          <div class="fg"><label>Password <span class="req">*</span></label>
            <input type="password" name="password" placeholder="Min. 6 karakter" required></div>
        </div>
        <div class="sec">🏢 Organisasi</div>
        <div class="fg"><label>Periode Kepengurusan <span class="req">*</span></label>
          <select name="id_periode" required>
            <option value="">-- Pilih Periode --</option>
            <?php while($p=mysqli_fetch_assoc($periode)): ?>
              <option value="<?=$p['id_periode']?>" <?=(($_POST['id_periode']??'')==$p['id_periode'])?'selected':''?>>
                📅 <?=htmlspecialchars($p['tahun_periode'])?></option>
            <?php endwhile; ?>
          </select></div>
        <div class="row2">
          <div class="fg"><label>Bidang <span class="req">*</span></label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b=mysqli_fetch_assoc($bidang)): ?>
                <option value="<?=$b['id_bidang']?>" <?=(($_POST['id_bidang']??'')==$b['id_bidang'])?'selected':''?>>
                  <?=htmlspecialchars($b['nama_bidang'])?></option>
              <?php endwhile; ?></select></div>
          <div class="fg"><label>Jabatan <span class="req">*</span></label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j=mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?=$j['id_jabatan']?>" <?=(($_POST['id_jabatan']??'')==$j['id_jabatan'])?'selected':''?>>
                  <?=htmlspecialchars($j['nama_jabatan'])?></option>
              <?php endwhile; ?></select></div>
        </div>
        <div class="fg"><label>Program Kerja</label>
          <select name="id_proker">
            <option value="">-- Tidak Mengambil Proker --</option>
            <?php while($pk=mysqli_fetch_assoc($proker)): $sisa=$pk['kuota_maksimal']-$pk['terisi']; ?>
              <option value="<?=$pk['id_proker']?>"><?=htmlspecialchars($pk['nama_proker'])?> (Sisa: <?=$sisa?>)</option>
            <?php endwhile; ?></select></div>
        <div class="btn-row">
          <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn btn-outline">← Batal</a>
          <button type="submit" name="tambah" class="btn btn-green">💾 Simpan Anggota</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
