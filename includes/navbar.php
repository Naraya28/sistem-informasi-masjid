<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="navbar-user">
    <a href="index.php" class="nav-logo" aria-label="Beranda Masjid Al-Firdaus">
        <img class="nav-logo-image" src="assets/img/logo-masjid-web.png" alt="Logo Masjid Al-Firdaus">
        <span class="nav-logo-text">
            <strong>Masjid Al-Firdaus</strong>
            <small>Sistem Informasi Masjid</small>
        </span>
    </a>

    <button class="nav-toggle" type="button" aria-label="Buka menu navigasi" aria-controls="publicNavPanel" aria-expanded="false">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="nav-panel" id="publicNavPanel">
        <nav class="nav-menu" aria-label="Navigasi utama">
            <ul>
                <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="profil.php" class="<?= ($current_page == 'profil.php') ? 'active' : '' ?>">Profil</a></li>
                <li><a href="manajemen.php" class="<?= ($current_page == 'manajemen.php') ? 'active' : '' ?>">Manajemen Masjid</a></li>
                <li><a href="artikel.php" class="<?= ($current_page == 'artikel.php' || $current_page == 'detail_artikel.php') ? 'active' : '' ?>">Artikel</a></li>
                <li class="nav-dropdown <?= ($current_page == 'organisasi.php') ? 'active' : '' ?>">
                    <button class="nav-dropdown-trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="kepengurusanMenu">
                        <span>Kepengurusan</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <ul class="nav-dropdown-menu" id="kepengurusanMenu">
                        <li><a href="organisasi.php#takmir">Takmir Masjid</a></li>
                        <li><a href="organisasi.php#organisasi">Organisasi</a></li>
                    </ul>
                </li>
                <li><a href="kontak.php" class="<?= ($current_page == 'kontak.php') ? 'active' : '' ?>">Kontak</a></li>
            </ul>
        </nav>
    </div>
</header>
