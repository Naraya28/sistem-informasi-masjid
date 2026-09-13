<footer class="site-footer" role="contentinfo">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-column footer-brand-block">
                <div class="footer-brand">
                    <i class="fa-solid fa-mosque" aria-hidden="true"></i>
                    <span>SIM Masjid Al-Firdaus</span>
                </div>
                <p>Sistem informasi untuk mendukung pengelolaan dan pelayanan Masjid Al-Firdaus.</p>
            </div>

            <div class="footer-column">
                <h3>Navigasi</h3>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="manajemen.php">Manajemen Masjid</a></li>
                    <li><a href="artikel.php">Artikel</a></li>
                    <li><a href="organisasi.php">Kepengurusan</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Layanan</h3>
                <ul>
                    <li><a href="profil.php">Informasi Masjid</a></li>
                    <li><a href="manajemen.php">Manajemen Masjid</a></li>
                    <li><a href="kontak.php#form-pesan">Kirim Pesan</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Informasi</h3>
                <p>Informasi kontak tersedia pada halaman Kontak.</p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Masjid Al-Firdaus. Semua hak dilindungi.</p>
    </div>
</footer>
<script>
const publicNavbar = document.querySelector('.navbar-user');
const publicNavToggle = document.querySelector('.nav-toggle');
const publicNavPanel = document.getElementById('publicNavPanel');
const publicDropdown = document.querySelector('.nav-dropdown');
const publicDropdownTrigger = document.querySelector('.nav-dropdown-trigger');

function setPublicDropdown(open) {
    if (!publicDropdown || !publicDropdownTrigger) return;
    publicDropdown.classList.toggle('dropdown-open', open);
    publicDropdownTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function setPublicNav(open) {
    if (!publicNavbar || !publicNavToggle || !publicNavPanel) return;
    publicNavbar.classList.toggle('nav-open', open);
    publicNavPanel.classList.toggle('show', open);
    publicNavToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    publicNavToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    publicNavToggle.innerHTML = open
        ? '<i class="fa-solid fa-xmark"></i>'
        : '<i class="fa-solid fa-bars"></i>';
    if (!open) setPublicDropdown(false);
}

if (publicNavToggle) {
    publicNavToggle.addEventListener('click', function () {
        setPublicNav(!publicNavPanel.classList.contains('show'));
    });

    publicNavPanel.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            setPublicNav(false);
        });
    });

    if (publicDropdown && publicDropdownTrigger) {
        publicDropdownTrigger.addEventListener('click', function () {
            setPublicDropdown(!publicDropdown.classList.contains('dropdown-open'));
        });

        publicDropdown.addEventListener('pointerenter', function () {
            if (window.innerWidth > 900) setPublicDropdown(true);
        });

        publicDropdown.addEventListener('pointerleave', function () {
            if (window.innerWidth > 900) setPublicDropdown(false);
        });

        publicDropdown.addEventListener('focusin', function () {
            if (window.innerWidth > 900) setPublicDropdown(true);
        });

        publicDropdown.addEventListener('focusout', function (event) {
            if (window.innerWidth > 900 && !publicDropdown.contains(event.relatedTarget)) {
                setPublicDropdown(false);
            }
        });
    }

    document.addEventListener('click', function (event) {
        if (publicDropdown && publicDropdown.classList.contains('dropdown-open') && !publicDropdown.contains(event.target)) {
            setPublicDropdown(false);
        }
        if (
            window.innerWidth <= 900 &&
            publicNavbar.classList.contains('nav-open') &&
            !publicNavbar.contains(event.target)
        ) {
            setPublicNav(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape' &&
            (publicNavbar.classList.contains('nav-open') || (publicDropdown && publicDropdown.classList.contains('dropdown-open')))
        ) {
            if (publicDropdown && publicDropdown.classList.contains('dropdown-open')) {
                setPublicDropdown(false);
                publicDropdownTrigger.focus();
            } else {
                setPublicNav(false);
                publicNavToggle.focus();
            }
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) setPublicNav(false);
    });

}
</script>
</body>
</html>
