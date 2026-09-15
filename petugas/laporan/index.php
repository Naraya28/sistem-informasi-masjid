<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
try {
    $list = laporan_list($conn, (int) $_SESSION['user_id'], $_GET);
    $pendingDeleteMap = delete_approval_pending_map($conn, 'laporan', (int) $_SESSION['user_id'], array_column($list['rows'], 'id'));
} catch (RuntimeException $error) {
    http_response_code(500);
    exit(laporan_escape($error->getMessage()));
}
$title = 'Laporan Saya';
$active = 'laporan';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">LAPORAN PETUGAS</p><h1>Laporan Saya</h1><p><?= (int) $list['total'] ?> laporan yang Anda kelola.</p></div><a class="button primary" href="tambah.php">+ Buat Laporan</a></div>
<section class="panel"><div class="panel-title"><h2>Daftar Laporan</h2><span class="subtle">Gunakan pencarian atau filter untuk menemukan laporan.</span></div>
<form method="GET" class="finance-filters"><div><label for="search">Cari laporan</label><input id="search" name="search" maxlength="200" placeholder="Judul atau deskripsi" value="<?= laporan_escape($list['search']) ?>"></div><div><label for="jenis">Jenis laporan</label><select id="jenis" name="jenis"><option value="">Semua Jenis</option><?php foreach (laporan_jenis() as $item): ?><option value="<?= laporan_escape($item) ?>" <?= $list['jenis'] === $item ? 'selected' : '' ?>><?= laporan_escape($item) ?></option><?php endforeach; ?></select></div><button class="button primary" type="submit">Terapkan</button><a class="button secondary" href="index.php">Reset</a></form>
<?php if (!$list['rows']): ?><div class="empty-state"><h2>Belum ada laporan.</h2><p>Buat laporan pertama untuk mulai mengontrol dokumentasi masjid.</p><a class="button primary" href="tambah.php">Buat Laporan Pertama</a></div><?php else: ?>
<div class="table-responsive"><table><thead><tr><th>No</th><th>Tanggal</th><th>Judul</th><th>Jenis</th><th>Lampiran</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($list['rows'] as $i => $row): $pendingId = (int) (($pendingDeleteMap ?? [])[(int) $row['id']] ?? 0); ?><tr><td><?= $list['offset'] + $i + 1 ?></td><td class="nowrap"><?= laporan_escape(date('d-m-Y', strtotime($row['tanggal']))) ?></td><td class="report-title"><?= laporan_escape($row['judul_laporan']) ?></td><td><span class="type-badge income"><?= laporan_escape($row['jenis_laporan']) ?></span></td><td class="attachment"><?= !empty($row['file']) ? 'Tersedia' : 'Tidak ada' ?></td><td><a class="edit-link" href="detail.php?id=<?= (int) $row['id'] ?>">Detail</a><?php if ($pendingId): ?><span class="approval-badge pending">Menunggu Admin</span><?php else: ?><a class="edit-link" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a><form method="POST" action="<?= laporan_escape(app_url('petugas/laporan/request_hapus.php')) ?>" class="inline-action" onsubmit="return confirm('Ajukan permintaan penghapusan data ini kepada Admin?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="edit-link danger-link" type="submit">Ajukan Hapus</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?>
<nav class="pagination" aria-label="Halaman laporan"><span><?= (int) $list['total'] ?> laporan &middot; Halaman <?= $list['page'] ?> dari <?= $list['pages'] ?></span><?php if ($list['page'] > 1): ?><a href="<?= laporan_escape(laporan_page_url($list, $list['page'] - 1)) ?>">Sebelumnya</a><?php endif; ?><?php for ($p = max(1, $list['page'] - 2); $p <= min($list['pages'], $list['page'] + 2); $p++): ?><a <?= $p === $list['page'] ? 'aria-current="page"' : '' ?> href="<?= laporan_escape(laporan_page_url($list, $p)) ?>"><?= $p ?></a><?php endfor; ?><?php if ($list['page'] < $list['pages']): ?><a href="<?= laporan_escape(laporan_page_url($list, $list['page'] + 1)) ?>">Berikutnya</a><?php endif; ?></nav>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
