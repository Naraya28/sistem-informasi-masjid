<?php
session_start();
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontak.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');
$subjek = trim($_POST['subjek'] ?? '');
$isiPesan = trim($_POST['pesan'] ?? '');

if ($nama === '' || $isiPesan === '') {
    $_SESSION['contact_error'] = 'Nama dan pesan wajib diisi.';
} elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Format email tidak valid.';
} elseif ($noHp !== '' && !preg_match('/^[0-9+() .-]{6,20}$/', $noHp)) {
    $_SESSION['contact_error'] = 'Nomor HP hanya boleh berisi angka dan karakter +, -, spasi, atau kurung.';
} else {
    $stmt = mysqli_prepare($conn, 'INSERT INTO pesan (nama, email, no_hp, subjek, pesan) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sssss', $nama, $email, $noHp, $subjek, $isiPesan);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['contact_success'] = 'Pesan berhasil dikirim. Terima kasih telah menghubungi kami.';
        unset($_SESSION['contact_old']);
        header('Location: kontak.php#form-pesan');
        exit;
    }
    $_SESSION['contact_error'] = 'Pesan belum dapat disimpan. Silakan coba lagi.';
}

$_SESSION['contact_old'] = compact('nama', 'email', 'noHp', 'subjek', 'isiPesan');
header('Location: kontak.php#form-pesan');
exit;
