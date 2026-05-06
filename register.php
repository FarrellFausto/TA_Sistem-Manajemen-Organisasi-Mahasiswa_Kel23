<?php
include 'config/koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nim      = mysqli_real_escape_string($conn, $_POST['nim']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id_bidang  = $_POST['id_bidang'];
    $id_jabatan = $_POST['id_jabatan'];
    $id_proker  = $_POST['id_proker'];
    $periode    = "2025/2026";

    // Cek apakah username atau NIM sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM anggota WHERE nim = '$nim'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIM sudah terdaftar bray!');</script>";
    } else {
        // 1. Insert ke Tabel USERS (Role otomatis 'Anggota')
        $q_user = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'Anggota')";
        mysqli_query($conn, $q_user);
        $id_user_baru = mysqli_insert_id($conn);

        // 2. Insert ke Tabel ANGGOTA
        $q_anggota = "INSERT INTO anggota (id_user, id_bidang, id_jabatan, nama_lengkap, nim, periode) 
                      VALUES ('$id_user_baru', '$id_bidang', '$id_jabatan', '$nama', '$nim', '$periode')";
        mysqli_query($conn, $q_anggota);
        $id_anggota_baru = mysqli_insert_id($conn);

        // 3. Insert ke Tabel TUGAS_PROKER (Jika milih proker)
        if (!empty($id_proker)) {
            $q_proker = "INSERT INTO tugas_proker (id_anggota, id_proker) VALUES ('$id_anggota_baru', '$id_proker')";
            mysqli_query($conn, $q_proker);
        }

        echo "<script>alert('Registrasi Berhasil! Silakan Login bray.'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register Anggota - B-Org</title></head>
<body style="font-family: sans-serif; background: #f4f4f4; padding: 20px;">
    <div style="background: white; max-width: 500px; margin: auto; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center;">Daftar Anggota Baru</h2>
        <form method="POST">
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_lengkap" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>
            
            <label>NIM:</label><br>
            <input type="text" name="nim" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>

            <label>Username (untuk Login):</label><br>
            <input type="text" name="username" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>

            <label>Password:</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

            <label>Pilih Bidang:</label><br>
            <select name="id_bidang" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <?php
                $bidang = mysqli_query($conn, "SELECT * FROM bidang");
                while($b = mysqli_fetch_assoc($bidang)) echo "<option value='".$b['id_bidang']."'>".$b['nama_bidang']."</option>";
                ?>
            </select><br>

            <label>Pilih Jabatan:</label><br>
            <select name="id_jabatan" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <?php
                $jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
                while($j = mysqli_fetch_assoc($jabatan)) echo "<option value='".$j['id_jabatan']."'>".$j['nama_jabatan']."</option>";
                ?>
            </select><br>

            <label>Pilih Program Kerja (Max 2 Orang):</label><br>
            <select name="id_proker" style="width: 100%; padding: 8px; margin-bottom: 20px;">
                <option value="">-- Tidak Mengambil Proker --</option>
                <?php
                // Logic Kuota: Hanya proker yang diisi < 2 orang yang muncul
                $q_kuota = "SELECT p.*, COUNT(tp.id_tugas) as terisi 
                            FROM proker p 
                            LEFT JOIN tugas_proker tp ON p.id_proker = tp.id_proker 
                            GROUP BY p.id_proker HAVING terisi < 2";
                $proker = mysqli_query($conn, $q_kuota);
                while($p = mysqli_fetch_assoc($proker)) echo "<option value='".$p['id_proker']."'>".$p['nama_proker']." (Slot Sisa: ".(2-$p['terisi']).")</option>";
                ?>
            </select><br>

            <button type="submit" name="register" style="width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">DAFTAR SEKARANG</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>