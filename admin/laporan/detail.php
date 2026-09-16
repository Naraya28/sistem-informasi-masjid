<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$rows = laporan_rows($conn, 'SELECT l.*, u.nama_lengkap AS creator FROM laporan l LEFT JOIN users u ON u.id=l.created_by WHERE l.id=? AND l.deleted_at IS NULL', 'i', [$id]);
if (!$rows) { http_response_code(404); exit('Laporan tidak ditemukan.'); }
$data = $rows[0];
$page_title = 'Detail Laporan';
$active_menu = 'laporan';
$admin_base = '..';
require __DIR__ . '/../includes/header.php';
?>
<div class="detail-card"><div class="admin-page-header"><div><h1><?= laporan_escape($data['judul_laporan']) ?></h1><p><?= laporan_escape(date('d-m-Y', strtotime($data['tanggal']))) ?> | <?= laporan_escape($data['creator'] ?? 'Data Lama') ?></p></div><span class="badge"><?= laporan_escape($data['jenis_laporan']) ?></span></div><div class="deskripsi-box"><?= nl2br(laporan_escape($data['deskripsi'] ?? 'Tidak ada deskripsi.')) ?></div><?php if (!empty($data['file'])): $fileName = basename($data['file']); ?><p><a class="btn-detail" target="_blank" rel="noopener" href="<?= laporan_escape(app_url('uploads/laporan/' . rawurlencode($fileName))) ?>">Buka Lampiran</a></p><?php endif; ?><div class="detail-actions"><a class="btn-kembali" href="index.php">Kembali</a></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
