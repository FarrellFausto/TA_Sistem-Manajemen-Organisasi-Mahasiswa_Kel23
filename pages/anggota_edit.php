<?php
<<<<<<< HEAD
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] == 'viewer') {
    header("Location: login.php?pesan=restricted");
    exit();
}

if (isset($_POST['update'])) {
    $id_anggota    = $_POST['id_anggota'];
    $nim           = $_POST['nim'];
    $nama          = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $email         = $_POST['email'];
    $no_hp         = $_POST['no_hp'];
    $prodi         = $_POST['prodi'];
    $fakultas      = $_POST['fakultas'];
    $angkatan      = $_POST['angkatan'];
    $id_jabatan    = $_POST['id_jabatan'];
    $id_bidang     = $_POST['id_bidang'];
    $id_periode    = $_POST['id_periode'];
    $status        = $_POST['status_anggota'];

    $sql = "UPDATE anggota SET 
            nim=?, nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, 
            email=?, no_hp=?, prodi=?, fakultas=?, angkatan=?, 
            id_jabatan=?, id_bidang=?, id_periode=?, status_anggota=? 
            WHERE id_anggota=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssiiiisi", 
        $nim, $nama, $jenis_kelamin, $tanggal_lahir, 
        $email, $no_hp, $prodi, $fakultas, $angkatan, 
        $id_jabatan, $id_bidang, $id_periode, $status, 
        $id_anggota
    );

    if ($stmt->execute()) {
        header("Location: anggota_tampil.php?status=updated");
        exit();
    } else {
        echo "Gagal memperbarui data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Anggota</title></head>
<body style="font-family: sans-serif; background: #f8f9fa; margin: 0;">
    <?php include '../includes/navbar.php'; ?>
    <div style="max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50; text-align: center;">Edit Data Anggota</h2>
        <form method="POST">
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_lengkap" value="<?= $d['nama_lengkap'] ?>" required style="width: 100%; padding: 10px; margin: 10px 0;"><br>
            
            <label>Pilih Bidang:</label><br>
            <select name="id_bidang" style="width: 100%; padding: 10px; margin: 10px 0;">
                <?php
                $bidang = mysqli_query($conn, "SELECT * FROM bidang");
                while($b = mysqli_fetch_assoc($bidang)) {
                    $sel = ($b['id_bidang'] == $d['id_bidang']) ? 'selected' : '';
                    echo "<option value='".$b['id_bidang']."' $sel>".$b['nama_bidang']."</option>";
                }
                ?>
            </select><br>

            <label>Pilih Jabatan:</label><br>
            <select name="id_jabatan" style="width: 100%; padding: 10px; margin: 10px 0;">
                <?php
                $jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
                while($j = mysqli_fetch_assoc($jabatan)) {
                    $sel = ($j['id_jabatan'] == $d['id_jabatan']) ? 'selected' : '';
                    echo "<option value='".$j['id_jabatan']."' $sel>".$j['nama_jabatan']."</option>";
                }
                ?>
            </select><br>

            <label>Pilih Proker (Slot Max 2):</label><br>
            <select name="id_proker" style="width: 100%; padding: 10px; margin: 10px 0;">
                <option value="">-- Tanpa Proker --</option>
                <?php
                $q_kuota = "SELECT p.*, COUNT(tp.id_tugas) as terisi FROM proker p 
                            LEFT JOIN tugas_proker tp ON p.id_proker = tp.id_proker 
                            GROUP BY p.id_proker HAVING terisi < 2 OR p.id_proker = '".$d['id_proker']."'";
                $proker = mysqli_query($conn, $q_kuota);
                while($p = mysqli_fetch_assoc($proker)) {
                    $sel = ($p['id_proker'] == $d['id_proker']) ? 'selected' : '';
                    echo "<option value='".$p['id_proker']."' $sel>".$p['nama_proker']."</option>";
                }
                ?>
            </select><br><br>

            <button type="submit" name="update" style="width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">SIMPAN PERUBAHAN</button>
            <a href="anggota_tampil.php" style="display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none;">Batal</a>
        </form>
    </div>
=======
include '../config/koneksi.php';
include '../includes/log_helper.php';
require_admin('../');

$id_anggota = (int)($_GET['id'] ?? 0);
if ($id_anggota <= 0) {
    tab_redirect('anggota_tampil.php', ['error' => 'ID Anggota tidak valid.']);
}

// Fetch data anggota dan user terkait
$stmt = $conn->prepare("
    SELECT a.*, u.id_user, u.username 
    FROM anggota a 
    LEFT JOIN users u ON a.id_anggota = u.id_anggota 
    WHERE a.id_anggota = ?
");
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$data_awal = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data_awal) {
    tab_redirect('anggota_tampil.php', ['error' => 'Data anggota tidak ditemukan.']);
}

// Fetch proker yang sedang diambil
$q_proker = mysqli_query($conn, "SELECT id_proker FROM anggota_proker WHERE id_anggota = $id_anggota LIMIT 1");
$current_proker = mysqli_fetch_assoc($q_proker)['id_proker'] ?? 0;

$errors = [];

if (isset($_POST['edit'])) {
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
    $status        = $_POST['status_anggota'] ?? 'aktif';
    
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $id_bidang     = (int)($_POST['id_bidang'] ?? 0);
    $id_jabatan    = (int)($_POST['id_jabatan'] ?? 0);
    $id_proker     = (int)($_POST['id_proker'] ?? 0);
    $id_periode    = (int)($_POST['id_periode'] ?? 0);

    if (empty($nama))      $errors[] = "Nama lengkap wajib diisi.";
    if (empty($nim))       $errors[] = "NIM wajib diisi.";
    if (empty($username))  $errors[] = "Username wajib diisi.";
    if ($id_periode === 0) $errors[] = "Periode wajib dipilih.";

    if (empty($errors)) {
        // Cek NIM unik (selain diri sendiri)
        $cek = $conn->prepare("SELECT id_anggota FROM anggota WHERE nim=? AND id_anggota != ?");
        $cek->bind_param("si", $nim, $id_anggota); $cek->execute(); $cek->store_result();
        if ($cek->num_rows > 0) $errors[] = "NIM <b>$nim</b> sudah digunakan anggota lain!";
        $cek->close();

        // Cek Username unik (selain diri sendiri)
        $id_user = (int)$data_awal['id_user'];
        $cek2 = $conn->prepare("SELECT id_user FROM users WHERE username=? AND id_user != ?");
        $cek2->bind_param("si", $username, $id_user); $cek2->execute(); $cek2->store_result();
        if ($cek2->num_rows > 0) $errors[] = "Username <b>$username</b> sudah digunakan akun lain!";
        $cek2->close();
    }

    if (empty($errors)) {
        // 1. Update anggota (Semua atribut)
        $s1 = $conn->prepare("
            UPDATE anggota SET 
                nim=?, nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, 
                email=?, no_hp=?, prodi=?, fakultas=?, angkatan=?, 
                id_bidang=?, id_jabatan=?, id_periode=?, status_anggota=? 
            WHERE id_anggota=?
        ");
        $s1->bind_param("ssssssssiiiisi", 
            $nim, $nama, $jk, $tgl_lahir, 
            $email, $no_hp, $prodi, $fakultas, $angkatan, 
            $id_bidang, $id_jabatan, $id_periode, $status, 
            $id_anggota
        );
        $s1->execute();
        $s1->close();

        // 2. Update users (username & password if not empty)
        if ($id_user > 0) {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $s2 = $conn->prepare("UPDATE users SET username=?, password=? WHERE id_user=?");
                $s2->bind_param("ssi", $username, $hash, $id_user);
            } else {
                $s2 = $conn->prepare("UPDATE users SET username=? WHERE id_user=?");
                $s2->bind_param("si", $username, $id_user);
            }
            $s2->execute();
            $s2->close();
        } else {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sNew = $conn->prepare("INSERT INTO users (username, password, role, id_anggota) VALUES (?, ?, 'viewer', ?)");
                $sNew->bind_param("ssi", $username, $hash, $id_anggota);
                $sNew->execute();
                $sNew->close();
            }
        }

        // 3. Update proker
        mysqli_query($conn, "DELETE FROM anggota_proker WHERE id_anggota = $id_anggota");
        if ($id_proker > 0) {
            $s3 = $conn->prepare("INSERT INTO anggota_proker (id_anggota, id_proker, peran) VALUES (?, ?, 'Panitia')");
            $s3->bind_param("ii", $id_anggota, $id_proker);
            $s3->execute();
            $s3->close();
        }

        catat_log($conn, $ses_id_user, "Admin $ses_username mengubah data anggota (Full Update): $nama (NIM: $nim)");
        tab_redirect('anggota_tampil.php', ['success' => "Data anggota $nama berhasil diperbarui secara lengkap!"]);
    }
}

