<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/delete_approval.php';
$redirect = app_url('admin/laporan/index.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Permintaan tidak valid.'); }
$requestId = filter_var($_POST['request_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    $request = delete_approval_rows($conn, 'SELECT entity_type,status FROM delete_requests WHERE id=? LIMIT 1', 'i', [$requestId])[0] ?? null;
    if (!$request || $request['entity_type'] !== 'laporan') { throw new RuntimeException('Permintaan laporan tidak ditemukan.'); }
    delete_approval_review($conn, $requestId, (int) $_SESSION['user_id'], true);
    $_SESSION['success'] = 'Permintaan disetujui dan laporan dipindahkan ke Tempat Sampah. Lampiran tetap disimpan.';
} catch (RuntimeException $error) { $_SESSION['error'] = $error->getMessage(); }
header('Location: ' . $redirect, true, 303);
exit;
