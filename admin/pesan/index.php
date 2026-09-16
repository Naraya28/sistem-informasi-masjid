<?php
require '../auth.php';
require '../../config/koneksi.php';
require_once '../../includes/csrf.php';

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = 'WHERE deleted_at IS NULL';
if ($search !== '') {
    $escaped = mysqli_real_escape_string($conn, $search);
    $where .= " AND (nama LIKE '%$escaped%' OR email LIKE '%$escaped%' OR subjek LIKE '%$escaped%' OR pesan LIKE '%$escaped%')";
}
$total = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM pesan $where"))['total'] ?? 0);
$totalPages = max(1, (int) ceil($total / $limit));
$data = mysqli_query($conn, "SELECT * FROM pesan $where ORDER BY tanggal DESC LIMIT $offset, $limit");

$page_title = 'Pesan Masuk';
$active_menu = 'pesan';
$admin_base = '..';
include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Pesan Masuk</h1><p><?= $total ?> pesan dari pengunjung website</p></div></div>
<div class="content-card">
    <form method="GET" class="filter-bar message-filter admin-filter-bar"><input class="admin-filter-search" type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari nama, email, subjek..."><button type="submit" class="btn admin-filter-action"><i class="fa fa-search"></i> Cari</button></form>
    <div class="table-responsive"><table><thead><tr><th>Tanggal</th><th>Pengirim</th><th>Kontak</th><th>Subjek dan Pesan</th><th>Aksi</th></tr></thead><tbody>
    <?php if (mysqli_num_rows($data) === 0): ?><tr><td colspan="5"><div class="table-empty"><i class="fa fa-envelope-open"></i><strong>Belum ada pesan masuk</strong><span>Pesan dari pengunjung website akan tampil di sini.</span></div></td></tr><?php endif; ?>
    <?php while ($row = mysqli_fetch_assoc($data)): ?><tr>
        <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
        <td><strong><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></strong></td>
        <td><?= htmlspecialchars($row['email'] ?: '-', ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars($row['no_hp'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td><strong><?= htmlspecialchars($row['subjek'] ?: 'Tanpa subjek', ENT_QUOTES, 'UTF-8') ?></strong><br><?= nl2br(htmlspecialchars($row['pesan'], ENT_QUOTES, 'UTF-8')) ?></td>
        <td><form method="POST" action="hapus.php" class="inline-action" onsubmit="return confirm('Data akan dipindahkan ke Tempat Sampah dan masih dapat dipulihkan.')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn-hapus" type="submit">Hapus</button></form></td>
    </tr><?php endwhile; ?></tbody></table></div>
    <?php if ($total > 0): ?><div class="pagination"><?php for ($i = 1; $i <= $totalPages; $i++): ?><a class="<?= $i === $page ? 'active-page' : '' ?>" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
