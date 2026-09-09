<?php
require __DIR__ . '/auth.php';
$dashboardUser = current_user();
$areaTitle = 'Laporan';
$areaDescription = 'Area ini khusus untuk pengelolaan laporan Masjid Al-Firdaus.';
$featureDescription = 'Pembuatan dan pengelolaan laporan akan tersedia di sini.';
require __DIR__ . '/../includes/role_dashboard.php';
