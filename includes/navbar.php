<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="navbar-user">

    <!-- LOGO -->
    <div class="nav-logo">
        <img src="assets/img/logo-masjid-web.png" alt="Logo Masjid Al-Firdaus" onerror="this.src='assets/img/logo-masjid.png'">
        <h2>Masjid Al-Firdaus</h2>
    </div>

    <button class="nav-toggle" type="button" aria-label="Buka menu navigasi" aria-controls="publicNavPanel" aria-expanded="false">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="nav-panel" id="publicNavPanel">
    <!-- MENU -->
    <nav class="nav-menu" aria-label="Navigasi utama">
        <ul>

            <li>
                <a href="index.php"
                   class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                   Home
                </a>
            </li>

            <li>
                <a href="profil.php"
                   class="<?= ($current_page == 'profil.php') ? 'active' : '' ?>">
                   Profil
                </a>
            </li>

            <li>
                <a href="manajemen.php"
                   class="<?= ($current_page == 'manajemen.php') ? 'active' : '' ?>">
                   Manajemen Masjid
                </a>
            </li>

            <li>
                <a href="artikel.php"
                   class="<?= ($current_page == 'artikel.php' || $current_page == 'detail_artikel.php') ? 'active' : '' ?>">
                   Artikel
                </a>
            </li>

            <li>
                <a href="organisasi.php"
                   class="<?= ($current_page == 'organisasi.php') ? 'active' : '' ?>">
                   Organisasi
                </a>
            </li>

            <li>
                <a href="kontak.php"
                   class="<?= ($current_page == 'kontak.php') ? 'active' : '' ?>">
                   Kontak
                </a>
            </li>

        </ul>
    </nav>

    <!-- SOSIAL MEDIA -->
    <div class="nav-social">

        <a href="https://facebook.com" target="_blank">
            <i class="fab fa-facebook-f"></i>
        </a>

        <a href="https://instagram.com" target="_blank">
            <i class="fab fa-instagram"></i>
        </a>

        <a href="https://youtube.com" target="_blank">
            <i class="fab fa-youtube"></i>
        </a>

    </div>
    </div>

</header>
