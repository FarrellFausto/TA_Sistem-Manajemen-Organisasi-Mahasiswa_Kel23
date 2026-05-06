<?php
include 'config/koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $data  = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        // Membersihkan sesi lama sebelum buat yang baru
        session_unset();
        session_destroy();
        session_start();
        
        // Regenerasi ID Sesi agar tidak sinkron dengan data sampah lama
        session_regenerate_id(true); 

        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        header("Location: pages/anggota_tampil.php"); // Langsung ke halaman tampil bray
        exit();
    } else {
        $error = "Username atau Password salah, bray!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login - Kelompok 23</title></head>
<body style="font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4;">
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center;">Login Sistem</h2>
        <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box;"><br>
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box;"><br>
            <button type="submit" name="login" style="width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; cursor: pointer; font-weight: bold;">GAS LOGIN</button>
        </form>

        <!-- LINK REGISTER TARUH DI SINI BRAY -->
        <p style="text-align: center; margin-top: 15px; font-size: 14px;">
            Belum jadi anggota? <a href="register.php" style="color: #2980b9; text-decoration: none; font-weight: bold;">Daftar di sini bray</a>
        </p>

    </div>
</body>
</html>