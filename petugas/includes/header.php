<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
$active = $active ?? 'laporan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= laporan_escape($title) ?> | SIM Masjid Al-Firdaus</title>
    <link rel="stylesheet" href="<?= laporan_escape(app_url('assets/css/petugas.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="finance-app report-app">
<a class="skip-link" href="#main">Lewati ke konten</a>
<aside class="finance-sidebar">
    <div class="brand"><span class="brand-icon"><i class="fa-solid fa-mosque" aria-hidden="true"></i></span><div><strong>SIM MASJID</strong><small>Al-Firdaus</small></div></div>
    <p class="nav-caption">RUANG PETUGAS MASJID</p>
    <nav aria-label="Navigasi Petugas Masjid">
        <a <?= $active === 'dashboard' ? 'aria-current="page"' : '' ?> href="<?= laporan_escape(app_url('petugas/dashboard.php')) ?>"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i> Dashboard</a>
        <a <?= $active === 'laporan' ? 'aria-current="page"' : '' ?> href="<?= laporan_escape(app_url('petugas/laporan/index.php')) ?>"><i class="fa-solid fa-file-lines" aria-hidden="true"></i> Laporan</a>
    </nav>
    <div class="sidebar-bottom"><span class="role-pill">Petugas Masjid</span><a href="<?= laporan_escape(app_url('admin/logout.php')) ?>"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Logout</a></div>
</aside>
<div class="finance-workspace">
    <header class="workspace-header"><span>SIM Masjid Al-Firdaus</span><span class="user-name"><i class="fa-regular fa-circle-user" aria-hidden="true"></i> <?= laporan_escape($_SESSION['nama_lengkap']) ?></span></header>
    <main id="main">
    <?php foreach (['success', 'error'] as $flash): if (!empty($_SESSION[$flash])): ?>
        <div class="alert <?= $flash ?>" role="<?= $flash === 'error' ? 'alert' : 'status' ?>"><?= laporan_escape($_SESSION[$flash]) ?></div>
    <?php unset($_SESSION[$flash]); endif; endforeach; ?>