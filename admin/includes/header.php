<?php
$page_title = $page_title ?? 'Admin SIM Masjid';
$admin_base = $admin_base ?? '..';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $admin_base ?>/../assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
    <link rel="stylesheet" href="<?= $admin_base ?>/../assets/css/delete-approval.css?v=<?= filemtime(__DIR__ . '/../../assets/css/delete-approval.css') ?>">
    <link rel="stylesheet" href="<?= $admin_base ?>/../assets/css/recycle-bin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/recycle-bin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-shell">
<button class="sidebar-toggle" type="button" aria-label="Buka menu navigasi" aria-controls="adminSidebar" aria-expanded="false" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
<button class="sidebar-backdrop" type="button" aria-label="Tutup menu navigasi" onclick="closeSidebar()"></button>
<div class="layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
