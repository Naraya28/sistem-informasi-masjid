<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/keuangan.php';
$active = $active ?? 'keuangan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= keuangan_escape($title) ?> | SIM Masjid Al-Firdaus</title>
    <link rel="stylesheet" href="<?= keuangan_escape(app_url('assets/css/bendahara.css')) ?>?v=<?= filemtime(__DIR__ . '/../../assets/css/bendahara.css') ?>">
    <link rel="stylesheet" href="<?= keuangan_escape(app_url('assets/css/delete-approval.css')) ?>?v=<?= filemtime(__DIR__ . '/../../assets/css/delete-approval.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="finance-app">
<a class="skip-link" href="#main">Lewati ke konten</a>
<aside class="finance-sidebar">
    <div class="brand"><span class="brand-icon"><i class="fa-solid fa-mosque" aria-hidden="true"></i></span><div><strong>SIM MASJID</strong><small>Al-Firdaus</small></div></div>
    <div class="finance-role-card"><span>BENDAHARA</span><strong><?= keuangan_escape($_SESSION['nama_lengkap']) ?></strong><small>Pengelola keuangan masjid</small></div>
    <p class="nav-caption">MENU UTAMA</p>
    <nav aria-label="Navigasi Bendahara">
        <a <?= $active === 'dashboard' ? 'aria-current="page"' : '' ?> href="<?= keuangan_escape(app_url('bendahara/dashboard.php')) ?>"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i> Dashboard</a>
        <a <?= $active === 'keuangan' ? 'aria-current="page"' : '' ?> href="<?= keuangan_escape(app_url('bendahara/keuangan/index.php')) ?>"><i class="fa-solid fa-wallet" aria-hidden="true"></i> Keuangan</a>
    </nav>
    <div class="sidebar-bottom"><a href="<?= keuangan_escape(app_url('admin/logout.php')) ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout</a></div>
</aside>
<div class="finance-workspace">
    <header class="workspace-header"><span><i class="fa-solid fa-building-columns" aria-hidden="true"></i> Ruang Keuangan</span><span class="user-name"><i class="fa-regular fa-circle-user" aria-hidden="true"></i> <?= keuangan_escape($_SESSION['nama_lengkap']) ?></span></header>
    <main id="main">
    <?php foreach (['success', 'error'] as $flash): if (!empty($_SESSION[$flash])): ?>
        <div class="alert <?= $flash ?>" role="<?= $flash === 'error' ? 'alert' : 'status' ?>"><?= keuangan_escape($_SESSION[$flash]) ?></div>
    <?php unset($_SESSION[$flash]); endif; endforeach; ?>
