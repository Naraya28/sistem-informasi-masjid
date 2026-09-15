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
        <section class="management-intro-grid">
            <div class="manajemen-card management-intro-copy">
                <p class="section-eyebrow">Tentang Pengelolaan</p>
                <div class="manajemen-title"><i class="fa-solid fa-building"></i><h2>Manajemen Masjid</h2></div>
                <div class="manajemen-content"><?= nl2br(htmlspecialchars($row['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <aside class="management-principles" aria-label="Tiga prinsip manajemen">
                <p class="section-eyebrow">Tiga Fokus</p>
                <h2>Prinsip Manajemen</h2>
                <div class="principle-list">
                    <div><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i><span>Pemetaan</span></div>
                    <div><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i><span>Pelayanan</span></div>
                    <div><i class="fa-solid fa-people-group" aria-hidden="true"></i><span>Pemberdayaan</span></div>
                </div>
            </aside>
        </section>

        <section class="manajemen-card management-history">
            <div class="management-section-label"><span>01</span><p>Perjalanan Pengelolaan</p></div>
            <div>
                <div class="manajemen-title"><i class="fa-solid fa-clock-rotate-left"></i><h2>Sejarah Manajemen</h2></div>
                <div class="manajemen-content"><?= nl2br(htmlspecialchars($row['sejarah'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
        </section>

        <?php
        $studyText = (string) ($row['studi_banding'] ?? '');
        preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $studyText, $studyEmailMatch);
        $studyEmail = $studyEmailMatch[0] ?? '';
        ?>
        <section class="manajemen-card management-study">
            <div>
                <div class="manajemen-title"><i class="fa-solid fa-users"></i><h2>Studi Banding</h2></div>
                <div class="manajemen-content"><?= nl2br(htmlspecialchars($studyText, ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <?php if ($studyEmail !== ''): ?>
                <aside class="study-contact">
                    <i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i>
                    <span>Kontak Studi Banding</span>
                    <a href="mailto:<?= htmlspecialchars($studyEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($studyEmail, ENT_QUOTES, 'UTF-8') ?></a>
                </aside>
            <?php endif; ?>
        </section>

        <?php $wa = preg_replace('/\D+/', '', preg_replace('/^0/', '62', $row['kontak'] ?? '')); ?>
        <aside class="management-contact">
            <div><i class="fa-solid fa-phone" aria-hidden="true"></i><span>Kontak Manajemen</span><strong><?= htmlspecialchars($row['kontak'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></div>
            <?php if ($wa): ?><a href="https://wa.me/<?= $wa ?>" target="_blank" rel="noopener" class="btn-wa">Hubungi via WhatsApp</a><?php endif; ?>
        </aside>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
