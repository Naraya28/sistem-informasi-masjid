<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    auth_redirect(app_url('bendahara/keuangan/index.php'));
}
if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Permintaan tidak valid.');
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    $created = delete_approval_create($conn, 'keuangan', $id, (int) $_SESSION['user_id']);
    $_SESSION[$created ? 'success' : 'error'] = $created
        ? 'Permintaan penghapusan berhasil diajukan kepada Admin.'
        : 'Permintaan penghapusan data ini sedang menunggu persetujuan Admin.';
} catch (RuntimeException $error) {
    $_SESSION['error'] = $error->getMessage();
}
header('Location: ' . app_url('bendahara/keuangan/index.php'), true, 303);
exit;