<?php
include '../config/koneksi.php';
require_login('../');

$search         = trim($_GET['cari'] ?? '');
$filter_periode = (int)($_GET['periode'] ?? 0);

// Build query dengan prepared statement
$where  = "a.deleted_at IS NULL";
$params = []; $types = "";

if (!empty($search)) {
    $where .= " AND (a.nama_lengkap LIKE ? OR a.nim LIKE ?)";
    $s = "%$search%"; $params[] = $s; $params[] = $s; $types .= "ss";
}
if ($filter_periode > 0) {
    $where .= " AND a.id_periode = ?";
    $params[] = $filter_periode; $types .= "i";
}

$sql = "SELECT a.id_anggota, a.nama_lengkap, a.nim,
               b.nama_bidang, j.nama_jabatan,
               p.tahun_periode, pk.nama_proker
        FROM anggota a
        JOIN bidang  b  ON a.id_bidang  = b.id_bidang
        JOIN jabatan j  ON a.id_jabatan = j.id_jabatan
        LEFT JOIN periode      p  ON a.id_periode  = p.id_periode
        LEFT JOIN tugas_proker tp ON a.id_anggota  = tp.id_anggota
        LEFT JOIN proker       pk ON tp.id_proker  = pk.id_proker
        WHERE $where ORDER BY p.id_periode DESC, a.nama_lengkap ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res_aktif = $stmt->get_result();

