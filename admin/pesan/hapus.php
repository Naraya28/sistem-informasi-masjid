<?php
require '../auth.php';
require '../../config/koneksi.php';
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM pesan WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['success'] = 'Pesan berhasil dihapus.';
}
header('Location: index.php');
exit;
