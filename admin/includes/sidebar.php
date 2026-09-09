<?php
require_once __DIR__ . '/../../includes/delete_approval.php';
$admin_base = $admin_base ?? '..';
$active_menu = $active_menu ?? '';
$adminName = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8');
try {
    $pendingFinanceCount = delete_approval_pending_count_for($conn, 'keuangan');
    $pendingReportCount = delete_approval_pending_count_for($conn, 'laporan');
} catch (Throwable $error) {
    $pendingFinanceCount = 0;
    $pendingReportCount = 0;
}
$menus = [
    'dashboard' => ['Dashboard', 'fa-home', $admin_base . '/dashboard.php'],
    'pengurus' => ['Data Petugas', 'fa-users', $admin_base . '/pengurus/index.php'],
    'kegiatan' => ['Artikel Kegiatan', 'fa-layer-group', $admin_base . '/kegiatan/index.php'],
    'keuangan' => ['Keuangan', 'fa-wallet', $admin_base . '/keuangan/index.php'],
    'laporan' => ['Laporan', 'fa-file-lines', $admin_base . '/laporan/index.php'],
    'pesan' => ['Pesan', 'fa-envelope', $admin_base . '/pesan/index.php'],
    'profil' => ['Profil Masjid', 'fa-id-card', $admin_base . '/profil/index.php'],
    'manajemen' => ['Manajemen', 'fa-book', $admin_base . '/manajemen/index.php'],
];
?>
<aside class="sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <h2>SIM MASJID</h2>
        <p><?= $adminName ?></p>
    </div>
    <ul>
        <?php foreach ($menus as $key => [$label, $icon, $url]): ?>
            <li class="<?= $active_menu === $key ? 'active' : '' ?>">
                <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                    <i class="fa <?= $icon ?>"></i> <?= $label ?><?php if ($key === 'keuangan' && $pendingFinanceCount > 0): ?> <span class="nav-count"><?= $pendingFinanceCount ?></span><?php elseif ($key === 'laporan' && $pendingReportCount > 0): ?> <span class="nav-count"><?= $pendingReportCount ?></span><?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a class="sidebar-logout" href="<?= $admin_base ?>/logout.php" onclick="return confirm('Yakin ingin logout?')">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>
</aside>
