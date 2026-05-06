<?php
// ============================================================
// config/session.php — Tab-Aware Session Manager
//
// MASALAH LAMA:
//   PHP session pakai 1 cookie (PHPSESSID) untuk semua tab.
//   Login di Tab 2 langsung overwrite $_SESSION['role'] milik Tab 1.
//
// SOLUSI:
//   Simpan data user di $_SESSION['tabs'][$tsid], bukan di root session.
//   Setiap tab browser punya tsid unik yang disimpan di sessionStorage
//   (sessionStorage memang per-tab, beda dari cookie/localStorage).
//   Setiap request PHP baca tsid dari GET/POST → load data tab yang tepat.
//
// STRUKTUR SESSION:
//   $_SESSION['tabs'] = [
//       'abc123...' => ['id_user'=>1, 'username'=>'admin_utama', 'role'=>'Admin'],
//       'def456...' => ['id_user'=>3, 'username'=>'budi_santoso', 'role'=>'Anggota'],
//   ]
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil tsid dari request (GET lebih prioritas dari POST untuk navigasi)
$tsid = trim($_GET['tsid'] ?? $_POST['tsid'] ?? '');

// Variabel global untuk session tab aktif (TIDAK pernah disimpan ke root $_SESSION)
$ses_id_user  = null;
$ses_username = null;
$ses_role     = null;
$ses_valid    = false;

// Load data tab yang sesuai dengan tsid
if ($tsid !== '' && isset($_SESSION['tabs'][$tsid])) {
    $tab          = $_SESSION['tabs'][$tsid];
    $ses_id_user  = $tab['id_user'];
    $ses_username = $tab['username'];
    $ses_role     = $tab['role'];
    $ses_valid    = true;
}

// ============================================================
// HELPERS
// ============================================================

/**
 * Buat URL dengan tsid di-append otomatis (untuk link & redirect)
 */
function tab_url(string $url, array $extra = []): string {
    global $tsid;
    $extra['tsid'] = $tsid;
    $sep = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $sep . http_build_query($extra);
}

/**
 * Redirect sambil bawa tsid
 */
function tab_redirect(string $url, array $extra = []): void {
    header('Location: ' . tab_url($url, $extra));
    exit();
}

/**
 * Proteksi halaman: harus login
 */
function require_login(string $base = ''): void {
    global $ses_valid, $tsid;
    if (!$ses_valid) {
        $url = $base . 'login.php';
        if ($tsid) $url .= '?tsid=' . urlencode($tsid);
        header("Location: $url");
        exit();
    }
}

/**
 * Proteksi halaman: harus Admin
 */
function require_admin(string $base = ''): void {
    global $ses_valid, $ses_role, $tsid;
    if (!$ses_valid || $ses_role !== 'Admin') {
        $url = $base . 'login.php?error=' . urlencode('Akses ditolak. Hanya Admin.');
        if ($tsid) $url .= '&tsid=' . urlencode($tsid);
        header("Location: $url");
        exit();
    }
}

/**
 * Buat tab session baru saat login berhasil
 */
function create_tab_session(string $tsid, int $id_user, string $username, string $role): void {
    if (!isset($_SESSION['tabs'])) {
        $_SESSION['tabs'] = [];
    }
    $_SESSION['tabs'][$tsid] = [
        'id_user'  => $id_user,
        'username' => $username,
        'role'     => $role,
    ];
}

/**
 * Hapus tab session saat logout (hanya tab ini, tab lain tidak terpengaruh)
 */
function destroy_tab_session(string $tsid): void {
    if (isset($_SESSION['tabs'][$tsid])) {
        unset($_SESSION['tabs'][$tsid]);
    }
    // Bersihkan tabs yang sudah kosong lebih dari 24 jam (opsional GC)
}
