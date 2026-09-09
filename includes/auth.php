<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function role_labels(): array
{
    return ['admin' => 'Admin', 'bendahara' => 'Bendahara', 'pengurus' => 'Petugas Masjid'];
}

function app_url(string $path): string
{
    // Preserve the installation subdirectory, including nested admin modules.
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = preg_replace('#/(?:admin|bendahara|petugas)(?:/.*)?$#', '', $script);
    return '/' . ltrim(rtrim($base, '/') . '/' . ltrim($path, '/'), '/');
}

function dashboard_url(string $role): string
{
    $destinations = [
        'admin' => 'admin/dashboard.php',
        'bendahara' => 'bendahara/dashboard.php',
        'pengurus' => 'petugas/dashboard.php',
    ];
    return app_url($destinations[$role] ?? 'admin/login.php');
}

function auth_redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function current_user(): ?array
{
    // Cache only within this request; every authenticated request rechecks users.
    static $checked = false;
    static $current = null;
    if ($checked) {
        return $current;
    }
    $checked = true;
    $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        unset($_SESSION['user_id'], $_SESSION['nama_lengkap'], $_SESSION['role']);
        return null;
    }

    global $conn;
    require_once __DIR__ . '/../config/koneksi.php';
    $stmt = mysqli_prepare($conn, 'SELECT id, nama_lengkap, role FROM users WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$user || !isset(role_labels()[$user['role']])) {
        $_SESSION = [];
        session_regenerate_id(true);
        return null;
    }
    if (($_SESSION['role'] ?? '') !== $user['role']) {
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
    }
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];
    $current = $user;
    return $current;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function current_role(): ?string
{
    $user = current_user();
    return $user['role'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        $_SESSION['error'] = 'Silakan login untuk mengakses dashboard.';
        auth_redirect(app_url('admin/login.php'));
    }
    return $user;
}

function require_roles(array $roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        auth_redirect(dashboard_url($user['role']));
    }
    return $user;
}

function require_role(string $role): array
{
    return require_roles([$role]);
}
