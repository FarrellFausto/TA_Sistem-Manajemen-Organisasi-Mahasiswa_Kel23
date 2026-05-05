<?php
include '../config/koneksi.php';
session_start();
if ($_SESSION['role'] != 'Admin') die("Hanya Admin yang bisa edit data!");

$id = $_GET['id'];
// Ambil data lama
$query_lama = mysqli_query($conn, "SELECT a.*, tp.id_proker FROM anggota a 
                                   LEFT JOIN tugas_proker tp ON a.id_anggota = tp.id_anggota 
                                   WHERE a.id_anggota = $id");
$d = mysqli_fetch_assoc($query_lama);

if (isset($_POST['update'])) {
    $nama = $_POST['nama_lengkap'];
    $id_bidang = $_POST['id_bidang'];
    $id_jabatan = $_POST['id_jabatan'];
    $id_proker = $_POST['id_proker'];

    // Update tabel anggota
    mysqli_query($conn, "UPDATE anggota SET nama_lengkap='$nama', id_bidang='$id_bidang', id_jabatan='$id_jabatan' WHERE id_anggota=$id");

    // Update tabel tugas_proker (Hapus lama, pasang baru)
    mysqli_query($conn, "DELETE FROM tugas_proker WHERE id_anggota=$id");
    if (!empty($id_proker)) {
        mysqli_query($conn, "INSERT INTO tugas_proker (id_anggota, id_proker) VALUES ($id, $id_proker)");
    }

    echo "<script>alert('Data Berhasil Diupdate!'); window.location='anggota_tampil.php';</script>";
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