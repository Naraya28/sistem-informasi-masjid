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
<div class="page-heading"><div><p class="eyebrow">DASHBOARD BENDAHARA</p><h1>Dashboard Keuangan</h1><p>Ringkasan pengelolaan keuangan Masjid Al-Firdaus.</p></div><a class="button primary" href="keuangan/tambah.php"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah Transaksi</a></div>
<section class="welcome-strip"><span class="welcome-icon"><i class="fa-solid fa-hand-sparkles" aria-hidden="true"></i></span><div><small>SELAMAT DATANG</small><strong><?= keuangan_escape($_SESSION['nama_lengkap']) ?></strong><p>Kelola pencatatan kas secara tertib dan transparan.</p></div></section>
<?php require __DIR__ . '/includes/stats.php'; ?>
<div class="dashboard-finance-grid">
    <section class="panel recent-panel"><div class="panel-title"><div><p class="panel-kicker">AKTIVITAS TERBARU</p><h2>Transaksi Terbaru</h2><p>Lima catatan terbaru berdasarkan tanggal transaksi.</p></div><a class="text-link" href="keuangan/index.php">Lihat semua <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    <?php require __DIR__ . '/../includes/keuangan_table.php'; ?>
    </section>
    <aside class="panel cash-overview"><div class="panel-title"><div><p class="panel-kicker">RINGKASAN KAS</p><h2>Posisi Keuangan</h2><p>Nilai dari seluruh transaksi Anda.</p></div></div>
        <div class="cash-overview-body">
            <div><span><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i> Pemasukan</span><strong><?= keuangan_rupiah($summary['pemasukan']) ?></strong></div>
            <div><span><i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i> Pengeluaran</span><strong><?= keuangan_rupiah($summary['pengeluaran']) ?></strong></div>
            <div class="cash-balance"><span>Saldo tercatat</span><strong><?= keuangan_rupiah($summary['pemasukan'] - $summary['pengeluaran']) ?></strong></div>
        </div>
        <a class="cash-overview-link" href="keuangan/index.php"><i class="fa-solid fa-wallet" aria-hidden="true"></i> Buka workspace keuangan</a>
    </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
