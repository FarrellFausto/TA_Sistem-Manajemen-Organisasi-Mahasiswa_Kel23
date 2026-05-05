<?php
include '../config/koneksi.php';
require_admin('../');

$filter_user    = (int)($_GET['id_user'] ?? 0);
$filter_keyword = trim($_GET['q'] ?? '');
$page           = max(1, (int)($_GET['page'] ?? 1));
$per_page       = 20;
$offset         = ($page - 1) * $per_page;

// Build WHERE
$where = "1=1"; $params = []; $types = "";
if ($filter_user > 0)          { $where .= " AND l.id_user = ?";    $params[] = $filter_user;          $types .= "i"; }
if (!empty($filter_keyword))   { $where .= " AND l.aksi LIKE ?";    $params[] = "%$filter_keyword%";   $types .= "s"; }

// Count total
$stmt_cnt = $conn->prepare("SELECT COUNT(*) FROM log_aktivitas l WHERE $where");
if (!empty($params)) { $stmt_cnt->bind_param($types, ...$params); }
$stmt_cnt->execute();
$total_rows  = $stmt_cnt->get_result()->fetch_row()[0];
$stmt_cnt->close();
$total_pages = (int)ceil($total_rows / $per_page);

// Fetch logs
$p2 = $params; $t2 = $types;
$p2[] = $per_page; $t2 .= "i";
$p2[] = $offset;   $t2 .= "i";
$stmt = $conn->prepare(
    "SELECT l.id_log, l.aksi, l.waktu, u.username, u.role
     FROM log_aktivitas l LEFT JOIN users u ON l.id_user = u.id_user
     WHERE $where ORDER BY l.waktu DESC LIMIT ? OFFSET ?"
);
$stmt->bind_param($t2, ...$p2);
$stmt->execute();
$logs = $stmt->get_result();
$stmt->close();

