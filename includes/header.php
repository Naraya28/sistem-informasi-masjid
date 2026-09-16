<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'SIM Masjid Al-Firdaus' ?></title>
    <?php
    $publicStylesheetPath = dirname(__DIR__) . '/assets/css/style.css';
    $publicStylesheetVersion = is_file($publicStylesheetPath) ? (string) filemtime($publicStylesheetPath) : '1';
    ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= rawurlencode($publicStylesheetVersion) ?>">
    <?php if (!empty($page_description)): ?><meta name="description" content="<?= htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
    <?php foreach (($page_stylesheets ?? []) as $pageStylesheet):
        $safeStylesheet = is_string($pageStylesheet) && preg_match('#^assets/css/[a-z0-9._-]+\.css$#i', $pageStylesheet) ? $pageStylesheet : null;
        if (!$safeStylesheet) continue;
        $stylesheetFile = dirname(__DIR__) . '/' . $safeStylesheet;
        $stylesheetVersion = is_file($stylesheetFile) ? (string) filemtime($stylesheetFile) : '1';
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($safeStylesheet, ENT_QUOTES, 'UTF-8') ?>?v=<?= rawurlencode($stylesheetVersion) ?>">
    <?php endforeach; ?>
</head>
<?php $publicPageClass = 'page-' . preg_replace('/[^a-z0-9-]+/i', '-', pathinfo(basename($_SERVER['PHP_SELF'] ?? 'index.php'), PATHINFO_FILENAME)); ?>
<body class="public-site <?= htmlspecialchars($publicPageClass, ENT_QUOTES, 'UTF-8') ?>">
