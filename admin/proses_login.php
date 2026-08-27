<?php
session_start();
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$passwordValid = false;
$passwordInfo = $data ? password_get_info($data['password']) : ['algo' => null];

if ($data) {
    if (!empty($passwordInfo['algo'])) {
        $passwordValid = password_verify($password, $data['password']);
    } else {
        $passwordValid = hash_equals((string) $data['password'], $password);
        if ($passwordValid) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $userId = (int) $data['id'];
            $update = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
            mysqli_stmt_bind_param($update, 'si', $newHash, $userId);
            mysqli_stmt_execute($update);
        }
    }
}

if (!$passwordValid || !in_array($data['role'], ['admin', 'pengurus'], true)) {
    $_SESSION['error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $data['id'];
$_SESSION['nama_lengkap'] = $data['nama_lengkap'];
$_SESSION['role'] = $data['role'];
$_SESSION['success'] = 'Login berhasil. Selamat datang, ' . $data['nama_lengkap'] . '.';
header('Location: dashboard.php');
exit;
