<?php
require '../auth.php';
$_SESSION['error']='Data manajemen dikelola dari halaman utama manajemen.';
header('Location:index.php');exit;
