<?php
// register.php — Pendaftaran anggota baru (tab-aware)
include 'config/koneksi.php';
include 'includes/log_helper.php';

// Kalau sudah login di tab ini, tolak akses register
if ($ses_valid) {
    tab_redirect('pages/anggota_tampil.php');
}

$errors = [];
$success = '';

/* AUTO COUNT DATA */
$jml_bidang  = 0;
$jml_anggota = 0;

// status aktif otomatis dari tahun sekarang
$periode_aktif_label = date('Y');

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


/* PROSES REGISTER*/
if (isset($_POST['register'])) {
    $new_tsid   = trim($_POST['new_tsid'] ?? '');
    $nama       = trim($_POST['nama_lengkap'] ?? '');
    $nim        = trim($_POST['nim'] ?? '');
    $jk         = trim($_POST['jenis_kelamin'] ?? '');
    $tgl_lahir  = trim($_POST['tanggal_lahir'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $no_hp      = trim($_POST['no_hp'] ?? '');
    $prodi      = trim($_POST['prodi'] ?? '');
    $fakultas   = trim($_POST['fakultas'] ?? '');

    $username   = trim($_POST['username'] ?? '');
    $password   = $_POST['password'] ?? '';
    $id_bidang  = (int)($_POST['id_bidang']  ?? 0);
    $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker  = (int)($_POST['id_proker']  ?? 0);
    $id_periode = (int)($_POST['id_periode'] ?? 0);

    if (empty($nama))        $errors[] = "Nama lengkap tidak boleh kosong.";
    if (empty($nim))         $errors[] = "NIM tidak boleh kosong.";
    if (empty($jk))          $errors[] = "Jenis kelamin wajib dipilih.";
    if (empty($username))    $errors[] = "Username tidak boleh kosong.";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
    if ($id_periode === 0)   $errors[] = "Periode wajib dipilih.";
    if ($id_bidang === 0)    $errors[] = "Bidang wajib dipilih.";
    if ($id_jabatan === 0)   $errors[] = "Jabatan wajib dipilih.";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if (empty($errors)) {
        $cek = $conn->prepare("SELECT id_anggota FROM anggota WHERE nim = ?");
        $cek->bind_param("s", $nim);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) $errors[] = "NIM <strong>$nim</strong> sudah terdaftar!";
        $cek->close();

        $cek2 = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $cek2->bind_param("s", $username);
        $cek2->execute();
        $cek2->store_result();
        if ($cek2->num_rows > 0) $errors[] = "Username <strong>$username</strong> sudah dipakai.";
        $cek2->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Karena di database_final.sql role hanya admin/viewer
        $role = 'viewer';

        // Insert ke users
        $s1 = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $s1->bind_param("sss", $username, $hash, $role);
        $s1->execute();
        $id_u = $conn->insert_id;
        $s1->close();

        // Handle tanggal lahir (boleh kosong)
        $tanggal_lahir = (!empty($tgl_lahir)) ? $tgl_lahir : NULL;

        // Insert anggota
        $s2 = $conn->prepare("INSERT INTO anggota 
            (nim, nama_lengkap, jenis_kelamin, tanggal_lahir, email, no_hp, prodi, fakultas, id_jabatan, id_bidang, id_periode) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?)"
        );

        $s2->bind_param(
            "ssssssssiii",
            $nim,
            $nama,
            $jk,
            $tanggal_lahir,
            $email,
            $no_hp,
            $prodi,
            $fakultas,
            $id_jabatan,
            $id_bidang,
            $id_periode
        );

        $s2->execute();
        $id_a = $conn->insert_id;
        $s2->close();

        // Update users agar terhubung ke anggota
        $sUser = $conn->prepare("UPDATE users SET id_anggota = ? WHERE id_user = ?");
        $sUser->bind_param("ii", $id_a, $id_u);
        $sUser->execute();
        $sUser->close();

        // Jika pilih proker, masukkan ke anggota_proker
        if ($id_proker > 0) {
            $s3 = $conn->prepare("INSERT INTO anggota_proker (id_anggota, id_proker, peran) VALUES (?,?, 'Panitia')");
            $s3->bind_param("ii", $id_a, $id_proker);
            $s3->execute();
            $s3->close();
        }

        catat_log($conn, $id_u, "Anggota baru mendaftar: $nama (NIM: $nim) via halaman register");

        // Pesan sukses (TIDAK REDIRECT)
        $success = "Registrasi berhasil! Silakan login menggunakan akun kamu 🎉";

        // kosongkan form
        $_POST = [];

        // refresh counter anggota
        $qAnggota = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggota WHERE deleted_at IS NULL");
        if ($qAnggota) {
            $row = mysqli_fetch_assoc($qAnggota);
            $jml_anggota = $row['total'] ?? 0;
        }
    }
}

