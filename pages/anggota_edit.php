<?php
include '../config/koneksi.php';
require_admin('../');

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
        tab_redirect('pages/anggota_tampil.php', ['status' => 'updated']);
    } else {
        echo "Gagal memperbarui data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Form Edit Anggota</h4>
        </div>
        <div class="card-body">
            <!-- Arahkan action ke file pemroses PHP update Anda -->
            <form action="anggota_update_aksi.php" method="POST">
                
                <!-- SANGAT PENTING: Hidden ID agar PHP tahu data mana yang diupdate -->
                <input type="hidden" name="id_anggota" value="<?= $data['id_anggota'] ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" class="form-control" value="<?= $data['nim'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $data['nama_lengkap'] ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= $data['tanggal_lahir'] ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= $data['no_hp'] ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status Anggota</label>
                        <select name="status_anggota" class="form-select">
                            <option value="Aktif" <?= $data['status_anggota'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= $data['status_anggota'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Angkatan</label>
                        <input type="number" name="angkatan" class="form-control" value="<?= $data['angkatan'] ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Jabatan</label>
                        <input type="number" name="id_jabatan" class="form-control" value="<?= $data['id_jabatan'] ?>" required>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="pages/anggota_tampil.php" class="btn btn-secondary me-2">Batal</a>
                    <!-- name="update" harus sesuai dengan isset($_POST['update']) di PHP -->
                    <button type="submit" name="update" class="btn btn-warning">Update Data</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
