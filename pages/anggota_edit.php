<?php
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
</body>
</html>
