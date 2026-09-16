<?php
require '../auth.php';
require '../../config/koneksi.php';
require_once '../../includes/csrf.php';
$listJabatan = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Humas', 'Keamanan'];

function badgeJabatanClass(string $jabatan): string {
    $slug = strtolower(trim($jabatan));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    $allowed = ['ketua', 'wakil-ketua', 'sekretaris', 'bendahara', 'humas', 'keamanan', 'anggota', 'pengurus'];

    return in_array($slug, $allowed, true) ? 'badge-' . $slug : 'badge-default';
}

$jabatan = trim($_GET['jabatan'] ?? '');
if (!in_array($jabatan, $listJabatan, true)) $jabatan = '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 5;
$start = ($page - 1) * $limit;
$where = 'WHERE deleted_at IS NULL';
if ($jabatan !== '') $where .= " AND jabatan='" . mysqli_real_escape_string($conn, $jabatan) . "'";
$total = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM pengurus $where"))['total'] ?? 0);
$totalPages = max(1, (int) ceil($total / $limit));
$data = mysqli_query($conn, "SELECT * FROM pengurus $where ORDER BY FIELD(jabatan,'Ketua','Wakil Ketua','Sekretaris','Bendahara','Humas','Keamanan'), nama ASC LIMIT $start,$limit");
$page_title = 'Data Pengurus'; $active_menu = 'pengurus'; $admin_base = '..';
include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Data Pengurus</h1><p>Kelola struktur pengurus Masjid Al-Firdaus</p></div><a href="tambah.php" class="btn-tambah"><i class="fa fa-plus"></i> Tambah Pengurus</a></div>
<div class="content-card">
<form method="GET" class="filter-bar compact-filter admin-filter-bar pengurus-filter"><select class="admin-filter-select" name="jabatan" aria-label="Filter jabatan"><option value="">Semua Jabatan</option><?php foreach ($listJabatan as $item): ?><option value="<?= $item ?>" <?= $jabatan === $item ? 'selected' : '' ?>><?= $item ?></option><?php endforeach; ?></select><button class="btn admin-filter-action" type="submit"><i class="fa fa-filter"></i> Filter</button></form>
<div class="table-responsive"><table><thead><tr><th>No</th><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Deskripsi</th><th>Aksi</th></tr></thead><tbody>
<?php if (mysqli_num_rows($data) === 0): ?><tr><td colspan="6"><div class="table-empty"><i class="fa fa-users"></i><strong>Data pengurus belum tersedia</strong><span>Data yang tersimpan akan tampil pada tabel ini.</span></div></td></tr><?php endif; ?>
<?php $no=$start+1; while ($row=mysqli_fetch_assoc($data)): ?><tr><td><?= $no++ ?></td><td><img class="table-photo" src="../../uploads/pengurus/<?= rawurlencode($row['foto']) ?>" onerror="this.src='../../assets/img/default_user.png'" alt=""></td><td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td><td><span class="badge-jabatan <?= badgeJabatanClass($row['jabatan']) ?>"><?= htmlspecialchars($row['jabatan'], ENT_QUOTES, 'UTF-8') ?></span></td><td><?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?></td><td><a class="btn-edit" href="edit.php?id=<?= (int)$row['id'] ?>">Edit</a> <form method="POST" action="hapus.php" class="inline-action" onsubmit="return confirm('Data akan dipindahkan ke Tempat Sampah dan masih dapat dipulihkan.')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button class="btn-hapus" type="submit">Hapus</button></form></td></tr><?php endwhile; ?>
</tbody></table></div><div class="pagination"><?php for($i=1;$i<=$totalPages;$i++): ?><a class="<?= $i===$page?'active-page':'' ?>" href="?page=<?= $i ?>&jabatan=<?= urlencode($jabatan) ?>"><?= $i ?></a><?php endfor; ?></div></div>
<?php include '../includes/footer.php'; ?>
