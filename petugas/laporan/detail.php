<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$rows = laporan_rows($conn, 'SELECT l.*, u.nama_lengkap AS creator FROM laporan l LEFT JOIN users u ON u.id=l.created_by WHERE l.id=? AND l.created_by=?', 'ii', [$id, (int) $_SESSION['user_id']]);
if (!$rows) { http_response_code(404); exit('Laporan tidak ditemukan atau tidak dapat diakses.'); }
$data = $rows[0];
$title = 'Detail Laporan';
$active = 'laporan';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">DETAIL LAPORAN</p><h1><?= laporan_escape($data['judul_laporan']) ?></h1><div class="detail-meta"><span><?= laporan_escape(date('d-m-Y', strtotime($data['tanggal']))) ?></span><span><?= laporan_escape($data['jenis_laporan']) ?></span><span><?= laporan_escape($data['creator'] ?? 'Petugas Masjid') ?></span></div></div><a class="button primary" href="edit.php?id=<?= (int) $data['id'] ?>">Edit Laporan</a></div>
<section class="panel detail-panel"><h2>Deskripsi</h2><div class="detail-description"><?= laporan_escape($data['deskripsi'] ?? 'Tidak ada deskripsi.') ?></div><?php if (!empty($data['file'])): $fileName = basename($data['file']); ?><p><a class="button secondary" target="_blank" rel="noopener" href="<?= laporan_escape(app_url('uploads/laporan/' . rawurlencode($fileName))) ?>"><i class="fa-solid fa-paperclip" aria-hidden="true"></i> Buka Lampiran</a></p><?php else: ?><p class="subtle">Tidak ada lampiran.</p><?php endif; ?><a class="button secondary" href="index.php">Kembali ke Daftar</a></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>