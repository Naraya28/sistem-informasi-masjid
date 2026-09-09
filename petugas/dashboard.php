<?php
require __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/laporan.php';
try {
	$summary = laporan_summary($conn, (int) $_SESSION['user_id']);
	$recent = laporan_list($conn, (int) $_SESSION['user_id'], [], 5);
} catch (RuntimeException $error) {
	http_response_code(500);
	exit(laporan_escape($error->getMessage()));
}
$title = 'Dashboard Petugas Masjid';
$active = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">DASHBOARD PETUGAS MASJID</p><h1>Selamat datang,<br><?= laporan_escape($_SESSION['nama_lengkap']) ?></h1><p>Kontrol dan kelola laporan masjid Anda dalam satu tempat.</p></div><a class="button primary" href="laporan/tambah.php">+ Buat Laporan</a></div>
<?php require __DIR__ . '/includes/stats.php'; ?>
<section class="panel"><div class="panel-title"><div><h2>Laporan Terbaru</h2><p>Lima laporan terbaru yang Anda buat.</p></div><a class="text-link" href="laporan/index.php">Lihat Semua Laporan <span aria-hidden="true">→</span></a></div>
<?php if (!$recent['rows']): ?><div class="empty-state"><h2>Belum ada laporan.</h2><p>Mulai dokumentasikan kegiatan masjid Anda.</p><a class="button primary" href="laporan/tambah.php">Buat Laporan Pertama</a></div><?php else: ?><div class="table-responsive"><table><thead><tr><th>Tanggal</th><th>Judul</th><th>Jenis</th><th>Lampiran</th><th>Aksi</th></tr></thead><tbody><?php foreach ($recent['rows'] as $row): ?><tr><td class="nowrap"><?= laporan_escape(date('d-m-Y', strtotime($row['tanggal']))) ?></td><td class="report-title"><?= laporan_escape($row['judul_laporan']) ?></td><td><span class="type-badge income"><?= laporan_escape($row['jenis_laporan']) ?></span></td><td class="attachment"><?= !empty($row['file']) ? 'Tersedia' : 'Tidak ada' ?></td><td><a class="edit-link" href="laporan/detail.php?id=<?= (int) $row['id'] ?>">Detail</a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
