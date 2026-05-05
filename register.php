<?php
// register.php — Pendaftaran anggota baru (tab-aware)
include 'config/koneksi.php';
include 'includes/log_helper.php';

// Kalau sudah login di tab ini, tolak akses register
if ($ses_valid) {
    tab_redirect('pages/anggota_tampil.php');
}

$errors = [];

if (isset($_POST['register'])) {
    $new_tsid   = trim($_POST['new_tsid'] ?? '');
    $nama       = trim($_POST['nama_lengkap'] ?? '');
    $nim        = trim($_POST['nim'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';
    $id_bidang  = (int)($_POST['id_bidang']  ?? 0);
    $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker  = (int)($_POST['id_proker']  ?? 0);
    $id_periode = (int)($_POST['id_periode'] ?? 0);

    if (empty($nama))        $errors[] = "Nama lengkap tidak boleh kosong.";
    if (empty($nim))         $errors[] = "NIM tidak boleh kosong.";
    if (empty($username))    $errors[] = "Username tidak boleh kosong.";
    if (strlen($password)<6) $errors[] = "Password minimal 6 karakter.";
    if ($id_periode === 0)   $errors[] = "Periode wajib dipilih.";

    if (empty($errors)) {
        $cek = $conn->prepare("SELECT id_anggota FROM anggota WHERE nim = ?");
        $cek->bind_param("s", $nim); $cek->execute(); $cek->store_result();
        if ($cek->num_rows > 0) $errors[] = "NIM <strong>$nim</strong> sudah terdaftar!";
        $cek->close();

        $cek2 = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $cek2->bind_param("s", $username); $cek2->execute(); $cek2->store_result();
        if ($cek2->num_rows > 0) $errors[] = "Username <strong>$username</strong> sudah dipakai.";
        $cek2->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $s1 = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'Anggota')");
        $s1->bind_param("ss", $username, $hash); $s1->execute();
        $id_u = $conn->insert_id; $s1->close();

        $s2 = $conn->prepare("INSERT INTO anggota (id_user,id_bidang,id_jabatan,id_periode,nama_lengkap,nim) VALUES (?,?,?,?,?,?)");
        $s2->bind_param("iiiiss", $id_u, $id_bidang, $id_jabatan, $id_periode, $nama, $nim);
        $s2->execute(); $id_a = $conn->insert_id; $s2->close();

        if ($id_proker > 0) {
            $s3 = $conn->prepare("INSERT INTO tugas_proker (id_anggota, id_proker) VALUES (?,?)");
            $s3->bind_param("ii", $id_a, $id_proker); $s3->execute(); $s3->close();
        }

        catat_log($conn, $id_u, "Anggota baru mendaftar: $nama (NIM: $nim) via halaman register");

        // Buat session langsung untuk tab ini
        if (empty($new_tsid)) {
            $new_tsid = bin2hex(random_bytes(16));
        }
        create_tab_session($new_tsid, $id_u, $username, 'Anggota');

        // Set global tsid agar tab_redirect() bisa pakai
        global $tsid;
        $tsid = $new_tsid;

        tab_redirect('pages/anggota_tampil.php', [
            'success' => "Registrasi berhasil! Selamat datang, $nama 🎉"
        ]);
    }
}

$bidang  = mysqli_query($conn, "SELECT * FROM bidang  ORDER BY nama_bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$proker  = mysqli_query($conn,
    "SELECT p.*, COUNT(tp.id_tugas) AS terisi FROM proker p
     LEFT JOIN tugas_proker tp ON p.id_proker = tp.id_proker
     GROUP BY p.id_proker HAVING terisi < p.kuota_maksimal ORDER BY p.nama_proker");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Anggota — B-ORG</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body{background:linear-gradient(135deg,#1a252f,#2c3e50);min-height:100vh;padding:30px 20px;display:flex;justify-content:center;align-items:flex-start}
    .card{background:white;width:100%;max-width:520px;border-radius:16px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.3)}
    .card-header{background:linear-gradient(135deg,#27ae60,#2ecc71);padding:28px 30px;color:white;text-align:center}
    .card-header h2{font-size:1.4rem;font-weight:700;margin-bottom:4px}
    .card-header p{opacity:.8;font-size:.85rem;margin:0}
    .card-body{padding:28px 30px}
    .sec{font-size:.74rem;font-weight:700;color:#27ae60;text-transform:uppercase;letter-spacing:1px;margin:18px 0 10px;padding-bottom:6px;border-bottom:2px solid #eafaf1}
    .fg{margin-bottom:13px}
    label{display:block;font-size:.82rem;font-weight:600;color:#5f6368;margin-bottom:5px}
    input,select{width:100%;padding:11px 14px;border:2px solid #e8eaed;border-radius:9px;font-size:.9rem;transition:.3s;color:#2c3e50;background:#fafafa;-webkit-appearance:none}
    input:focus,select:focus{outline:none;border-color:#27ae60;background:white}
    .btn{width:100%;padding:13px;background:linear-gradient(135deg,#27ae60,#2ecc71);color:white;border:none;border-radius:9px;font-size:1rem;font-weight:700;cursor:pointer;transition:.3s;margin-top:8px}
    .btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(39,174,96,.4)}
    .errors{background:#fdf0f0;border:1px solid #f5c6cb;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.85rem;color:#c0392b}
    .errors ul{margin:6px 0 0 16px}.errors li{margin-bottom:3px}
    .link{text-align:center;margin-top:14px;font-size:.85rem;color:#7f8c8d}
    .link a{color:#27ae60;text-decoration:none;font-weight:600}
    .loader{display:none;width:18px;height:18px;border:2px solid rgba(255,255,255,.4);border-top:2px solid white;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto}
    @keyframes spin{to{transform:rotate(360deg)}}
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <h2>📝 Daftar Anggota Baru</h2>
    <p>Isi formulir untuk bergabung ke B-ORG System</p>
  </div>
  <div class="card-body">
    <?php if(!empty($errors)): ?>
      <div class="errors"><strong>Terdapat kesalahan:</strong>
        <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" id="regForm">
      <!-- tsid di-generate JS sebelum submit, bukan dari cookie -->
      <input type="hidden" name="new_tsid" id="newTsid">

      <div class="sec">📋 Biodata</div>
      <div class="fg"><label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required placeholder="Nama sesuai KTM"
               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>"></div>
      <div class="fg"><label>NIM</label>
        <input type="text" name="nim" required placeholder="Nomor Induk Mahasiswa"
               value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>"></div>

      <div class="sec">🔐 Akun Login</div>
      <div class="fg"><label>Username</label>
        <input type="text" name="username" required placeholder="Huruf kecil, tanpa spasi"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"></div>
      <div class="fg"><label>Password</label>
        <input type="password" name="password" required placeholder="Minimal 6 karakter"></div>

      <div class="sec">🏢 Struktur Organisasi</div>
      <div class="fg"><label>Periode Kepengurusan</label>
        <select name="id_periode" required>
          <option value="">-- Pilih Periode --</option>
          <?php while($p=mysqli_fetch_assoc($periode)): ?>
            <option value="<?=$p['id_periode']?>" <?=(($_POST['id_periode']??'')==$p['id_periode'])?'selected':''?>>
              📅 <?=htmlspecialchars($p['tahun_periode'])?></option>
          <?php endwhile; ?>
        </select></div>
      <div class="fg"><label>Bidang</label>
        <select name="id_bidang" required>
          <option value="">-- Pilih Bidang --</option>
          <?php while($b=mysqli_fetch_assoc($bidang)): ?>
            <option value="<?=$b['id_bidang']?>" <?=(($_POST['id_bidang']??'')==$b['id_bidang'])?'selected':''?>>
              <?=htmlspecialchars($b['nama_bidang'])?></option>
          <?php endwhile; ?>
        </select></div>
      <div class="fg"><label>Jabatan</label>
        <select name="id_jabatan" required>
          <option value="">-- Pilih Jabatan --</option>
          <?php while($j=mysqli_fetch_assoc($jabatan)): ?>
            <option value="<?=$j['id_jabatan']?>" <?=(($_POST['id_jabatan']??'')==$j['id_jabatan'])?'selected':''?>>
              <?=htmlspecialchars($j['nama_jabatan'])?></option>
          <?php endwhile; ?>
        </select></div>
      <div class="fg"><label>Program Kerja (Opsional)</label>
        <select name="id_proker">
          <option value="">-- Tidak Mengambil Proker --</option>
          <?php while($pk=mysqli_fetch_assoc($proker)): $sisa=$pk['kuota_maksimal']-$pk['terisi']; ?>
            <option value="<?=$pk['id_proker']?>"><?=htmlspecialchars($pk['nama_proker'])?> (Sisa: <?=$sisa?>)</option>
          <?php endwhile; ?>
        </select></div>

      <button type="submit" name="register" class="btn" id="btnReg">
        <span id="btnText">DAFTAR SEKARANG 🚀</span>
        <div class="loader" id="loader"></div>
      </button>
    </form>
    <div class="link">Sudah punya akun? <a href="login.php">Login di sini</a></div>
  </div>
</div>
<script>
function generateTsid() {
    const arr = new Uint8Array(16);
    crypto.getRandomValues(arr);
    return Array.from(arr).map(b => b.toString(16).padStart(2,'0')).join('');
}
document.getElementById('regForm').addEventListener('submit', function() {
    const tsid = generateTsid();
    sessionStorage.setItem('b_org_tsid', tsid);
    document.getElementById('newTsid').value = tsid;
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('loader').style.display  = 'block';
});
</script>
</body>
</html>
