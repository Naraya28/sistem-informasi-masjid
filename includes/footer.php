<footer>
    <p>&copy; <?= date('Y') ?> Masjid Al-Firdaus</p>
</footer>
<script>
const publicNavbar = document.querySelector('.navbar-user');
const publicNavToggle = document.querySelector('.nav-toggle');
const publicNavPanel = document.getElementById('publicNavPanel');

function setPublicNav(open) {
    if (!publicNavbar || !publicNavToggle || !publicNavPanel) return;
    publicNavbar.classList.toggle('nav-open', open);
    publicNavPanel.classList.toggle('show', open);
    publicNavToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    publicNavToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    publicNavToggle.innerHTML = open
        ? '<i class="fa-solid fa-xmark"></i>'
        : '<i class="fa-solid fa-bars"></i>';
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

    document.addEventListener('click', function (event) {
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
            publicNavbar.classList.contains('nav-open')
        ) {
            setPublicNav(false);
            publicNavToggle.focus();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) setPublicNav(false);
    });
}
</script>
</body>
</html>