$all_periode = mysqli_query($conn, "SELECT * FROM periode ORDER BY id_periode DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Data Anggota — B-ORG</title>
  <style>
    body{margin:0;background:#f0f3f8}
    .main{padding:28px;max-width:1300px;margin:auto}
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px}
    .page-header h2{margin:0;color:#2c3e50;font-size:1.4rem}
    .btn{padding:9px 18px;border-radius:8px;text-decoration:none;font-weight:600;font-size:.85rem;border:none;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:6px}
    .btn-green{background:#27ae60;color:white}.btn-green:hover{background:#229954}
    .btn-red{background:#e74c3c;color:white}.btn-red:hover{background:#c0392b}
    .btn-blue{background:#3498db;color:white}.btn-blue:hover{background:#2980b9}
    .btn-orange{background:#f39c12;color:white}.btn-orange:hover{background:#e67e22}
    .btn-gray{background:#95a5a6;color:white}.btn-gray:hover{background:#7f8c8d}
    .btn-sm{padding:6px 12px;font-size:.78rem}
    .filter-bar{background:white;padding:14px 18px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);margin-bottom:18px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .filter-bar input,.filter-bar select{padding:9px 13px;border:2px solid #e8eaed;border-radius:8px;font-size:.88rem;min-width:160px;transition:.3s}
    .filter-bar input:focus,.filter-bar select:focus{outline:none;border-color:#3498db}
    .table-wrap{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead tr{background:linear-gradient(135deg,#2c3e50,#34495e)}
    th{padding:13px 14px;color:white;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;text-align:left}
    td{padding:12px 14px;border-bottom:1px solid #f0f3f8;font-size:.85rem;color:#2c3e50;vertical-align:middle}
    tr:last-child td{border-bottom:none}
    tbody tr:hover{background:#f8fbff}
    .badge{padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;white-space:nowrap;display:inline-block}
    .badge-blue{background:#e8f4fd;color:#2980b9}.badge-green{background:#eafaf1;color:#27ae60}
    .badge-orange{background:#fef9e7;color:#e67e22}.badge-gray{background:#f2f3f4;color:#7f8c8d}
    .action-group{display:flex;gap:5px;flex-wrap:wrap}
    .empty-state{text-align:center;padding:50px;color:#95a5a6}
    .empty-state .icon{font-size:3rem;margin-bottom:10px}
    .pagination-info{padding:12px 14px;font-size:.8rem;color:#7f8c8d;border-top:1px solid #f0f3f8;background:#fafbfc}
    .trash-section{margin-top:36px}
    .trash-section h3{color:#e74c3c;font-size:1.05rem;margin:0 0 12px;display:flex;align-items:center;gap:8px}
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="main">
  <div class="page-header">
    <h2>👥 Daftar Anggota</h2>
    <?php if($ses_role === 'Admin'): ?>
      <a href="<?= tab_url('anggota_tambah.php') ?>" class="btn btn-green">➕ Tambah Anggota</a>
    <?php endif; ?>
  </div>

  <!-- Filter -->
  <form method="GET" class="filter-bar">
    <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">
    <input type="text" name="cari" placeholder="🔍 Cari nama atau NIM..." value="<?= htmlspecialchars($search) ?>">
    <select name="periode">
      <option value="0">📅 Semua Periode</option>
      <?php while($p = mysqli_fetch_assoc($all_periode)): ?>
        <option value="<?= $p['id_periode'] ?>" <?= $filter_periode==$p['id_periode']?'selected':'' ?>>
          Periode <?= htmlspecialchars($p['tahun_periode']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-blue btn-sm">Filter</button>
    <?php if(!empty($search) || $filter_periode > 0): ?>
      <a href="<?= tab_url('anggota_tampil.php') ?>" class="btn btn-gray btn-sm">✕ Reset</a>
    <?php endif; ?>
  </form>

  <!-- Tabel -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th><th>NIM</th><th>Nama Lengkap</th><th>Bidang</th><th>Jabatan</th>
          <th>Periode</th><th>Proker</th>
          <?php if($ses_role === 'Admin'): ?><th>Aksi</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; $cnt=0; while($row=$res_aktif->fetch_assoc()): $cnt++; ?>
        <tr>
          <td style="color:#bdc3c7;font-size:.8rem"><?= $no++ ?></td>
          <td><code style="background:#f0f3f8;padding:2px 7px;border-radius:5px;font-size:.8rem"><?= htmlspecialchars($row['nim']) ?></code></td>
          <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
          <td><span class="badge badge-blue"><?= htmlspecialchars($row['nama_bidang']) ?></span></td>
          <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
          <td><span class="badge badge-green">📅 <?= htmlspecialchars($row['tahun_periode'] ?? '-') ?></span></td>
          <td><?php if($row['nama_proker']): ?>
            <span class="badge badge-orange">📋 <?= htmlspecialchars($row['nama_proker']) ?></span>
          <?php else: ?><span class="badge badge-gray">-</span><?php endif; ?></td>
          <?php if($ses_role === 'Admin'): ?>
          <td>
            <div class="action-group">
              <a href="<?= tab_url('anggota_edit.php', ['id' => $row['id_anggota']]) ?>" class="btn btn-blue btn-sm">✏️</a>
              <a href="<?= tab_url('anggota_hapus.php', ['id' => $row['id_anggota'], 'type' => 'soft']) ?>"
                 class="btn btn-orange btn-sm"
                 onclick="return confirm('Pindahkan <?= addslashes($row['nama_lengkap']) ?> ke tong sampah?')">🗑️</a>
            </div>
          </td>
          <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        <?php if($cnt === 0): ?>
        <tr><td colspan="8">
          <div class="empty-state"><div class="icon">🔍</div>
            <p>Tidak ada anggota ditemukan<?= $search ? " untuk \"".htmlspecialchars($search)."\"" : "" ?></p>
          </div>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="pagination-info">Menampilkan <?= $cnt ?> anggota</div>
  </div>

  <!-- Tong Sampah (Admin only) -->
  <?php if($ses_role === 'Admin'): ?>
  <div class="trash-section">
    <h3>🗑️ Tong Sampah</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr style="background:linear-gradient(135deg,#c0392b,#e74c3c)">
            <th>#</th><th>NIM</th><th>Nama</th><th>Periode</th><th>Dihapus</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $trash = mysqli_query($conn,"SELECT a.*,p.tahun_periode FROM anggota a LEFT JOIN periode p ON a.id_periode=p.id_periode WHERE a.deleted_at IS NOT NULL ORDER BY a.deleted_at DESC");
          $tc=0;
          while($t=mysqli_fetch_assoc($trash)): $tc++;
          ?>
          <tr>
            <td style="color:#bdc3c7;font-size:.8rem"><?= $tc ?></td>
            <td><code style="background:#f0f3f8;padding:2px 7px;border-radius:5px;font-size:.8rem"><?= htmlspecialchars($t['nim']) ?></code></td>
            <td><strong><?= htmlspecialchars($t['nama_lengkap']) ?></strong></td>
            <td><?= htmlspecialchars($t['tahun_periode'] ?? '-') ?></td>
            <td style="color:#95a5a6;font-size:.78rem"><?= date('d M Y H:i',strtotime($t['deleted_at'])) ?></td>
            <td>
              <div class="action-group">
                <a href="<?= tab_url('anggota_hapus.php', ['id'=>$t['id_anggota'],'type'=>'restore']) ?>" class="btn btn-green btn-sm">♻️ Restore</a>
                <a href="<?= tab_url('anggota_hapus.php', ['id'=>$t['id_anggota'],'type'=>'hard']) ?>"
                   class="btn btn-red btn-sm"
                   onclick="return confirm('HAPUS PERMANEN <?= addslashes($t['nama_lengkap']) ?>?')">💣 Hapus Permanen</a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if($tc===0): ?>
          <tr><td colspan="6" style="text-align:center;padding:28px;color:#95a5a6">Tong sampah kosong 🎉</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
