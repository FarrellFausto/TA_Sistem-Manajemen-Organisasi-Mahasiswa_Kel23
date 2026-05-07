<?php
include '../config/koneksi.php';

// WAJIB (pakai sistem kamu)
require_login('../');

// DEBUG BIAR GA PUTIH
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CEK ROLE
if ($ses_role !== 'admin') {
    die("Akses ditolak!");
}

$error = "";

/* =========================
   AMBIL DATA DROPDOWN
========================= */
$bidang  = mysqli_query($conn, "SELECT * FROM bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");

/* =========================
   PROSES FORM
========================= */
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

    // CEK NIM
    $check = $conn->prepare("SELECT nim FROM anggota WHERE nim = ?");
    if (!$check) die("Prepare Error: " . $conn->error);

    $check->bind_param("s", $nim);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "NIM sudah terdaftar!";
    } else {

        $sql = "INSERT INTO anggota 
        (nim, nama_lengkap, jenis_kelamin, tanggal_lahir, email, no_hp, prodi, fakultas, angkatan, id_jabatan, id_bidang, id_periode) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare Error: " . $conn->error);

        $stmt->bind_param("ssssssssiiii", 
            $nim, $nama, $jenis_kelamin, $tanggal_lahir, $email, 
            $no_hp, $prodi, $fakultas, $angkatan, $id_jabatan, 
            $id_bidang, $id_periode
        );

        if ($stmt->execute()) {
            header("Location: anggota_tampil.php?tsid=" . urlencode($tsid) . "&success=Data berhasil ditambahkan");
            exit();
        } else {
            $error = "Gagal: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Anggota</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/anggota_tambah.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="main">
  <div class="card">

    <div class="card-header">
      <h2>➕ Tambah Anggota</h2>
      <p>Isi data anggota baru dengan lengkap</p>
    </div>

    <div class="card-body">

      <?php if ($error): ?>
        <div class="errors">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <!-- 🔥 FIX PENTING DI SINI -->
      <form method="POST" action="anggota_tambah.php?tsid=<?= urlencode($tsid) ?>">

        <!-- TSID WAJIB -->
        <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">

        <!-- DATA UTAMA -->
        <div class="sec">Data Utama</div>

        <div class="fg">
          <label>NIM</label>
          <input type="text" name="nim" required>
        </div>

        <div class="fg">
          <label>Nama Lengkap</label>
          <input type="text" name="nama_lengkap" required>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
              <option value="">-- Pilih --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div class="fg">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" required>
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" required>
          </div>

          <div class="fg">
            <label>No HP</label>
            <input type="text" name="no_hp" required>
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Prodi</label>
            <input type="text" name="prodi">
          </div>

          <div class="fg">
            <label>Fakultas</label>
            <input type="text" name="fakultas">
          </div>
        </div>

        <div class="fg">
          <label>Angkatan</label>
          <input type="number" name="angkatan">
        </div>

        <!-- ORGANISASI -->
        <div class="sec">Organisasi</div>

        <div class="row2">
          <div class="fg">
            <label>Bidang</label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b = mysqli_fetch_assoc($bidang)): ?>
                <option value="<?= $b['id_bidang'] ?>">
                  <?= htmlspecialchars($b['nama_bidang']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="fg">
            <label>Jabatan</label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j = mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?= $j['id_jabatan'] ?>">
                  <?= htmlspecialchars($j['nama_jabatan']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="fg">
          <label>Periode</label>
          <select name="id_periode" required>
            <option value="">-- Pilih Periode --</option>
            <?php while($p = mysqli_fetch_assoc($periode)): ?>
              <option value="<?= $p['id_periode'] ?>">
                <?= htmlspecialchars($p['label']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- BUTTON -->
        <div class="btn-row">
          <a href="anggota_tampil.php?tsid=<?= urlencode($tsid) ?>" class="btn btn-outline">← Batal</a>
          <button type="submit" name="tambah" class="btn btn-green">💾 Simpan</button>
        </div>

      </form>
    </div>

  </div>
</div>

</body>
</html>
