<?php
require_once __DIR__ . '/../includes/auth.php';
if (is_logged_in()) {
    auth_redirect(dashboard_url(current_role()));
}
?>
<!DOCTYPE html>
<html lang="id" class="login-document">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem | SIM Masjid</title>
    <link rel="stylesheet" href="../assets/css/login.css?v=<?= filemtime(__DIR__ . '/../assets/css/login.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-page">
<div class="background-overlay"></div>
<div class="floating-icons" aria-hidden="true">
    <span class="floating-icon icon-mosque"><i class="fa-solid fa-mosque"></i></span>
    <span class="floating-icon icon-moon"><i class="fa-solid fa-moon"></i></span>
    <span class="floating-icon icon-star"><i class="fa-solid fa-star"></i></span>
    <span class="floating-icon icon-book"><i class="fa-solid fa-book-quran"></i></span>
    <span class="floating-icon icon-sparkle">&#10024;</span>
    <span class="floating-icon icon-pray"><i class="fa-solid fa-hands-praying"></i></span>
</div>
<div class="login-container">
    <div class="login-left">
        <h1>SIM Masjid Al-Firdaus</h1>
        <p>Sistem informasi untuk pengelolaan kegiatan, artikel, pengurus, keuangan, laporan, dan pesan jamaah.</p>
        <div class="activity-slider">
            <div class="slide active">Kajian Rutin Setiap Minggu</div>
            <div class="slide">Santunan Anak Yatim</div>
            <div class="slide">Program Ramadhan</div>
            <div class="slide">Kegiatan Pengajian Bulanan</div>
        </div>
    </div>
    <div class="login-right">
        <div class="login-heading-icon"><i class="fa-solid fa-mosque"></i></div>
        <span class="login-eyebrow">Portal Pengelola</span>
        <h2>Masuk ke Sistem</h2>
        <p class="login-subtitle">Akses dashboard sesuai peran Anda.</p>
        <p class="login-roles"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Admin <span>&bull;</span> Bendahara <span>&bull;</span> Petugas Masjid</p>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="login-alert"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form action="proses_login.php" method="POST">
            <div class="input-group">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
                <input type="text" name="username" placeholder="Username" aria-label="Username" autocomplete="username" required autofocus>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" name="password" placeholder="Password" aria-label="Password" autocomplete="current-password" required>
            </div>
            <button type="submit"><span>Masuk</span><i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i></button>
        </form>
    </div>
</div>
<script src="../assets/js/login.js"></script>
</body>
</html>
