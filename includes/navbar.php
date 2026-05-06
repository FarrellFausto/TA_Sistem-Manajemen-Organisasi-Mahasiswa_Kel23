<?php
// Cek apakah session sudah jalan, kalau belum jalankan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gunakan variabel global dari session.php
global $ses_role;
?>
<nav style="background: #2c3e50; padding: 0.8rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <div style="color: #ecf0f1; font-weight: bold; font-size: 1.5rem; letter-spacing: 1px;">B-ORG <span style="color: #3498db;">SYSTEM</span></div>
    <div style="display: flex; gap: 20px;">
        
        <!-- BUNGKUS HREF DENGAN tab_url() -->
        <a href="<?= tab_url('/TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23-backup-ta/index.php') ?>" style="color: #bdc3c7; text-decoration: none; font-weight: 500; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#bdc3c7'">Dashboard</a>
        
        <a href="<?= tab_url('/TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23-backup-ta/pages/anggota_tampil.php') ?>" style="color: #bdc3c7; text-decoration: none; font-weight: 500; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#bdc3c7'">Data Anggota</a>
        
        <?php 
        // CEK ROLE MENGGUNAKAN $ses_role (case-insensitive)
        if (isset($ses_role) && strtolower(trim($ses_role)) === 'admin'): 
        ?>
            <a href="<?= tab_url('/TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23-backup-ta/pages/audit_log.php') ?>" style="color: #bdc3c7; text-decoration: none; font-weight: 500; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#bdc3c7'">Audit Log</a>
        <?php endif; ?>
        
        <a href="<?= tab_url('/TA_Sistem-Manajemen-Organisasi-Mahasiswa_Kel23-backup-ta/logout.php') ?>" style="background: #e74c3c; color: white; padding: 6px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">LOGOUT</a>
    </div>
</nav>