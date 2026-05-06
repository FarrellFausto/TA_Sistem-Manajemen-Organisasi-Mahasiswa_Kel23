<?php
// ============================================================
// includes/navbar.php — Navbar dengan Tab-Aware Session
// session.php sudah di-load via koneksi.php di halaman pemanggil
// ============================================================

$in_pages = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false);
$base     = $in_pages ? '../' : '';
$current  = basename($_SERVER['PHP_SELF']);

function nav_active($page, $current) {
    return ($current === $page)
        ? 'nav-link active'
        : 'nav-link';
}

// Semua URL navbar harus bawa tsid agar tab tidak kehilangan sesi
function nurl($path) {
    global $tsid, $base;
    return $base . $path . '?tsid=' . urlencode($tsid);
}
?>

<!-- CSS Navbar dipisah -->
<link rel="stylesheet" href="<?= $base ?>assets/css/navbar.css">

<div class="toast-container" id="toastContainer"></div>

<nav class="b-navbar">
  <a href="<?= nurl('index.php') ?>" class="b-nav-brand">
    <span class="brand-icon">🏢</span>
    B-ORG <span class="accent">SYSTEM</span>
  </a>
  <div class="b-nav-links">
    <a href="<?= nurl('index.php') ?>" class="<?= nav_active('index.php', $current) ?>">Dashboard</a>
    <a href="<?= nurl('pages/anggota_tampil.php') ?>" class="<?= nav_active('anggota_tampil.php', $current) ?>">Data Anggota</a>
    <?php if (isset($ses_role) && $ses_role === 'admin'): ?>
      <a href="<?= nurl('pages/audit_log.php') ?>" class="<?= nav_active('audit_log.php', $current) ?>">Audit Log</a>
    <?php endif; ?>
    <div class="nav-divider"></div>
    <?php if(isset($ses_username)): ?>
      <div class="nav-user">
        👤 <?= htmlspecialchars($ses_username) ?>
        <span class="role-badge <?= $ses_role==='admin'?'role-admin':'role-anggota' ?>"><?= $ses_role ?></span>
      </div>
    <?php endif; ?>
    <a href="<?= $base ?>logout.php?tsid=<?= urlencode($tsid) ?>" class="btn-logout">LOGOUT</a>
  </div>
</nav>

<script>
// ============================================================
// Tab Session JS — Sinkronisasi tsid antara URL dan sessionStorage
// ============================================================
(function () {
    const STORAGE_KEY = 'b_org_tsid';

    // 1. Ambil tsid dari URL (selalu paling fresh dari server)
    const urlParams = new URLSearchParams(window.location.search);
    const urlTsid   = urlParams.get('tsid');

    if (urlTsid) {
        // Simpan ke sessionStorage tab ini
        sessionStorage.setItem(STORAGE_KEY, urlTsid);
    }

    const tsid = sessionStorage.getItem(STORAGE_KEY) || urlTsid || '';
    if (!tsid) return;

    // 2. Inject tsid ke semua link internal yang belum punya tsid
    function injectLinks() {
        document.querySelectorAll('a[href]').forEach(a => {
            const href = a.getAttribute('href');
            if (!href) return;
            // Skip: link eksternal, anchor, javascript, logout (sudah punya tsid dari PHP)
            if (href.startsWith('http') || href.startsWith('#') ||
                href.startsWith('javascript') || href.includes('logout')) return;
            // Skip kalau sudah ada tsid
            if (href.includes('tsid=')) return;
            // Tambahkan tsid
            a.href = href + (href.includes('?') ? '&' : '?') + 'tsid=' + encodeURIComponent(tsid);
        });
    }

    // 3. Inject tsid ke semua form sebagai hidden input
    function injectForms() {
        document.querySelectorAll('form').forEach(form => {
            if (form.querySelector('input[name="tsid"]')) return; // sudah ada
            const inp  = document.createElement('input');
            inp.type   = 'hidden';
            inp.name   = 'tsid';
            inp.value  = tsid;
            form.appendChild(inp);
        });
    }

    // Jalankan saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { injectLinks(); injectForms(); });
    } else {
        injectLinks(); injectForms();
    }

    // MutationObserver: tangkap link/form yang di-render dinamis (misal JS)
    const obs = new MutationObserver(() => { injectLinks(); injectForms(); });
    obs.observe(document.body, { childList: true, subtree: true });
})();

// ============================================================
// Toast Notification System
// ============================================================
function showToast(message, type = 'success', title = '') {
    const icons  = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const titles = { success: 'Berhasil!', error: 'Gagal!', warning: 'Perhatian!', info: 'Info' };
    const toast  = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML =
        '<span class="toast-icon">' + (icons[type] || '💬') + '</span>' +
        '<div class="toast-body">' +
            '<p class="toast-title">' + (title || titles[type]) + '</p>' +
            '<p class="toast-msg">' + message + '</p>' +
        '</div>' +
        '<button class="toast-close" onclick="dismissToast(this.parentElement)">×</button>';
    document.getElementById('toastContainer').appendChild(toast);
    setTimeout(() => dismissToast(toast), 5000);
}
function dismissToast(el) {
    if (!el) return;
    el.classList.add('hide');
    setTimeout(() => el && el.remove(), 380);
}

// Auto-tampilkan toast dari URL params
(function () {
    const p = new URLSearchParams(window.location.search);
    if (p.get('success')) showToast(decodeURIComponent(p.get('success')), 'success');
    if (p.get('error'))   showToast(decodeURIComponent(p.get('error')),   'error');
    if (p.get('warning')) showToast(decodeURIComponent(p.get('warning')), 'warning');
    if (p.get('info'))    showToast(decodeURIComponent(p.get('info')),    'info');
})();
</script>
