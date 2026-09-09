<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/keuangan.php';
try {
    $summary = keuangan_summary($conn, null);
    $list = keuangan_list($conn, null, $_GET);
} catch (RuntimeException $error) { http_response_code(500); exit(keuangan_escape($error->getMessage())); }
$page_title = 'Monitoring Keuangan';
$active_menu = 'keuangan';
$admin_base = '..';
$monitoring = true;
require __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Monitoring Keuangan</h1><p>Ringkasan dan catatan transaksi yang diinput Bendahara.</p></div></div>
<div class="keuangan-cards">
    <div class="keuangan-card masuk"><h3>Pemasukan</h3><p><?= keuangan_rupiah($summary['pemasukan']) ?></p></div>
    <div class="keuangan-card keluar"><h3>Pengeluaran</h3><p><?= keuangan_rupiah($summary['pengeluaran']) ?></p></div>
    <div class="keuangan-card saldo"><h3>Saldo</h3><p><?= keuangan_rupiah($summary['pemasukan']-$summary['pengeluaran']) ?></p></div>
</div>
<div class="content-card">
<?php require __DIR__ . '/../../includes/keuangan_filters.php'; ?>
<?php require __DIR__ . '/../../includes/keuangan_table.php'; ?>
<?php require __DIR__ . '/../../includes/keuangan_pagination.php'; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
