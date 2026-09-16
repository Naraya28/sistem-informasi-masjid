<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/recycle_bin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Permintaan tidak valid.');
}

$itemId = filter_var($_POST['item_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    recycle_bin_restore($conn, $itemId, (int) $_SESSION['user_id']);
    $_SESSION['success'] = 'Data berhasil dipulihkan dan kembali tersedia.';
} catch (RuntimeException $error) {
    $_SESSION['error'] = $error->getMessage();
}
header('Location: ' . app_url('admin/tempat_sampah/index.php'), true, 303);
exit;