$bidang  = mysqli_query($conn, "SELECT * FROM bidang ORDER BY nama_bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
$proker  = mysqli_query($conn, "SELECT p.*, COUNT(ap.id_anggota_proker) AS terisi FROM proker p LEFT JOIN anggota_proker ap ON p.id_proker = ap.id_proker GROUP BY p.id_proker ORDER BY p.nama_proker");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Anggota — B-ORG</title>
  <link rel="stylesheet" href="anggota_edit.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Data Anggota (Full)</h2>
      <p>Perbarui informasi lengkap anggota <strong><?= htmlspecialchars($data_awal['nama_lengkap']) ?></strong></p>
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
            <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $data_awal['nama_lengkap']) ?>"></div>
          <div class="fg"><label>NIM <span class="req">*</span></label>
            <input type="text" name="nim" required value="<?= htmlspecialchars($_POST['nim'] ?? $data_awal['nim']) ?>"></div>
        </div>
        
        <div class="row2">
          <div class="fg"><label>Jenis Kelamin</label>
            <select name="jenis_kelamin">
              <option value="L" <?= (($_POST['jenis_kelamin'] ?? $data_awal['jenis_kelamin']) == 'L') ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= (($_POST['jenis_kelamin'] ?? $data_awal['jenis_kelamin']) == 'P') ? 'selected' : '' ?>>Perempuan</option>
            </select></div>
          <div class="fg"><label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? $data_awal['tanggal_lahir']) ?>"></div>
        </div>

        <div class="row2">
          <div class="fg"><label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $data_awal['email']) ?>"></div>
          <div class="fg"><label>No. HP</label>
            <input type="text" name="no_hp" value="<?= htmlspecialchars($_POST['no_hp'] ?? $data_awal['no_hp']) ?>"></div>
        </div>

        <div class="sec">🎓 Informasi Akademik</div>
        <div class="row2">
          <div class="fg"><label>Program Studi</label>
            <input type="text" name="prodi" value="<?= htmlspecialchars($_POST['prodi'] ?? $data_awal['prodi']) ?>"></div>
          <div class="fg"><label>Fakultas</label>
            <input type="text" name="fakultas" value="<?= htmlspecialchars($_POST['fakultas'] ?? $data_awal['fakultas']) ?>"></div>
        </div>
        <div class="fg"><label>Angkatan</label>
          <input type="number" name="angkatan" value="<?= htmlspecialchars($_POST['angkatan'] ?? $data_awal['angkatan']) ?>"></div>

        <div class="sec">🔐 Akun Login</div>
        <div class="row2">
          <div class="fg"><label>Username <span class="req">*</span></label>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? $data_awal['username']) ?>"></div>
          <div class="fg"><label>Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" placeholder="Min. 6 karakter"></div>
        </div>

        <div class="sec">🏢 Informasi Organisasi</div>
        <div class="row2">
          <div class="fg"><label>Periode Kepengurusan <span class="req">*</span></label>
            <select name="id_periode" required>
              <option value="">-- Pilih Periode --</option>
              <?php while($p=mysqli_fetch_assoc($periode)): ?>
                <option value="<?=$p['id_periode']?>" <?= (($_POST['id_periode'] ?? $data_awal['id_periode']) == $p['id_periode']) ? 'selected' : '' ?>>
                  📅 <?=htmlspecialchars($p['label'])?></option>
              <?php endwhile; ?>
            </select></div>
          <div class="fg"><label>Status Anggota</label>
            <select name="status_anggota">
              <option value="aktif" <?= (($_POST['status_anggota'] ?? $data_awal['status_anggota']) == 'aktif') ? 'selected' : '' ?>>Aktif</option>
              <option value="tidak aktif" <?= (($_POST['status_anggota'] ?? $data_awal['status_anggota']) == 'tidak aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
              <option value="alumni" <?= (($_POST['status_anggota'] ?? $data_awal['status_anggota']) == 'alumni') ? 'selected' : '' ?>>Alumni</option>
            </select></div>
        </div>
        
        <div class="row2">
          <div class="fg"><label>Bidang <span class="req">*</span></label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b=mysqli_fetch_assoc($bidang)): ?>
                <option value="<?=$b['id_bidang']?>" <?= (($_POST['id_bidang'] ?? $data_awal['id_bidang']) == $b['id_bidang']) ? 'selected' : '' ?>>
                  <?=htmlspecialchars($b['nama_bidang'])?></option>
              <?php endwhile; ?></select></div>
          <div class="fg"><label>Jabatan <span class="req">*</span></label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j=mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?=$j['id_jabatan']?>" <?= (($_POST['id_jabatan'] ?? $data_awal['id_jabatan']) == $j['id_jabatan']) ? 'selected' : '' ?>>
                  <?=htmlspecialchars($j['nama_jabatan'])?></option>
              <?php endwhile; ?></select></div>
        </div>

        <div class="fg"><label>Program Kerja</label>
          <select name="id_proker">
            <option value="">-- Tidak Mengambil Proker --</option>
            <?php while($pk=mysqli_fetch_assoc($proker)): ?>
              <option value="<?=$pk['id_proker']?>" <?= (($_POST['id_proker'] ?? $current_proker) == $pk['id_proker']) ? 'selected' : '' ?>>
                <?=htmlspecialchars($pk['nama_proker'])?>
              </option>
            <?php endwhile; ?></select></div>

        <div class="btn-row">
          <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn btn-outline">← Batal</a>
          <button type="submit" name="edit" class="btn btn-blue">💾 Simpan Semua Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
>>>>>>> bismillah-acc
</body>
</html>
