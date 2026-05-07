<?php
<<<<<<< HEAD
session_start(); 
include 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] == 'viewer') {
    header("Location: login.php?pesan=restricted");
    exit();
}

if (isset($_POST['tambah'])) {
    $nim           = $_POST['nim'];
    $nama          = $_POST['nama_lengkap'];
    $id_jabatan    = $_POST['id_jabatan'];
    $id_bidang     = $_POST['id_bidang'];
    $id_periode    = $_POST['id_periode'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $email         = $_POST['email'];
    $no_hp         = $_POST['no_hp'];
    $prodi         = $_POST['prodi'];
    $fakultas      = $_POST['fakultas'];
    $angkatan      = $_POST['angkatan'];

    $check = $conn->prepare("SELECT nim FROM anggota WHERE nim = ?");
    $check->bind_param("s", $nim);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Error: NIM $nim sudah terdaftar!'); window.history.back();</script>";
        exit();
    } else {
        $sql = "INSERT INTO anggota 
                (nim, nama_lengkap, jenis_kelamin, tanggal_lahir, email, no_hp, prodi, fakultas, angkatan, id_jabatan, id_bidang, id_periode) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssiiii", 
            $nim, $nama, $jenis_kelamin, $tanggal_lahir, $email, 
            $no_hp, $prodi, $fakultas, $angkatan, $id_jabatan, 
            $id_bidang, $id_periode
        );
        
        if ($stmt->execute()) {
            header("Location: anggota_tampil.php?status=sukses");
            exit(); 
        }
    }
}
=======
include '../config/koneksi.php';
include '../includes/log_helper.php';
require_admin('../');

$errors = [];

if (isset($_POST['tambah'])) {
    // Collect all attributes
    $nama          = trim($_POST['nama_lengkap'] ?? '');
    $nim           = trim($_POST['nim'] ?? '');
    $jk            = $_POST['jenis_kelamin'] ?? 'L';
    $tgl_lahir     = $_POST['tanggal_lahir'] ?? null;
    $email         = trim($_POST['email'] ?? '');
    $no_hp         = trim($_POST['no_hp'] ?? '');
    $prodi         = trim($_POST['prodi'] ?? '');
    $fakultas      = trim($_POST['fakultas'] ?? '');
    $angkatan      = (int)($_POST['angkatan'] ?? 0);
    
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $id_bidang     = (int)($_POST['id_bidang'] ?? 0);
    $id_jabatan    = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker     = (int)($_POST['id_proker'] ?? 0);
    $id_periode    = (int)($_POST['id_periode'] ?? 0);

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
        
        // 1. Insert ke users (default role viewer)
        $s1 = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'viewer')");
        $s1->bind_param("ss", $username, $hash);
        $s1->execute();
        $id_u = $conn->insert_id;
        $s1->close();

        // 2. Insert ke anggota (Semua atribut)
        $s2 = $conn->prepare("
            INSERT INTO anggota (
                nim, nama_lengkap, jenis_kelamin, tanggal_lahir, 
                email, no_hp, prodi, fakultas, angkatan, 
                id_bidang, id_jabatan, id_periode
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $s2->bind_param("ssssssssiiii", 
            $nim, $nama, $jk, $tgl_lahir, 
            $email, $no_hp, $prodi, $fakultas, $angkatan, 
            $id_bidang, $id_jabatan, $id_periode
        );
        $s2->execute();
        $id_a = $conn->insert_id;
        $s2->close();

        // 3. Update users untuk menghubungkan ke anggota
        $s3 = $conn->prepare("UPDATE users SET id_anggota = ? WHERE id_user = ?");
        $s3->bind_param("ii", $id_a, $id_u);
        $s3->execute();
        $s3->close();

        // 4. Insert ke anggota_proker jika ada
        if ($id_proker > 0) {
            $s4 = $conn->prepare("INSERT INTO anggota_proker (id_anggota, id_proker, peran) VALUES (?, ?, 'Panitia')");
            $s4->bind_param("ii", $id_a, $id_proker);
            $s4->execute();
            $s4->close();
        }

        catat_log($conn, $ses_id_user, "Admin $ses_username menambahkan anggota baru (Full Data): $nama (NIM: $nim)");
        tab_redirect('anggota_tampil.php', ['success' => "Data anggota $nama berhasil ditambahkan secara lengkap!"]);
    }
}

