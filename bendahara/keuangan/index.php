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
<div class="page-heading"><div><p class="eyebrow">WORKSPACE BENDAHARA</p><h1>Keuangan Masjid</h1><p>Catat dan kelola transaksi pemasukan serta pengeluaran.</p></div><a class="button primary" href="tambah.php"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah Transaksi</a></div>
<?php require __DIR__ . '/../includes/stats.php'; ?>
<section class="panel transaction-workspace"><div class="panel-title"><div><p class="panel-kicker">PENCATATAN KAS</p><h2>Daftar Transaksi</h2><p>Telusuri dan kelola catatan keuangan Anda.</p></div></div>
<?php require __DIR__ . '/../../includes/keuangan_filters.php'; ?>
<div class="finance-table-meta"><strong>Data transaksi</strong><span><?= (int) $list['total'] ?> transaksi &bull; Halaman <?= $list['page'] ?>/<?= $list['pages'] ?></span></div>
<?php require __DIR__ . '/../../includes/keuangan_table.php'; if ($list['pages'] > 1) { require __DIR__ . '/../../includes/keuangan_pagination.php'; } ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
