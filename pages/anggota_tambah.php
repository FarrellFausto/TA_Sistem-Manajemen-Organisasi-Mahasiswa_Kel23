<?php
session_start(); 
include 'config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] == 'viewer') {
    header("Location: login.php?pesan=restricted");
    exit();
}

if (isset($_POST['tambah'])) {
    $nim           = $_POST['nim'];
    $nama          = $_POST['nama_lengkap'];
    $id_jabatan    = $_POST['id_jabatan'];
    $id_bidang     = $_POST['id_bidang'];
    $id_periode    = $_POST['id_periode'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $email         = $_POST['email'];
    $no_hp         = $_POST['no_hp'];
    $prodi         = $_POST['prodi'];
    $fakultas      = $_POST['fakultas'];
    $angkatan      = $_POST['angkatan'];

    $check = $conn->prepare("SELECT nim FROM anggota WHERE nim = ?");
    $check->bind_param("s", $nim);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Error: NIM $nim sudah terdaftar!'); window.history.back();</script>";
        exit();
    } else {
        $sql = "INSERT INTO anggota 
                (nim, nama_lengkap, jenis_kelamin, tanggal_lahir, email, no_hp, prodi, fakultas, angkatan, id_jabatan, id_bidang, id_periode) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssiiii", 
            $nim, $nama, $jenis_kelamin, $tanggal_lahir, $email, 
            $no_hp, $prodi, $fakultas, $angkatan, $id_jabatan, 
            $id_bidang, $id_periode
        );
        
        if ($stmt->execute()) {
            header("Location: anggota_tampil.php?status=sukses");
            exit(); 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota - Admin Panel</title>
</head>
<body style="font-family: sans-serif; background: #f8f9fa; margin: 0;">
    <?php include '../includes/navbar.php'; ?>
    
    <div style="max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #27ae60; margin-top: 0;">Tambah Anggota Baru</h2>
        <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 20px;">Silakan isi formulir di bawah untuk mendaftarkan anggota baru ke sistem.</p>
        
        <form method="POST">
            <label style="font-weight: bold; font-size: 0.9rem;">Biodata Dasar</label>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            <input type="text" name="nim" placeholder="NIM" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            
            <label style="font-weight: bold; font-size: 0.9rem;">Akun Login</label>
            <input type="text" name="username" placeholder="Username Login" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
            
            <label style="font-weight: bold; font-size: 0.9rem;">Struktur Organisasi</label>
            <select name="id_bidang" required style="width: 100%; padding: 12px; margin: 8px 0 15px 0; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Bidang --</option>
                <?php
                $bid = mysqli_query($conn, "SELECT * FROM bidang");
                while($b = mysqli_fetch_assoc($bid)) echo "<option value='".$b['id_bidang']."'>".$b['nama_bidang']."</option>";
                ?>
            </select>

            <select name="id_jabatan" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Jabatan --</option>
                <?php
                $jab = mysqli_query($conn, "SELECT * FROM jabatan");
                while($j = mysqli_fetch_assoc($jab)) echo "<option value='".$j['id_jabatan']."'>".$j['nama_jabatan']."</option>";
                ?>
            </select>

            <label style="font-weight: bold; font-size: 0.9rem;">Program Kerja (Kuota Sisa)</label>
            <select name="id_proker" style="width: 100%; padding: 12px; margin: 8px 0 25px 0; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <option value="">-- Pilih Proker (Optional) --</option>
                <?php
                // Logic Kuota: Hanya proker dengan < 2 anggota yang muncul
                $pro = mysqli_query($conn, "SELECT p.*, COUNT(tp.id_tugas) as t 
                                            FROM proker p 
                                            LEFT JOIN tugas_proker tp ON p.id_proker=tp.id_proker 
                                            GROUP BY p.id_proker HAVING t < 2");
                while($p = mysqli_fetch_assoc($pro)) {
                    $sisa = 2 - $p['t'];
                    echo "<option value='".$p['id_proker']."'>".$p['nama_proker']." (Sisa: $sisa)</option>";
                }
                ?>
            </select>

            <button type="submit" name="tambah" style="width: 100%; padding: 14px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; font-weight: bold;">SIMPAN DATA ANGGOTA</button>
            <a href="anggota_tampil.php" style="display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; font-size: 0.9rem;">Batal & Kembali</a>
        </form>
    </div>
</body>
</html>