$all_admins  = mysqli_query($conn, "SELECT id_user, username FROM users WHERE role='Admin' ORDER BY username");
$total_log   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM log_aktivitas"))[0];
$total_hari  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM log_aktivitas WHERE DATE(waktu) = CURDATE()"))[0];
$total_admin_aktif = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT id_user) FROM log_aktivitas WHERE id_user IS NOT NULL"))[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Audit Log — B-ORG</title>
  <style>
    body { margin:0; background:#f0f3f8; }
    .main { padding:28px; max-width:1300px; margin:auto; }
    .page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .page-header h2 { margin:0 0 4px; color:#2c3e50; font-size:1.4rem; }
    .page-header p  { margin:0; color:#95a5a6; font-size:.85rem; }
    .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:22px; }
    .stat { background:white; padding:18px 20px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); border-top:3px solid; }
    .stat.purple { border-color:#9b59b6; } .stat.blue { border-color:#3498db; } .stat.orange { border-color:#f39c12; }
    .stat .snum   { font-size:2rem; font-weight:800; margin:4px 0; line-height:1; }
    .stat.purple .snum { color:#9b59b6; } .stat.blue .snum { color:#3498db; } .stat.orange .snum { color:#f39c12; }
    .stat .slabel { font-size:.74rem; color:#95a5a6; text-transform:uppercase; letter-spacing:.5px; font-weight:600; }
    .filter-bar { background:white; padding:14px 18px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .filter-bar input, .filter-bar select { padding:9px 13px; border:2px solid #e8eaed; border-radius:8px; font-size:.88rem; min-width:160px; transition:.3s; }
    .filter-bar input:focus, .filter-bar select:focus { outline:none; border-color:#9b59b6; }
    .table-wrap { background:white; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:linear-gradient(135deg,#8e44ad,#9b59b6); }
    th { padding:13px 14px; color:white; font-size:.76rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:left; }
    td { padding:12px 14px; border-bottom:1px solid #f0f3f8; font-size:.84rem; color:#2c3e50; vertical-align:top; }
    tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fbf9ff; }
    .badge { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:600; white-space:nowrap; display:inline-block; }
    .badge-admin  { background:#fde8e8; color:#c0392b; }
    .badge-member { background:#e8f8f1; color:#27ae60; }
    .badge-lock   { background:#f3e8ff; color:#8e44ad; padding:5px 12px; font-size:.78rem; }
    /* Warna aksi */
    .aksi-tambah  { color:#27ae60; } .aksi-edit { color:#3498db; }
    .aksi-soft    { color:#f39c12; } .aksi-hard { color:#e74c3c; font-weight:600; }
    .aksi-restore { color:#8e44ad; } .aksi-login { color:#95a5a6; }
    .btn { padding:8px 16px; border-radius:8px; font-weight:600; font-size:.84rem; cursor:pointer; border:none; text-decoration:none; transition:.3s; display:inline-flex; align-items:center; gap:5px; }
    .btn-purple { background:#9b59b6; color:white; } .btn-purple:hover { background:#8e44ad; }
    .btn-gray   { background:#95a5a6; color:white; } .btn-gray:hover   { background:#7f8c8d; }
    .empty-state { text-align:center; padding:50px; color:#95a5a6; }
    .empty-state .icon { font-size:3rem; margin-bottom:10px; }
    .pag { display:flex; justify-content:center; gap:6px; padding:16px; flex-wrap:wrap; }
    .plink { padding:7px 13px; border-radius:8px; text-decoration:none; font-size:.84rem; font-weight:600; transition:.3s; }
    .plink.active { background:#9b59b6; color:white; }
    .plink:not(.active) { background:white; color:#7f8c8d; border:2px solid #e8eaed; }
    .plink:not(.active):hover { border-color:#9b59b6; color:#9b59b6; }
    .plink.disabled { opacity:.4; pointer-events:none; }
    .pag-info { padding:11px 14px; font-size:.8rem; color:#7f8c8d; border-top:1px solid #f0f3f8; background:#fafbfc; }
    .ts { color:#95a5a6; font-size:.76rem; white-space:nowrap; }
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">

  <div class="page-header">
    <div>
      <h2>📝 Audit Log Aktivitas</h2>
      <p>Histori semua aksi manipulasi data — hanya terlihat oleh Admin</p>
    </div>
    <span class="badge badge-lock">🔒 Admin Only</span>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stat purple">
      <div class="slabel">📋 Total Log</div>
      <div class="snum"><?= number_format($total_log) ?></div>
    </div>
    <div class="stat blue">
      <div class="slabel">📅 Aktivitas Hari Ini</div>
      <div class="snum"><?= $total_hari ?></div>
    </div>
    <div class="stat orange">
      <div class="slabel">👤 Admin Aktif</div>
      <div class="snum"><?= $total_admin_aktif ?></div>
    </div>
  </div>

  <!-- Filter -->
  <form method="GET" class="filter-bar">
    <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">
    <input type="text" name="q" placeholder="🔍 Cari di log aktivitas..."
           value="<?= htmlspecialchars($filter_keyword) ?>">
    <select name="id_user">
      <option value="0">👤 Semua Admin</option>
      <?php while($au = mysqli_fetch_assoc($all_admins)): ?>
        <option value="<?= $au['id_user'] ?>" <?= $filter_user==$au['id_user']?'selected':'' ?>>
          <?= htmlspecialchars($au['username']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-purple">Filter</button>
    <?php if(!empty($filter_keyword) || $filter_user > 0): ?>
      <a href="<?= tab_url('audit_log.php') ?>" class="btn btn-gray">✕ Reset</a>
    <?php endif; ?>
    <span style="color:#95a5a6;font-size:.8rem;margin-left:auto">
      <?= number_format($total_rows) ?> record ditemukan
    </span>
  </form>

  <!-- Tabel -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:55px">ID</th>
          <th style="width:140px">Admin</th>
          <th>Keterangan Aktivitas</th>
          <th style="width:150px">Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php $count = 0; while($log = $logs->fetch_assoc()): $count++; ?>
        <tr>
          <td style="color:#bdc3c7;font-size:.76rem"><?= $log['id_log'] ?></td>
          <td>
            <?php if($log['username']): ?>
              <span class="badge <?= $log['role']==='Admin'?'badge-admin':'badge-member' ?>">
                <?= htmlspecialchars($log['username']) ?>
              </span>
            <?php else: ?>
              <span style="color:#bdc3c7;font-size:.76rem">—sistem—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php
            $aksi  = htmlspecialchars($log['aksi']);
            $lower = strtolower($log['aksi']);
            if      (str_contains($lower,'hard delete') || str_contains($lower,'dihapus permanen')) $kls = 'aksi-hard';
            elseif  (str_contains($lower,'soft delete') || str_contains($lower,'hapus'))            $kls = 'aksi-soft';
            elseif  (str_contains($lower,'restore'))                                                $kls = 'aksi-restore';
            elseif  (str_contains($lower,'edit') || str_contains($lower,'mengedit'))               $kls = 'aksi-edit';
            elseif  (str_contains($lower,'tambah') || str_contains($lower,'menambah') || str_contains($lower,'mendaftar')) $kls = 'aksi-tambah';
            elseif  (str_contains($lower,'login'))                                                  $kls = 'aksi-login';
            else    $kls = '';
            ?>
            <span class="<?= $kls ?>"><?= $aksi ?></span>
          </td>
          <td class="ts">
            🕐 <?= date('d M Y', strtotime($log['waktu'])) ?><br>
            <span style="font-size:.72rem"><?= date('H:i:s', strtotime($log['waktu'])) ?></span>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if($count === 0): ?>
        <tr><td colspan="4">
          <div class="empty-state">
            <div class="icon">📭</div>
            <p>Tidak ada log yang cocok dengan filter yang dipilih.</p>
          </div>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pag-info">
      Halaman <?= $page ?> dari <?= max(1,$total_pages) ?> · Total <?= number_format($total_rows) ?> log
    </div>
  </div>

  <!-- Pagination -->
  <?php if($total_pages > 1):
    $qp = array_filter(['q' => $filter_keyword, 'id_user' => $filter_user ?: null, 'tsid' => $tsid]);
    $base_q = '?' . http_build_query($qp);
  ?>
  <div class="pag">
    <a href="audit_log.php<?= $base_q ?>&page=<?= max(1,$page-1) ?>"
       class="plink <?= $page<=1?'disabled':'' ?>">← Prev</a>
    <?php for($i=max(1,$page-3); $i<=min($total_pages,$page+3); $i++): ?>
      <a href="audit_log.php<?= $base_q ?>&page=<?= $i ?>"
         class="plink <?= $i==$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a href="audit_log.php<?= $base_q ?>&page=<?= min($total_pages,$page+1) ?>"
       class="plink <?= $page>=$total_pages?'disabled':'' ?>">Next →</a>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
