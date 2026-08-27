<?php
require 'config/koneksi.php';
$row = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM manajemen_masjid LIMIT 1'));
$page_title = 'Manajemen Masjid';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="manajemen-wrapper">
    <?php if (!$row): ?>
        <div class="manajemen-card"><p>Informasi manajemen belum tersedia.</p></div>
    <?php else: ?>
        <div class="manajemen-hero">
            <h1><?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Pengelolaan masjid yang profesional, transparan, dan berorientasi pada pelayanan jamaah.</p>
        </div>
        <?php foreach ([['fa-building', 'Manajemen Masjid', 'deskripsi'], ['fa-clock-rotate-left', 'Sejarah Manajemen', 'sejarah'], ['fa-users', 'Studi Banding', 'studi_banding']] as [$icon, $title, $field]): ?>
            <div class="manajemen-card">
                <div class="manajemen-title"><i class="fa-solid <?= $icon ?>"></i><h2><?= $title ?></h2></div>
                <div class="manajemen-content"><?= nl2br(htmlspecialchars($row[$field] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
        <?php endforeach; ?>
        <?php $wa = preg_replace('/\D+/', '', preg_replace('/^0/', '62', $row['kontak'] ?? '')); ?>
        <div class="manajemen-card">
            <div class="manajemen-title"><i class="fa-solid fa-phone"></i><h2>Kontak Manajemen</h2></div>
            <div class="kontak-box"><p><?= htmlspecialchars($row['kontak'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p><?php if ($wa): ?><a href="https://wa.me/<?= $wa ?>" target="_blank" rel="noopener" class="btn-wa">Hubungi via WhatsApp</a><?php endif; ?></div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
