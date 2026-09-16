<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/recycle_bin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Permintaan tidak valid.');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    recycle_bin_soft_delete($conn, 'artikel', $id, (int) $_SESSION['user_id']);
    $_SESSION['success'] = 'Artikel dipindahkan ke Tempat Sampah.';
} catch (RuntimeException $error) {
    $_SESSION['error'] = $error->getMessage();
}
header('Location: index.php', true, 303);
exit;
