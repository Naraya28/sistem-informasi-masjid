<?php
require __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/keuangan.php';
try {
    $summary = keuangan_summary($conn, (int) $_SESSION['user_id']);
    $list = keuangan_list($conn, (int) $_SESSION['user_id'], [], 5);
} catch (RuntimeException $error) { http_response_code(500); exit(keuangan_escape($error->getMessage())); }
$title = 'Dashboard Bendahara';
$active = 'dashboard';
$monitoring = false;
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">DASHBOARD BENDAHARA</p><h1>Selamat datang,<br><?= keuangan_escape($_SESSION['nama_lengkap']) ?></h1><p>Pantau dan kelola catatan keuangan Anda dalam satu tempat.</p></div><a class="button primary" href="keuangan/tambah.php">+ Tambah Transaksi</a></div>
<?php require __DIR__ . '/includes/stats.php'; ?>
<section class="panel"><div class="panel-title"><div><h2>Transaksi Terbaru</h2><p>Lima catatan terbaru berdasarkan tanggal transaksi.</p></div><a class="text-link" href="keuangan/index.php">Lihat Semua Transaksi <span aria-hidden="true">→</span></a></div>
<?php require __DIR__ . '/../includes/keuangan_table.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
