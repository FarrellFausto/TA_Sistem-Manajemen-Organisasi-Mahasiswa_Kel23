<?php
include '../config/koneksi.php';
require_login('../');

$search         = trim($_GET['cari'] ?? '');
$filter_periode = (int)($_GET['periode'] ?? 0);

// Build query dengan prepared statement
$where  = "a.deleted_at IS NULL";
$params = [];
$types  = "";

if (!empty($search)) {
    $where .= " AND (a.nama_lengkap LIKE ? OR a.nim LIKE ?)";
    $s = "%$search%";
    $params[] = $s;
    $params[] = $s;
    $types .= "ss";
}

if ($filter_periode > 0) {
    $where .= " AND a.id_periode = ?";
    $params[] = $filter_periode;
    $types .= "i";
}

$sql = "SELECT a.id_anggota, a.nama_lengkap, a.nim,
               b.nama_bidang, j.nama_jabatan,
               p.label AS tahun_periode, pr.nama_proker
        FROM anggota a
        LEFT JOIN bidang b ON a.id_bidang = b.id_bidang
        JOIN jabatan j ON a.id_jabatan = j.id_jabatan
        LEFT JOIN periode p ON a.id_periode = p.id_periode
        LEFT JOIN anggota_proker ap ON a.id_anggota = ap.id_anggota
        LEFT JOIN proker pr ON ap.id_proker = pr.id_proker
        WHERE $where
        ORDER BY p.id_periode DESC, a.nama_lengkap ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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

  <!-- LINK CSS -->
  <link rel="stylesheet" href="../assets/css/anggota_tampil.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="main">
  <div class="page-header">
    <h2>👥 Daftar Anggota</h2>

    <?php if($ses_role === 'admin'): ?>
      <a href="<?= tab_url('anggota_tambah.php') ?>" class="btn btn-green">➕ Tambah Anggota</a>
    <?php endif; ?>
  </div>

  <!-- Filter -->
  <form method="GET" class="filter-bar">
    <input type="hidden" name="tsid" value="<?= htmlspecialchars($tsid) ?>">

    <input type="text" name="cari" placeholder="🔍 Cari nama atau NIM..."
           value="<?= htmlspecialchars($search) ?>">

    <select name="periode">
      <option value="0">📅 Semua Periode</option>
      <?php while($p = mysqli_fetch_assoc($all_periode)): ?>
        <option value="<?= $p['id_periode'] ?>" <?= $filter_periode==$p['id_periode']?'selected':'' ?>>
          Periode <?= htmlspecialchars($p['label']) ?>
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
          <th>#</th>
          <th>NIM</th>
          <th>Nama Lengkap</th>
          <th>Bidang</th>
          <th>Jabatan</th>
          <th>Periode</th>
          <th>Proker</th>
          <?php if($ses_role === 'admin'): ?>
            <th>Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>

      <tbody>
        <?php
        $no  = 1;
        $cnt = 0;
        while($row = $res_aktif->fetch_assoc()):
          $cnt++;
        ?>
        <tr>
          <td style="color:#bdc3c7;font-size:.8rem"><?= $no++ ?></td>

          <td>
            <code style="background:#f0f3f8;padding:2px 7px;border-radius:5px;font-size:.8rem">
              <?= htmlspecialchars($row['nim']) ?>
            </code>
          </td>

          <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>

          <td>
            <span class="badge badge-blue"><?= htmlspecialchars($row['nama_bidang'] ?? '-') ?></span>
          </td>

          <td><?= htmlspecialchars($row['nama_jabatan'] ?? '-') ?></td>

          <td>
            <span class="badge badge-green">📅 <?= htmlspecialchars($row['tahun_periode'] ?? '-') ?></span>
          </td>

          <td>
            <?php if(!empty($row['nama_proker'])): ?>
              <span class="badge badge-orange">📋 <?= htmlspecialchars($row['nama_proker']) ?></span>
            <?php else: ?>
              <span class="badge badge-gray">-</span>
            <?php endif; ?>
          </td>

          <?php if($ses_role === 'admin'): ?>
          <td>
            <div class="action-group">

              <!-- FIX EDIT -->
              <a href="<?= tab_url('anggota_edit.php', ['id' => $row['id_anggota']]) ?>"
                 class="btn btn-blue btn-sm">✏️</a>

              <!-- FIX HAPUS (JANGAN PAKAI pages/ lagi karena sudah di folder pages) -->
              <a href="<?= tab_url('anggota_hapus.php', ['id' => $row['id_anggota'], 'type' => 'soft']) ?>"
                 class="btn btn-orange btn-sm"
                 onclick="return confirm('Pindahkan <?= addslashes($row['nama_lengkap']) ?> ke tong sampah?')">🗑️</a>

            </div>
          </td>
          <?php endif; ?>
        </tr>
        <?php endwhile; ?>

        <?php if($cnt === 0): ?>
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <div class="icon">🔍</div>
              <p>Tidak ada anggota ditemukan<?= $search ? " untuk \"".htmlspecialchars($search)."\"" : "" ?></p>
            </div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="pagination-info">Menampilkan <?= $cnt ?> anggota</div>
  </div>

  <!-- Tong Sampah (Admin only) -->
  <?php if($ses_role === 'admin'): ?>
  <div class="trash-section">
    <h3>🗑️ Tong Sampah</h3>

    <div class="table-wrap">
      <table>
        <thead>
          <tr style="background:linear-gradient(135deg,#c0392b,#e74c3c)">
            <th>#</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Periode</th>
            <th>Dihapus</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $trash = mysqli_query($conn,"
              SELECT a.*, p.label AS tahun_periode
              FROM anggota a
              LEFT JOIN periode p ON a.id_periode = p.id_periode
              WHERE a.deleted_at IS NOT NULL
              ORDER BY a.deleted_at DESC
          ");

          $tc = 0;
          while($t = mysqli_fetch_assoc($trash)):
            $tc++;
          ?>
          <tr>
            <td style="color:#bdc3c7;font-size:.8rem"><?= $tc ?></td>

            <td>
              <code style="background:#f0f3f8;padding:2px 7px;border-radius:5px;font-size:.8rem">
                <?= htmlspecialchars($t['nim']) ?>
              </code>
            </td>

            <td><strong><?= htmlspecialchars($t['nama_lengkap']) ?></strong></td>

            <td><?= htmlspecialchars($t['tahun_periode'] ?? '-') ?></td>

            <td style="color:#95a5a6;font-size:.78rem">
              <?= date('d M Y H:i', strtotime($t['deleted_at'])) ?>
            </td>

            <td>
              <div class="action-group">

                <a href="<?= tab_url('anggota_hapus.php', ['id'=>$t['id_anggota'],'type'=>'restore']) ?>"
                   class="btn btn-green btn-sm">♻️ Restore</a>

                <a href="<?= tab_url('anggota_hapus.php', ['id'=>$t['id_anggota'],'type'=>'hard']) ?>"
                   class="btn btn-red btn-sm"
                   onclick="return confirm('HAPUS PERMANEN <?= addslashes($t['nama_lengkap']) ?>?')">💣 Hapus Permanen</a>

              </div>
            </td>
          </tr>
          <?php endwhile; ?>

          <?php if($tc === 0): ?>
          <tr>
            <td colspan="6" style="text-align:center;padding:28px;color:#95a5a6">
              Tong sampah kosong 🎉
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>

</body>
</html>
