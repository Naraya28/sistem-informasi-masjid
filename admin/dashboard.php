<?php
require 'auth.php';
require '../config/koneksi.php';

$menuMap = [
    'petugas' => 'pengurus/index.php', 'data petugas' => 'pengurus/index.php',
    'artikel' => 'kegiatan/index.php', 'artikel kegiatan' => 'kegiatan/index.php', 'kegiatan' => 'kegiatan/index.php',
    'keuangan' => 'keuangan/index.php', 'donasi' => 'keuangan/index.php',
    'laporan' => 'laporan/index.php', 'pesan' => 'pesan/index.php',
    'profil' => 'profil/index.php', 'manajemen' => 'manajemen/index.php',
];
$search = strtolower(trim($_GET['search'] ?? ''));
if ($search !== '' && isset($menuMap[$search])) {
    header('Location: ' . $menuMap[$search]);
    exit;
}

function scalarQuery(mysqli $conn, string $sql): int {
    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_row($result) : [0];
    return (int) ($row[0] ?? 0);
}

$totalPengurus = scalarQuery($conn, 'SELECT COUNT(*) FROM pengurus');
$totalArtikel = scalarQuery($conn, 'SELECT COUNT(*) FROM artikel');
$totalLaporan = scalarQuery($conn, 'SELECT COUNT(*) FROM laporan');
$totalPesan = scalarQuery($conn, 'SELECT COUNT(*) FROM pesan');
$pemasukan = scalarQuery($conn, "SELECT COALESCE(SUM(jumlah), 0) FROM keuangan WHERE jenis='pemasukan'");
$pengeluaran = scalarQuery($conn, "SELECT COALESCE(SUM(jumlah), 0) FROM keuangan WHERE jenis='pengeluaran'");
$saldo = $pemasukan - $pengeluaran;

$artikelTerbaru = mysqli_query($conn, 'SELECT id_artikel, judul, tanggal FROM artikel ORDER BY tanggal DESC, id_artikel DESC LIMIT 3');
$laporanTerbaru = mysqli_query($conn, 'SELECT id, judul_laporan, jenis_laporan, tanggal FROM laporan ORDER BY tanggal DESC, id DESC LIMIT 3');
$pesanTerbaru = mysqli_query($conn, 'SELECT nama, subjek, pesan, tanggal FROM pesan ORDER BY tanggal DESC, id DESC LIMIT 3');
$statusSaldo = $saldo >= 0 ? 'Sehat' : 'Perlu perhatian';

$page_title = 'Dashboard Admin';
$active_menu = 'dashboard';
$admin_base = '.';
include 'includes/header.php';
?>
<div class="admin-page-header">
    <div><h1>Dashboard</h1><p>Ringkasan data SIM Masjid Al-Firdaus</p></div>
    <form method="GET" class="search-form"><input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari menu..."><button type="submit"><i class="fa fa-search"></i></button></form>
</div>
<?php if ($search !== '' && !isset($menuMap[$search])): ?><div class="alert alert-error">Menu tidak ditemukan.</div><?php endif; ?>
<div class="dashboard-stats">
    <a class="stat-card blue" href="pengurus/index.php"><span class="stat-icon"><i class="fa fa-users"></i></span><span>Total Pengurus</span><strong><?= $totalPengurus ?></strong><small>Lihat data petugas</small></a>
    <a class="stat-card green" href="kegiatan/index.php"><span class="stat-icon"><i class="fa fa-newspaper"></i></span><span>Total Artikel</span><strong><?= $totalArtikel ?></strong><small>Artikel dipublikasikan</small></a>
    <a class="stat-card orange" href="laporan/index.php"><span class="stat-icon"><i class="fa fa-file-lines"></i></span><span>Total Laporan</span><strong><?= $totalLaporan ?></strong><small>Dokumen tersimpan</small></a>
    <a class="stat-card purple" href="pesan/index.php"><span class="stat-icon"><i class="fa fa-envelope"></i></span><span>Total Pesan</span><strong><?= $totalPesan ?></strong><small>Pesan jamaah</small></a>
    <div class="stat-card emerald"><span class="stat-icon"><i class="fa fa-arrow-trend-up"></i></span><span>Total Pemasukan</span><strong>Rp <?= number_format($pemasukan, 0, ',', '.') ?></strong><small>Akumulasi dana masuk</small></div>
    <div class="stat-card red"><span class="stat-icon"><i class="fa fa-arrow-trend-down"></i></span><span>Total Pengeluaran</span><strong>Rp <?= number_format($pengeluaran, 0, ',', '.') ?></strong><small>Akumulasi dana keluar</small></div>
    <div class="stat-card navy"><span class="stat-icon"><i class="fa fa-wallet"></i></span><span>Saldo Akhir</span><strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong><small>Saldo kas saat ini</small></div>
    <div class="stat-card teal"><span class="stat-icon"><i class="fa fa-circle-check"></i></span><span>Status Sistem</span><strong>Aktif</strong><small>Keuangan: <?= $statusSaldo ?></small></div>
</div>

