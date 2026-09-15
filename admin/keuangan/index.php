<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/keuangan.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
try {
    $summary = keuangan_summary($conn, null);
    $list = keuangan_list($conn, null, $_GET);
    $pendingDeleteMap = delete_approval_pending_map($conn, 'keuangan', 0, array_column($list['rows'], 'id_keuangan'));
} catch (RuntimeException $error) { http_response_code(500); exit(keuangan_escape($error->getMessage())); }
$page_title = 'Monitoring Keuangan';
$active_menu = 'keuangan';
$admin_base = '..';
$monitoring = true;
require __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Monitoring Keuangan</h1><p>Ringkasan dan catatan transaksi yang diinput Bendahara.</p></div></div>
<div class="keuangan-cards">
    <div class="keuangan-card masuk"><span class="finance-icon"><i class="fa fa-arrow-trend-up"></i></span><div><h3>Pemasukan</h3><p><?= keuangan_rupiah($summary['pemasukan']) ?></p></div></div>
    <div class="keuangan-card keluar"><span class="finance-icon"><i class="fa fa-arrow-trend-down"></i></span><div><h3>Pengeluaran</h3><p><?= keuangan_rupiah($summary['pengeluaran']) ?></p></div></div>
    <div class="keuangan-card saldo"><span class="finance-icon"><i class="fa fa-wallet"></i></span><div><h3>Saldo</h3><p><?= keuangan_rupiah($summary['pemasukan']-$summary['pengeluaran']) ?></p></div></div>
</div>
<div class="content-card">
<?php require __DIR__ . '/../../includes/keuangan_filters.php'; ?>
<?php require __DIR__ . '/../../includes/keuangan_table.php'; ?>
<?php if ($list['total'] > 0) require __DIR__ . '/../../includes/keuangan_pagination.php'; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
