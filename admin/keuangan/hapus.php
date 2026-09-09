<?php
require __DIR__ . '/../auth.php';
$_SESSION['error'] = 'Pengelolaan transaksi dilakukan melalui akun Bendahara.';
header('Location: ' . app_url('admin/keuangan/index.php'));
exit;
