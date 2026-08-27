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
        <?php
        $sections = [
            ['fa-mosque', 'Profil Singkat', 'profil_singkat'],
            ['fa-book-open', 'Asal Nama Masjid', 'asal_nama'],
            ['fa-eye', 'Visi Masjid', 'visi'],
            ['fa-bullseye', 'Misi Masjid', 'misi'],
            ['fa-location-dot', 'Alamat Masjid', 'alamat'],
        ];
        foreach ($sections as [$icon, $title, $field]):
        ?>
            <div class="profil-card">
                <div class="profil-title"><i class="fa-solid <?= $icon ?>"></i><h2><?= $title ?></h2></div>
                <div class="profil-content"><?= nl2br(htmlspecialchars($profil[$field], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
