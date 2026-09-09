<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    auth_redirect(app_url('admin/login.php'));
}

$username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Username dan password wajib diisi.';
    auth_redirect(app_url('admin/login.php'));
}

require_once __DIR__ . '/../config/koneksi.php';
$stmt = mysqli_prepare($conn, 'SELECT id, password, nama_lengkap, role FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

mysqli_stmt_close($stmt);

// Existing Admin hash format was verified before removing plaintext compatibility.
if (!$data || !isset(role_labels()[$data['role']]) || !password_verify($password, $data['password'])) {
    $_SESSION['error'] = 'Username atau password salah.';
    auth_redirect(app_url('admin/login.php'));
}

session_regenerate_id(true);
$_SESSION = [];
$_SESSION['user_id'] = (int) $data['id'];
$_SESSION['nama_lengkap'] = $data['nama_lengkap'];
$_SESSION['role'] = $data['role'];
$_SESSION['success'] = 'Login berhasil. Selamat datang, ' . $data['nama_lengkap'] . '.';
auth_redirect(dashboard_url($data['role']));
