<?php
include 'config/koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $data  = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        session_regenerate_id(true);

        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        header("Location: anggota_tampil.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - B-Org</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="container">

    <!-- LEFT -->
    <div class="left">
        <div class="overlay">
            <h1>
                Setiap nama,<br>
                setiap data,<br>
                <span>tercatat rapi.</span>
            </h1>
            <p>Pantau data organisasi secara real-time, cepat dan akurat.</p>

            <div class="stats">
                <div><b>5+</b><span>Bidang</span></div>
                <div><b>80+</b><span>Anggota</span></div>
                <div><b>24/7</b><span>Akses</span></div>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="card" id="loginCard">

            <h2>Selamat Datang</h2>
            <p>Masuk untuk mengelola data organisasi Anda.</p>

            <?php if(isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <label>Email / Username</label>
                <input type="text" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <div class="remember">
                    <input type="checkbox"> Ingat saya
                </div>

                <button type="submit" name="login">Masuk</button>

            </form>

            <div class="divider">ATAU</div>

            <p class="link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>

        </div>
    </div>

</div>

<!--SCRIPT ANIMASI-->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const card = document.getElementById("loginCard");

    setTimeout(() => {
        card.classList.add("show");
    }, 100);
});
</script>

</body>
</html>