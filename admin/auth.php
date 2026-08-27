<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';

if (empty($_SESSION['user_id']) || !in_array($role, ['admin', 'pengurus'], true)) {
    $_SESSION['error'] = 'Silakan login untuk mengakses halaman admin.';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $adminRoot = preg_replace('#/admin(?:/.*)?$#', '/admin', $scriptName);
    header('Location: ' . $adminRoot . '/login.php');
    exit;
}