$bidang  = mysqli_query($conn, "SELECT * FROM bidang ORDER BY nama_bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$proker  = mysqli_query($conn, "SELECT p.*, COUNT(ap.id_anggota_proker) AS terisi FROM proker p LEFT JOIN anggota_proker ap ON p.id_proker = ap.id_proker GROUP BY p.id_proker ORDER BY p.nama_proker");
>>>>>>> bismillah-acc
?>
<!DOCTYPE html>
<html lang="id">
<head>
<<<<<<< HEAD
    <meta charset="UTF-8">
    <title>Tambah Anggota - Admin Panel</title>
</head>
<body style="font-family: sans-serif; background: #f8f9fa; margin: 0;">
    <?php include '../includes/navbar.php'; ?>
    
    <div style="max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #27ae60; margin-top: 0;">Tambah Anggota Baru</h2>
        <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 20px;">Silakan isi formulir di bawah untuk mendaftarkan anggota baru ke sistem.</p>
        
        <form method="POST">
            <label style="font-weight: bold; font-size: 0.9rem;">Biodata Dasar</label>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            <input type="text" name="nim" placeholder="NIM" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            
            <label style="font-weight: bold; font-size: 0.9rem;">Akun Login</label>
            <input type="text" name="username" placeholder="Username Login" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            
            <label style="font-weight: bold; font-size: 0.9rem;">Struktur Organisasi</label>
            <select name="id_bidang" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Bidang --</option>
                <?php
                $bid = mysqli_query($conn, "SELECT * FROM bidang");
                while($b = mysqli_fetch_assoc($bid)) echo "<option value='".$b['id_bidang']."'>".$b['nama_bidang']."</option>";
                ?>
            </select>

            <select name="id_jabatan" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Jabatan --</option>
                <?php
                $jab = mysqli_query($conn, "SELECT * FROM jabatan");
                while($j = mysqli_fetch_assoc($jab)) echo "<option value='".$j['id_jabatan']."'>".$j['nama_jabatan']."</option>";
                ?>
            </select>

            <label style="font-weight: bold; font-size: 0.9rem;">Program Kerja (Kuota Sisa)</label>
            <select name="id_proker" style="width: 100%; padding: 12px; margin: 8px 0 25px 0; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Proker (Optional) --</option>
                <?php
                // Logic Kuota: Hanya proker dengan < 2 anggota yang muncul
                $pro = mysqli_query($conn, "SELECT p.*, COUNT(tp.id_tugas) as t 
                                            FROM proker p 
                                            LEFT JOIN tugas_proker tp ON p.id_proker=tp.id_proker 
                                            GROUP BY p.id_proker HAVING t < 2");
                while($p = mysqli_fetch_assoc($pro)) {
                    $sisa = 2 - $p['t'];
                    echo "<option value='".$p['id_proker']."'>".$p['nama_proker']." (Sisa: $sisa)</option>";
                }
                ?>
            </select>

            <button type="submit" name="tambah" style="width: 100%; padding: 14px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; font-weight: bold;">SIMPAN DATA ANGGOTA</button>
            <a href="anggota_tampil.php" style="display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; font-size: 0.9rem;">Batal & Kembali</a>
        </form>
    </div>
=======
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Anggota — B-ORG</title>
  <link rel="stylesheet" href="anggota_tambah.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">
  <div class="card">
    <div class="card-header">
      <h2>➕ Tambah Anggota Baru (Full)</h2>
      <p>Lengkapi formulir berikut untuk mendaftarkan anggota baru dengan data lengkap</p>
    </div>
    <div class="card-body">
      <?php if(!empty($errors)): ?>
        <div class="errors"><strong>⚠️ Terdapat kesalahan:</strong>
          <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
      <?php endif; ?>
      
      <form method="POST">
        <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">
        
        <div class="sec">📋 Biodata Pribadi</div>
        <div class="row2">
          <div class="fg"><label>Nama Lengkap <span class="req">*</span></label>
            <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($_POST['nama_lengkap']??'') ?>"></div>
          <div class="fg"><label>NIM <span class="req">*</span></label>
            <input type="text" name="nim" required value="<?= htmlspecialchars($_POST['nim']??'') ?>"></div>
        </div>

        <div class="row2">
          <div class="fg"><label>Jenis Kelamin</label>
            <select name="jenis_kelamin">
              <option value="L" <?= (($_POST['jenis_kelamin']??'') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= (($_POST['jenis_kelamin']??'') == 'P') ? 'selected' : '' ?>>Perempuan</option>
            </select></div>
          <div class="fg"><label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($_POST['tanggal_lahir']??'') ?>"></div>
        </div>

        <div class="row2">
          <div class="fg"><label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email']??'') ?>"></div>
          <div class="fg"><label>No. HP</label>
            <input type="text" name="no_hp" value="<?= htmlspecialchars($_POST['no_hp']??'') ?>"></div>
        </div>

        <div class="sec">🎓 Informasi Akademik</div>
        <div class="row2">
          <div class="fg"><label>Program Studi</label>
            <input type="text" name="prodi" value="<?= htmlspecialchars($_POST['prodi']??'') ?>"></div>
          <div class="fg"><label>Fakultas</label>
            <input type="text" name="fakultas" value="<?= htmlspecialchars($_POST['fakultas']??'') ?>"></div>
        </div>
        <div class="fg"><label>Angkatan</label>
          <input type="number" name="angkatan" value="<?= htmlspecialchars($_POST['angkatan']??date('Y')) ?>"></div>

        <div class="sec">🔐 Akun Login</div>
        <div class="row2">
          <div class="fg"><label>Username <span class="req">*</span></label>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username']??'') ?>"></div>
          <div class="fg"><label>Password <span class="req">*</span></label>
            <input type="password" name="password" placeholder="Min. 6 karakter" required></div>
        </div>

        <div class="sec">🏢 Informasi Organisasi</div>
        <div class="row2">
          <div class="fg"><label>Periode Kepengurusan <span class="req">*</span></label>
            <select name="id_periode" required>
              <option value="">-- Pilih Periode --</option>
              <?php while($p=mysqli_fetch_assoc($periode)): ?>
                <option value="<?=$p['id_periode']?>" <?=(($_POST['id_periode']??'')==$p['id_periode'])?'selected':''?>>
                  📅 <?=htmlspecialchars($p['label'])?></option>
              <?php endwhile; ?>
            </select></div>
          <div class="fg"><label>Bidang <span class="req">*</span></label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b=mysqli_fetch_assoc($bidang)): ?>
                <option value="<?=$b['id_bidang']?>" <?=(($_POST['id_bidang']??'')==$b['id_bidang'])?'selected':''?>>
                  <?=htmlspecialchars($b['nama_bidang'])?></option>
              <?php endwhile; ?></select></div>
        </div>

        <div class="row2">
          <div class="fg"><label>Jabatan <span class="req">*</span></label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j=mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?=$j['id_jabatan']?>" <?=(($_POST['id_jabatan']??'')==$j['id_jabatan'])?'selected':''?>>
                  <?=htmlspecialchars($j['nama_jabatan'])?></option>
              <?php endwhile; ?></select></div>
          <div class="fg"><label>Program Kerja</label>
            <select name="id_proker">
              <option value="">-- Tidak Mengambil Proker --</option>
              <?php while($pk=mysqli_fetch_assoc($proker)): ?>
                <option value="<?=$pk['id_proker']?>" <?= (($_POST['id_proker'] ?? '') == $pk['id_proker']) ? 'selected' : '' ?>>
                  <?=htmlspecialchars($pk['nama_proker'])?>
                </option>
              <?php endwhile; ?></select></div>
        </div>

        <div class="btn-row">
          <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn btn-outline">← Batal</a>
          <button type="submit" name="tambah" class="btn btn-green">💾 Simpan Anggota</button>
        </div>
      </form>
    </div>
  </div>
</div>
>>>>>>> bismillah-acc
</body>
</html>
