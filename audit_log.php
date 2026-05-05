<?php
include 'config/koneksi.php';
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ilegal! Anda bukan Admin.");
}

// Ambil data log dengan Prepared Statement
$query = "SELECT * FROM audit_log ORDER BY waktu DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Keamanan - Audit Log</title>
</head>
<body style="font-family: sans-serif; background: #f8f9fa; margin: 0;">
  
    <?php include 'includes/navbar.php'; ?>
    
    <div style="padding: 30px; max-width: 1000px; margin: auto;">
        <h2 style="color: #2c3e50;">Audit Log</h2>
        
        <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #2c3e50; color: white; text-align: left;">
                    <th style="padding: 15px;">User</th>
                    <th style="padding: 15px;">Aksi</th>
                    <th style="padding: 15px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr style="border-bottom: 1px solid #eee; font-size: 0.95rem;">
                    <td style="padding: 15px; color: #7f8c8d; font-weight: bold;"><?= htmlspecialchars($row['username']) ?></td>
                    <td style="padding: 15px;">
                        <b><?= htmlspecialchars($row['aktivitas']) ?></b> - <?= htmlspecialchars($row['keterangan']) ?>
                    </td>
                    <td style="padding: 15px; color: #7f8c8d;"><?= $row['waktu'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
