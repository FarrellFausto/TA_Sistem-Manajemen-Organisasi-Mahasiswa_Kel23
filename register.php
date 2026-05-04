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

    $cek = mysqli_query($conn, "SELECT * FROM anggota WHERE nim = '$nim'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "NIM sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password, role) 
                            VALUES ('$username', '$password', 'Anggota')");
        $id_user_baru = mysqli_insert_id($conn);

        mysqli_query($conn, "INSERT INTO anggota 
            (id_user, id_bidang, id_jabatan, nama_lengkap, nim, periode) 
            VALUES 
            ('$id_user_baru','$id_bidang','$id_jabatan','$nama','$nim','$periode')");

        echo "<script>alert('Registrasi berhasil!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>

<div class="container">

    <!-- LEFT -->
    <div class="left">
        <h1>
            Setiap nama,<br>
            setiap data,<br>
            <span>tercatat rapi.</span>
        </h1>

        <p>Pantau data organisasi secara real-time, cepat dan akurat.</p>

        <div class="stats">
            <div><h3>5+</h3><span>Bidang</span></div>
            <div><h3>80+</h3><span>Anggota</span></div>
            <div><h3>24/7</h3><span>Akses</span></div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="card">

            <h2>Buat Akun</h2>
            <p class="subtitle">Daftarkan akun untuk organisasi</p>

            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST">

                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                <input type="text" name="nim" placeholder="NIM" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="id_bidang" required>
                    <option value="">Pilih Bidang</option>
                    <?php
                    $bidang = mysqli_query($conn, "SELECT * FROM bidang");
                    while($b = mysqli_fetch_assoc($bidang)) {
                        echo "<option value='{$b['id_bidang']}'>{$b['nama_bidang']}</option>";
                    }
                    ?>
                </select>

                <select name="id_jabatan" required>
                    <option value="">Pilih Jabatan</option>
                    <?php
                    $jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
                    while($j = mysqli_fetch_assoc($jabatan)) {
                        echo "<option value='{$j['id_jabatan']}'>{$j['nama_jabatan']}</option>";
                    }
                    ?>
                </select>

                <select name="id_proker">
                    <option value="">Pilih Proker</option>
                    <?php
                    $q = mysqli_query($conn, "
                        SELECT p.*, COUNT(tp.id_tugas) as terisi
                        FROM proker p
                        LEFT JOIN tugas_proker tp ON p.id_proker = tp.id_proker
                        GROUP BY p.id_proker HAVING terisi < 2
                    ");
                    while($p = mysqli_fetch_assoc($q)) {
                        $sisa = 2 - $p['terisi'];
                        echo "<option value='{$p['id_proker']}'>{$p['nama_proker']} (Sisa $sisa)</option>";
                    }
                    ?>
                </select>

                <button type="submit" name="register">Daftar</button>

            </form>

            <p class="link">Sudah punya akun? <a href="login.php">Login</a></p>

        </div>
    </div>

</div>

</body>
</html>