$bidang  = mysqli_query($conn, "SELECT * FROM bidang  ORDER BY nama_bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");

$proker  = mysqli_query($conn,
    "SELECT p.*, COUNT(ap.id_anggota_proker) AS terisi 
     FROM proker p
     LEFT JOIN anggota_proker ap ON p.id_proker = ap.id_proker
     GROUP BY p.id_proker
     ORDER BY p.nama_proker"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Anggota — B-ORG</title>

  <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<div class="split-container">

  <!-- LEFT SIDE -->
  <div class="left-panel">

    <div class="status-badge">
      <span class="dot"></span>
      Status Aktif — <?= htmlspecialchars($periode_aktif_label) ?>
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
      <p>
        Pantau data organisasi secara real-time, cepat dan akurat.
      </p>
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

  <!-- RIGHT SIDE -->
  <div class="right-panel">

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

        <?php if(!empty($success)): ?>
          <div class="success">
            ✅ <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <form method="POST" id="regForm">
          <input type="hidden" name="new_tsid" id="newTsid">

          <div class="sec">📋 Biodata</div>

          <div class="fg">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" required placeholder="Nama sesuai KTM"
                   value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>NIM</label>
            <input type="text" name="nim" required placeholder="Nomor Induk Mahasiswa"
                   value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="L" <?= (($_POST['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= (($_POST['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>

          <div class="fg">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir"
                   value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="contoh@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>No HP</label>
            <input type="text" name="no_hp" placeholder="08xxxxxxxxxx"
                   value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>Prodi</label>
            <input type="text" name="prodi" placeholder="Contoh: Teknik Informatika"
                   value="<?= htmlspecialchars($_POST['prodi'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>Fakultas</label>
            <input type="text" name="fakultas" placeholder="Contoh: Teknik"
                   value="<?= htmlspecialchars($_POST['fakultas'] ?? '') ?>">
          </div>

          <div class="sec">🔐 Akun Login</div>

          <div class="fg">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Huruf kecil, tanpa spasi"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>

          <div class="fg">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Minimal 6 karakter">
          </div>

          <div class="sec">🏢 Struktur Organisasi</div>

          <div class="fg">
            <label>Periode Kepengurusan</label>
            <select name="id_periode" required>
              <option value="">-- Pilih Periode --</option>
              <?php while($p=mysqli_fetch_assoc($periode)): ?>
                <option value="<?=$p['id_periode']?>" <?=(($_POST['id_periode']??'')==$p['id_periode'])?'selected':''?>>
                  📅 <?=htmlspecialchars($p['label'])?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="fg">
            <label>Bidang</label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b=mysqli_fetch_assoc($bidang)): ?>
                <option value="<?=$b['id_bidang']?>" <?=(($_POST['id_bidang']??'')==$b['id_bidang'])?'selected':''?>>
                  <?=htmlspecialchars($b['nama_bidang'])?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="fg">
            <label>Jabatan</label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j=mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?=$j['id_jabatan']?>" <?=(($_POST['id_jabatan']??'')==$j['id_jabatan'])?'selected':''?>>
                  <?=htmlspecialchars($j['nama_jabatan'])?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="fg">
            <label>Program Kerja (Opsional)</label>
            <select name="id_proker">
              <option value="">-- Tidak Mengambil Proker --</option>
              <?php while($pk=mysqli_fetch_assoc($proker)): ?>
                <option value="<?=$pk['id_proker']?>" <?= (($_POST['id_proker'] ?? '') == $pk['id_proker']) ? 'selected' : '' ?>>
                  <?=htmlspecialchars($pk['nama_proker'])?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <button type="submit" name="register" class="btn" id="btnReg">
            <span id="btnText">DAFTAR SEKARANG 🚀</span>
            <div class="loader" id="loader"></div>
          </button>
        </form>

        <div class="link">Sudah punya akun? <a href="login.php">Login di sini</a></div>
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