<?php
require 'config/koneksi.php';
$profil = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM profil_masjid LIMIT 1'));
$page_title = 'Profil Masjid';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="profil-wrapper">
    <?php if (!$profil): ?>
        <div class="profil-card"><p>Profil masjid belum tersedia.</p></div>
    <?php else: ?>
        <div class="profil-hero">
            <h1><?= htmlspecialchars($profil['judul'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Selamat datang di website resmi Masjid Al-Firdaus, pusat ibadah, dakwah, pendidikan, dan pelayanan umat.</p>
        </div>
        <section class="profil-card profile-overview">
            <div>
                <p class="section-eyebrow">Mengenal Al-Firdaus</p>
                <div class="profil-title"><i class="fa-solid fa-mosque"></i><h2>Profil Singkat</h2></div>
                <div class="profil-content"><?= nl2br(htmlspecialchars($profil['profil_singkat'], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <div class="profile-overview-mark" aria-hidden="true"><i class="fa-solid fa-mosque"></i></div>
        </section>

        <div class="profile-duo">
            <section class="profil-card profile-origin">
                <div class="profil-title"><i class="fa-solid fa-book-open"></i><h2>Asal Nama Masjid</h2></div>
                <div class="profil-content"><?= nl2br(htmlspecialchars($profil['asal_nama'], ENT_QUOTES, 'UTF-8')) ?></div>
            </section>
            <section class="profil-card profile-vision">
                <div class="profil-title"><i class="fa-solid fa-eye"></i><h2>Visi Masjid</h2></div>
                <div class="profil-content"><?= nl2br(htmlspecialchars($profil['visi'], ENT_QUOTES, 'UTF-8')) ?></div>
            </section>
        </div>

        <?php
        $missionItems = preg_split('/\R+/', trim((string) $profil['misi'])) ?: [];
        $missionItems = array_values(array_filter(array_map('trim', $missionItems), static fn($item) => $item !== ''));
        ?>
        <section class="profil-card profile-mission">
            <div class="profil-title"><i class="fa-solid fa-bullseye"></i><h2>Misi Masjid</h2></div>
            <ol class="mission-grid">
                <?php foreach ($missionItems as $index => $missionItem): ?>
                    <?php $missionText = preg_replace('/^\s*\d+[.)]\s*/u', '', $missionItem); ?>
                    <li><span class="mission-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span><?= htmlspecialchars($missionText, ENT_QUOTES, 'UTF-8') ?></span></li>
                <?php endforeach; ?>
            </ol>
        </section>

        <aside class="profile-address">
            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
            <div><span>Alamat Masjid</span><p><?= nl2br(htmlspecialchars($profil['alamat'], ENT_QUOTES, 'UTF-8')) ?></p></div>
        </aside>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
