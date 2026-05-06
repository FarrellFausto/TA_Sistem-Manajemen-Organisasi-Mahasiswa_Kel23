<?php
include '../config/koneksi.php';
require_login('../');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID anggota tidak valid.");
}

$errors = [];

// ambil data anggota
$stmt = $conn->prepare("
    SELECT * FROM anggota 
    WHERE id_anggota = ? AND deleted_at IS NULL
");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Data anggota tidak ditemukan.");
}

// ambil bidang, jabatan, periode
$bidang  = mysqli_query($conn, "SELECT * FROM bidang ORDER BY nama_bidang");
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");

// proses update
if (isset($_POST['update'])) {
    $nama      = trim($_POST['nama_lengkap'] ?? '');
    $jk        = trim($_POST['jenis_kelamin'] ?? '');
    $tgl_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $no_hp     = trim($_POST['no_hp'] ?? '');
    $prodi     = trim($_POST['prodi'] ?? '');
    $fakultas  = trim($_POST['fakultas'] ?? '');
    $id_bidang = (int)($_POST['id_bidang'] ?? 0);
    $id_jabatan= (int)($_POST['id_jabatan'] ?? 0);
    $id_periode= (int)($_POST['id_periode'] ?? 0);

    if (empty($nama)) $errors[] = "Nama lengkap tidak boleh kosong.";
    if (empty($jk)) $errors[] = "Jenis kelamin wajib dipilih.";
    if ($id_bidang <= 0) $errors[] = "Bidang wajib dipilih.";
    if ($id_jabatan <= 0) $errors[] = "Jabatan wajib dipilih.";
    if ($id_periode <= 0) $errors[] = "Periode wajib dipilih.";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    $tanggal_lahir = (!empty($tgl_lahir)) ? $tgl_lahir : NULL;

    if (empty($errors)) {
        // =========================
        // UPDATE DATA
        // =========================
        $stmt = $conn->prepare("
            UPDATE anggota SET 
                nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, email=?, no_hp=?, prodi=?, fakultas=?,
                id_bidang=?, id_jabatan=?, id_periode=?
            WHERE id_anggota=?
        ");
        $stmt->bind_param(
            "sssssssiiii",
            $nama, $jk, $tanggal_lahir, $email, $no_hp, $prodi, $fakultas,
            $id_bidang, $id_jabatan, $id_periode,
            $id
        );
        $stmt->execute();
        $stmt->close();

        // =========================
        //  AUDIT LOG TAMBAHAN
        // =========================
        $id_user = $_SESSION['id_user'] ?? null;

        $aksi = "Mengedit anggota: {$nama} (ID: {$id})";

        $log = $conn->prepare("
            INSERT INTO log_aktivitas (id_user, aksi, waktu)
            VALUES (?, ?, NOW())
        ");
        $log->bind_param("is", $id_user, $aksi);
        $log->execute();
        $log->close();

        // redirect
        tab_redirect('anggota_tampil.php', [
            'success' => "Data anggota berhasil diupdate!"
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Anggota — B-ORG</title>

  <link rel="stylesheet" href="../assets/css/anggota_edit.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="main">
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Data Anggota</h2>
      <p>Silakan ubah data anggota sesuai kebutuhan</p>
    </div>

    <div class="card-body">

      <div class="info-bar">
        Anda sedang mengedit anggota: <strong><?= htmlspecialchars($data['nama_lengkap']) ?></strong>
      </div>

      <?php if(!empty($errors)): ?>
        <div class="errors">
          <strong>Terdapat kesalahan:</strong>
          <ul>
            <?php foreach($errors as $e): ?>
              <li><?= $e ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST">

        <div class="sec">📋 Biodata</div>

        <div class="fg">
          <label>NIM</label>
          <input type="text" value="<?= htmlspecialchars($data['nim']) ?>" readonly>
        </div>

        <div class="fg">
          <label>Nama Lengkap <span class="req">*</span></label>
          <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $data['nama_lengkap']) ?>">
        </div>

        <div class="row2">
          <div class="fg">
            <label>Jenis Kelamin <span class="req">*</span></label>
            <select name="jenis_kelamin" required>
              <option value="">-- Pilih --</option>
              <option value="L" <?= (($_POST['jenis_kelamin'] ?? $data['jenis_kelamin']) == 'L') ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= (($_POST['jenis_kelamin'] ?? $data['jenis_kelamin']) == 'P') ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>

          <div class="fg">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($_POST['tanggal_lahir'] ?? $data['tanggal_lahir']) ?>">
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $data['email']) ?>">
          </div>

          <div class="fg">
            <label>No HP</label>
            <input type="text" name="no_hp" value="<?= htmlspecialchars($_POST['no_hp'] ?? $data['no_hp']) ?>">
          </div>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Prodi</label>
            <input type="text" name="prodi" value="<?= htmlspecialchars($_POST['prodi'] ?? $data['prodi']) ?>">
          </div>

          <div class="fg">
            <label>Fakultas</label>
            <input type="text" name="fakultas" value="<?= htmlspecialchars($_POST['fakultas'] ?? $data['fakultas']) ?>">
          </div>
        </div>

        <div class="sec">🏢 Struktur Organisasi</div>

        <div class="fg">
          <label>Periode <span class="req">*</span></label>
          <select name="id_periode" required>
            <option value="">-- Pilih Periode --</option>
            <?php while($p = mysqli_fetch_assoc($periode)): ?>
              <option value="<?= $p['id_periode'] ?>"
                <?= (($_POST['id_periode'] ?? $data['id_periode']) == $p['id_periode']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['label']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="row2">
          <div class="fg">
            <label>Bidang <span class="req">*</span></label>
            <select name="id_bidang" required>
              <option value="">-- Pilih Bidang --</option>
              <?php while($b = mysqli_fetch_assoc($bidang)): ?>
                <option value="<?= $b['id_bidang'] ?>"
                  <?= (($_POST['id_bidang'] ?? $data['id_bidang']) == $b['id_bidang']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['nama_bidang']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="fg">
            <label>Jabatan <span class="req">*</span></label>
            <select name="id_jabatan" required>
              <option value="">-- Pilih Jabatan --</option>
              <?php while($j = mysqli_fetch_assoc($jabatan)): ?>
                <option value="<?= $j['id_jabatan'] ?>"
                  <?= (($_POST['id_jabatan'] ?? $data['id_jabatan']) == $j['id_jabatan']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($j['nama_jabatan']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="btn-row">
          <button type="submit" name="update" class="btn btn-blue">💾 Simpan Perubahan</button>
          <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn btn-outline">↩ Kembali</a>
        </div>

      </form>

    </div>
  </div>
</div>

</body>
</html>
