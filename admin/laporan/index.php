<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
try {
    $list = laporan_list($conn, null, $_GET);
    $pendingDeleteMap = delete_approval_pending_map($conn, 'laporan', 0, array_column($list['rows'], 'id'));
} catch (RuntimeException $error) {
    http_response_code(500);
    exit(laporan_escape($error->getMessage()));
}
$page_title = 'Monitoring Laporan';
$active_menu = 'laporan';
$admin_base = '..';
require __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Monitoring Laporan</h1><p><?= (int) $list['total'] ?> laporan dari Petugas Masjid.</p></div></div>
<div class="content-card"><form method="GET" class="filter-bar monitoring-filter admin-filter-bar"><input class="admin-filter-search" name="search" maxlength="200" value="<?= laporan_escape($list['search']) ?>" placeholder="Cari judul atau deskripsi..."><select class="admin-filter-select" name="jenis"><option value="">Semua Jenis</option><?php foreach (laporan_jenis() as $item): ?><option value="<?= laporan_escape($item) ?>" <?= $list['jenis'] === $item ? 'selected' : '' ?>><?= laporan_escape($item) ?></option><?php endforeach; ?></select><div class="admin-filter-actions"><button class="btn" type="submit"><i class="fa fa-filter"></i> Filter</button><a class="btn-secondary-ui" href="index.php">Reset</a></div></form>
<div class="table-meta"><strong>Data laporan</strong><span><?= (int) $list['total'] ?> laporan &bull; Halaman <?= $list['page'] ?>/<?= $list['pages'] ?></span></div>
<div class="table-responsive"><table><thead><tr><th>No</th><th>Tanggal</th><th>Judul</th><th>Jenis</th><th>Diinput Oleh</th><th>Lampiran</th><th>Aksi</th></tr></thead><tbody>
<?php if (!$list['rows']): ?><tr><td colspan="7"><div class="table-empty"><i class="fa fa-file-circle-xmark"></i><strong>Laporan tidak ditemukan</strong><span>Laporan dari Petugas Masjid akan tampil pada tabel ini.</span></div></td></tr><?php else: foreach ($list['rows'] as $i => $row): ?><tr><td><?= $list['offset'] + $i + 1 ?></td><td><?= laporan_escape(date('d-m-Y', strtotime($row['tanggal']))) ?></td><td><strong><?= laporan_escape($row['judul_laporan']) ?></strong></td><td><span class="badge"><?= laporan_escape($row['jenis_laporan']) ?></span></td><td><span class="creator-identity"><i class="fa fa-user"></i><?= laporan_escape($row['creator'] ?? 'Data Lama') ?></span></td><td><span class="attachment-status <?= !empty($row['file']) ? 'available' : '' ?>"><i class="fa <?= !empty($row['file']) ? 'fa-paperclip' : 'fa-minus' ?>"></i><?= !empty($row['file']) ? 'Tersedia' : 'Tidak ada' ?></span></td><td><a class="btn-detail" href="detail.php?id=<?= (int) $row['id'] ?>">Detail</a><?php if (isset($pendingDeleteMap[(int) $row['id']])): ?> <span class="approval-badge pending">Menunggu Persetujuan Hapus</span><form method="POST" action="<?= laporan_escape(app_url('admin/laporan/approve_hapus.php')) ?>" class="inline-action"><?= csrf_field() ?><input type="hidden" name="request_id" value="<?= (int) $pendingDeleteMap[(int) $row['id']] ?>"><button class="btn-small approve" type="submit">Setujui</button></form><form method="POST" action="<?= laporan_escape(app_url('admin/laporan/reject_hapus.php')) ?>" class="inline-action"><?= csrf_field() ?><input type="hidden" name="request_id" value="<?= (int) $pendingDeleteMap[(int) $row['id']] ?>"><button class="btn-small reject" type="submit">Tolak</button></form><?php endif; ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div><?php if ($list['pages'] > 1): ?><div class="pagination"><?php for ($p = 1; $p <= $list['pages']; $p++): ?><a class="<?= $p === $list['page'] ? 'active-page' : '' ?>" href="<?= laporan_escape(laporan_page_url($list, $p)) ?>"><?= $p ?></a><?php endfor; ?></div><?php endif; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
