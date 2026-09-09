<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/keuangan.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
try {
    $summary = keuangan_summary($conn, (int) $_SESSION['user_id']);
    $list = keuangan_list($conn, (int) $_SESSION['user_id'], $_GET);
    $pendingDeleteMap = delete_approval_pending_map($conn, 'keuangan', (int) $_SESSION['user_id'], array_column($list['rows'], 'id_keuangan'));
} catch (RuntimeException $error) { http_response_code(500); exit(keuangan_escape($error->getMessage())); }
$title = 'Transaksi Keuangan';
$monitoring = false;
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">KEUANGAN SAYA</p><h1>Transaksi Keuangan</h1><p>Kelola pemasukan dan pengeluaran yang Anda catat.</p></div><a class="button primary" href="tambah.php">+ Tambah Transaksi</a></div>
<?php require __DIR__ . '/../includes/stats.php'; ?>
<section class="panel"><div class="panel-title"><h2>Daftar Transaksi</h2><span class="subtle">Catatan keuangan Anda</span></div>
<?php require __DIR__ . '/../../includes/keuangan_filters.php'; require __DIR__ . '/../../includes/keuangan_table.php'; require __DIR__ . '/../../includes/keuangan_pagination.php'; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
