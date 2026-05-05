<?php
include 'config/koneksi.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role_user = $_SESSION['role'];
$search = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : "";
$filter_periode = isset($_GET['periode']) ? (int)$_GET['periode'] : 0;

$where_clause = "a.deleted_at IS NULL";
if (!empty($search)) {
    $where_clause .= " AND (a.nama_lengkap LIKE '%$search%' OR a.nim LIKE '%$search%')";
}
if ($filter_periode > 0) {
    $where_clause .= " AND a.id_periode = $filter_periode";
}

$query_aktif = "SELECT a.*, b.nama_bidang, j.nama_jabatan, p.label as nama_periode
                FROM anggota a
                JOIN bidang b ON a.id_bidang = b.id_bidang
                JOIN jabatan j ON a.id_jabatan = j.id_jabatan
                JOIN periode p ON a.id_periode = p.id_periode
                WHERE $where_clause";
$res_aktif = mysqli_query($conn, $query_aktif);

$res_periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY tahun_mulai DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota</title>
</head>
<body style="font-family: sans-serif; background: #f8f9fa; margin: 0;">
    <?php include 'includes/navbar.php'; ?>
    
    <div style="padding: 30px; max-width: 1200px; margin: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #2c3e50;">Daftar Anggota Aktif</h2>
            <?php if($role_user == 'Admin'): ?>
                <a href="anggota_tambah.php" style="background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">+ Tambah Anggota</a>
            <?php endif; ?>
        </div>

        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <label style="font-weight: bold; color: #2c3e50;">Filter Periode:</label>
                <select name="periode" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; min-width: 150px;">
                    <option value="">Semua Periode</option>
                    <?php while($pr = mysqli_fetch_assoc($res_periode)): ?>
                        <option value="<?= $pr['id_periode'] ?>" <?= ($filter_periode == $pr['id_periode']) ? 'selected' : '' ?>>
                            <?= $pr['label'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="cari" placeholder="Cari NIM/Nama..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; width: 200px;">
                <button type="submit" style="padding: 8px 16px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Filter</button>
                <?php if($filter_periode > 0 || !empty($search)): ?>
                    <a href="anggota_tampil.php" style="padding: 8px 16px; color: #e74c3c; text-decoration: none;">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #2c3e50; color: white; text-align: left;">
                    <th style="padding: 15px;">NIM</th>
                    <th style="padding: 15px;">Nama Lengkap</th>
                    <th style="padding: 15px;">Bidang</th>
                    <th style="padding: 15px;">Jabatan</th>
                    <th style="padding: 15px;">Periode</th>
                    <?php if($role_user == 'Admin'): ?>
                        <th style="padding: 15px;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($res_aktif)): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;"><?= $row['nim'] ?></td>
                    <td style="padding: 15px;"><b><?= $row['nama_lengkap'] ?></b></td>
                    <td style="padding: 15px;"><?= $row['nama_bidang'] ?></td>
                    <td style="padding: 15px;"><?= $row['nama_jabatan'] ?></td>
                    <td style="padding: 15px;"><span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: bold;"><?= $row['nama_periode'] ?></span></td>
                    <?php if($role_user == 'Admin'): ?>
                    <td style="padding: 15px;">
                        <a href="anggota_edit.php?id=<?= $row['id_anggota'] ?>" style="color: #3498db; text-decoration: none;">Edit</a> | 
                        <a href="anggota_hapus.php?id=<?= $row['id_anggota'] ?>&type=soft" style="color: #f39c12; text-decoration: none;">Soft Delete</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- RESTORE SECTION - HANYA ADMIN -->
        <?php if($role_user == 'Admin'): ?>
            <div style="margin-top: 50px;">
                <h3 style="color: #e74c3c;">Tong Sampah (Restore Data)</h3>
                <table style="width: 100%; border-collapse: collapse; background: #fff5f5;">
                    <tr style="background: #c0392b; color: white; text-align: left;">
                        <th style="padding: 12px;">NIM</th>
                        <th style="padding: 12px;">Nama</th>
                        <th style="padding: 12px;">Aksi</th>
                    </tr>
                    <?php 
                    $res_trash = mysqli_query($conn, "SELECT * FROM anggota WHERE deleted_at IS NOT NULL");
                    while($trash = mysqli_fetch_assoc($res_trash)): 
                    ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?= $trash['nim'] ?></td>
                        <td style="padding: 12px;"><?= $trash['nama_lengkap'] ?></td>
                        <td style="padding: 12px;">
                            <a href="anggota_hapus.php?id=<?= $trash['id_anggota'] ?>&type=restore" style="color: #27ae60; text-decoration: none; font-weight: bold;">RESTORE</a> | 
                            <a href="anggota_hapus.php?id=<?= $trash['id_anggota'] ?>&type=hard" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Hapus Permanen?')">Hard Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>