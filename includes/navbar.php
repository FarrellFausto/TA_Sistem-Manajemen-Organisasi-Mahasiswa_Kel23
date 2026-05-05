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
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
  *{font-family:'Inter','Segoe UI',sans-serif;box-sizing:border-box}

  /* ---- Navbar ---- */
  .b-navbar{background:linear-gradient(135deg,#2c3e50,#34495e);padding:.7rem 2rem;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 12px rgba(0,0,0,.2);position:sticky;top:0;z-index:100}
  .b-nav-brand{color:#ecf0f1;font-weight:700;font-size:1.3rem;letter-spacing:1px;display:flex;align-items:center;gap:8px;text-decoration:none}
  .b-nav-brand span.accent{color:#3498db}
  .b-nav-brand .brand-icon{background:#3498db;width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.9rem}
  .b-nav-links{display:flex;gap:4px;align-items:center}
  .nav-link{color:#bdc3c7;text-decoration:none;font-weight:500;padding:8px 16px;border-radius:6px;transition:.25s;font-size:.88rem}
  .nav-link:hover{color:#fff;background:rgba(255,255,255,.08)}
  .nav-link.active{color:#fff;background:rgba(255,255,255,.15);font-weight:600}
  .nav-divider{width:1px;height:20px;background:rgba(255,255,255,.2);margin:0 8px}
  .nav-user{color:#95a5a6;font-size:.82rem;margin-right:8px;display:flex;align-items:center;gap:6px}
  .role-badge{padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:600;color:white}
  .role-admin{background:#e74c3c}.role-anggota{background:#27ae60}
  .btn-logout{background:#e74c3c;color:white;padding:7px 16px;border-radius:6px;text-decoration:none;font-weight:600;font-size:.85rem;transition:.3s}
  .btn-logout:hover{background:#c0392b}

  /* ---- Toast ---- */
  .toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px}
  .toast{display:flex;align-items:flex-start;gap:12px;background:#fff;border-radius:10px;padding:14px 18px;box-shadow:0 8px 24px rgba(0,0,0,.15);min-width:280px;max-width:380px;animation:toastIn .35s ease;border-left:4px solid}
  .toast.success{border-color:#27ae60}.toast.error{border-color:#e74c3c}.toast.warning{border-color:#f39c12}.toast.info{border-color:#3498db}
  .toast-icon{font-size:1.4rem;flex-shrink:0;margin-top:1px}
  .toast-body{flex:1}
  .toast-title{font-weight:700;font-size:.88rem;margin:0 0 2px;color:#2c3e50}
  .toast-msg{font-size:.82rem;color:#7f8c8d;margin:0;line-height:1.4}
  .toast-close{background:none;border:none;cursor:pointer;color:#bdc3c7;font-size:1.2rem;padding:0;line-height:1;flex-shrink:0}
  .toast-close:hover{color:#7f8c8d}
  .toast.hide{animation:toastOut .35s ease forwards}
  @keyframes toastIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}
  @keyframes toastOut{from{opacity:1}to{opacity:0;transform:translateX(120%)}}
</style>

<div class="toast-container" id="toastContainer"></div>

<nav class="b-navbar">
  <a href="<?= nurl('index.php') ?>" class="b-nav-brand">
    <span class="brand-icon">🏢</span>
    B-ORG <span class="accent">SYSTEM</span>
  </a>
  <div class="b-nav-links">
    <a href="<?= nurl('index.php') ?>" class="<?= nav_active('index.php', $current) ?>">Dashboard</a>
    <a href="<?= nurl('pages/anggota_tampil.php') ?>" class="<?= nav_active('anggota_tampil.php', $current) ?>">Data Anggota</a>
    <?php if (isset($ses_role) && $ses_role === 'Admin'): ?>
      <a href="<?= nurl('pages/audit_log.php') ?>" class="<?= nav_active('audit_log.php', $current) ?>">Audit Log</a>
    <?php endif; ?>
    <div class="nav-divider"></div>
    <?php if(isset($ses_username)): ?>
      <div class="nav-user">
        👤 <?= htmlspecialchars($ses_username) ?>
        <span class="role-badge <?= $ses_role==='Admin'?'role-admin':'role-anggota' ?>"><?= $ses_role ?></span>
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