<div class="dashboard-grid">
    <section class="dashboard-panel activity-panel">
        <div class="panel-heading"><div><span class="panel-eyebrow">Update terkini</span><h2>Aktivitas Terbaru</h2></div><i class="fa fa-clock-rotate-left"></i></div>
        <div class="activity-columns">
            <div>
                <h3><i class="fa fa-newspaper"></i> Artikel</h3>
                <?php if (!$artikelTerbaru || mysqli_num_rows($artikelTerbaru) === 0): ?><p class="dashboard-empty">Belum ada artikel.</p><?php endif; ?>
                <?php while ($artikel = $artikelTerbaru ? mysqli_fetch_assoc($artikelTerbaru) : null): ?>
                    <a class="activity-item" href="kegiatan/edit.php?id=<?= (int) $artikel['id_artikel'] ?>"><span><?= htmlspecialchars($artikel['judul'], ENT_QUOTES, 'UTF-8') ?></span><small><?= date('d M Y', strtotime($artikel['tanggal'])) ?></small></a>
                <?php endwhile; ?>
            </div>
            <div>
                <h3><i class="fa fa-file-lines"></i> Laporan</h3>
                <?php if (!$laporanTerbaru || mysqli_num_rows($laporanTerbaru) === 0): ?><p class="dashboard-empty">Belum ada laporan.</p><?php endif; ?>
                <?php while ($laporan = $laporanTerbaru ? mysqli_fetch_assoc($laporanTerbaru) : null): ?>
                    <a class="activity-item" href="laporan/detail.php?id=<?= (int) $laporan['id'] ?>"><span><?= htmlspecialchars($laporan['judul_laporan'], ENT_QUOTES, 'UTF-8') ?></span><small><?= htmlspecialchars($laporan['jenis_laporan'], ENT_QUOTES, 'UTF-8') ?> &middot; <?= date('d M Y', strtotime($laporan['tanggal'])) ?></small></a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="dashboard-panel finance-panel">
        <div class="panel-heading"><div><span class="panel-eyebrow">Kas masjid</span><h2>Ringkasan Keuangan</h2></div><i class="fa fa-chart-pie"></i></div>
        <div class="finance-balance"><span>Saldo tersedia</span><strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong><span class="status-badge <?= $saldo >= 0 ? 'positive' : 'negative' ?>"><?= $statusSaldo ?></span></div>
        <div class="finance-bars">
            <?php $totalArus = max(1, $pemasukan + $pengeluaran); ?>
            <div><div class="bar-label"><span>Pemasukan</span><strong>Rp <?= number_format($pemasukan, 0, ',', '.') ?></strong></div><div class="bar-track"><span class="income" style="width: <?= round(($pemasukan / $totalArus) * 100) ?>%"></span></div></div>
            <div><div class="bar-label"><span>Pengeluaran</span><strong>Rp <?= number_format($pengeluaran, 0, ',', '.') ?></strong></div><div class="bar-track"><span class="expense" style="width: <?= round(($pengeluaran / $totalArus) * 100) ?>%"></span></div></div>
        </div>
        <a class="panel-link" href="keuangan/index.php">Lihat rincian keuangan <i class="fa fa-arrow-right"></i></a>
    </section>

    <section class="dashboard-panel messages-panel">
        <div class="panel-heading"><div><span class="panel-eyebrow">Jamaah</span><h2>Pesan Terbaru</h2></div><a href="pesan/index.php">Lihat semua</a></div>
        <div class="message-list">
            <?php if (!$pesanTerbaru || mysqli_num_rows($pesanTerbaru) === 0): ?><p class="dashboard-empty">Belum ada pesan masuk.</p><?php endif; ?>
            <?php while ($pesan = $pesanTerbaru ? mysqli_fetch_assoc($pesanTerbaru) : null): ?>
                <article class="message-item"><span class="message-avatar"><?= strtoupper(substr($pesan['nama'], 0, 1)) ?></span><div><div class="message-meta"><strong><?= htmlspecialchars($pesan['nama'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= date('d M, H:i', strtotime($pesan['tanggal'])) ?></small></div><h3><?= htmlspecialchars($pesan['subjek'] ?: 'Tanpa subjek', ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars(mb_strimwidth($pesan['pesan'], 0, 90, '...'), ENT_QUOTES, 'UTF-8') ?></p></div></article>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="dashboard-panel actions-panel">
        <div class="panel-heading"><div><span class="panel-eyebrow">Navigasi</span><h2>Akses Cepat</h2></div><i class="fa fa-bolt"></i></div>
        <div class="quick-links">
            <a href="pengurus/tambah.php"><span><i class="fa fa-user-plus"></i></span><div><strong>Tambah Pengurus</strong><small>Lengkapi struktur organisasi</small></div></a>
            <a href="kegiatan/tambah.php"><span><i class="fa fa-pen-to-square"></i></span><div><strong>Tulis Artikel</strong><small>Publikasikan kegiatan baru</small></div></a>
            <a href="keuangan/index.php"><span><i class="fa fa-wallet"></i></span><div><strong>Monitoring Keuangan</strong><small>Lihat transaksi Bendahara</small></div></a>
            <a href="laporan/tambah.php"><span><i class="fa fa-file-circle-plus"></i></span><div><strong>Buat Laporan</strong><small>Simpan laporan terbaru</small></div></a>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
