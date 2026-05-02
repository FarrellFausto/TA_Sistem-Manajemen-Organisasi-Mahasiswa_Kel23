<?php
session_start();
if (!isset($_SESSION['username'])) header("Location: login.php");
include 'config/koneksi.php';

$total_anggota = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM anggota WHERE deleted_at IS NULL"));
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body style="margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa;">
    <?php include 'includes/navbar.php'; ?>
    <div style="padding: 40px; max-width: 1200px; margin: auto;">
        <h1 style="color: #2c3e50;">Selamat Datang, <span style="color: #3498db;"><?= $_SESSION['username'] ?></span>! 👋</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">Anda masuk sebagai <b><?= $_SESSION['role'] ?></b>. Kelola data organisasi Anda di sini.</p>
        
        <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #3498db;">
                <h3 style="margin: 0; color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase;">Total Anggota Aktif</h3>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: #2c3e50;"><?= $total_anggota ?></p>
            </div>
        </div>
    </div>
</body>
</html>
