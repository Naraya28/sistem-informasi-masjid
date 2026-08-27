<?php
require '../auth.php';
$_SESSION['error']='Data utama manajemen tidak dapat dihapus. Silakan kosongkan bagian yang tidak diperlukan.';
header('Location:index.php');exit;
