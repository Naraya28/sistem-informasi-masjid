<?php
// Render only from an already guarded dashboard.
if (!isset($dashboardUser, $areaTitle, $areaDescription, $featureDescription)) {
    http_response_code(404);
    exit;
}
$roleLabel = role_labels()[$dashboardUser['role']];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?> | SIM Masjid</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('assets/css/role-dashboard.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="role-shell">
    <header class="role-header">
        <div><strong>SIM Masjid Al-Firdaus</strong><p>Dashboard <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></p></div>
        <a class="logout" href="<?= htmlspecialchars(app_url('admin/logout.php'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
    </header>
    <main>
        <span class="role-badge"><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></span>
        <h1>Selamat datang, <?= htmlspecialchars($dashboardUser['nama_lengkap'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($areaDescription, ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="notice" role="status"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <section class="feature-card" aria-labelledby="feature-title">
            <h2 id="feature-title"><?= htmlspecialchars($areaTitle, ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($featureDescription, ENT_QUOTES, 'UTF-8') ?></p>
            <span class="coming-soon">Segera tersedia</span>
        </section>
    </main>
</div>
</body>
</html>
