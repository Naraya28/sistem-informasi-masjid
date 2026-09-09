<?php
require __DIR__ . '/../auth.php';
$_SESSION['error'] = 'Pengelolaan laporan dilakukan melalui akun Petugas Masjid.';
header('Location: ' . app_url('admin/laporan/index.php'));
exit;
