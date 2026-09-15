    </main>
</div>
<script>
const sidebar = document.getElementById('adminSidebar');
const sidebarToggle = document.querySelector('.sidebar-toggle');
const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

function setSidebar(open) {
    sidebar.classList.toggle('show', open);
    sidebarBackdrop.classList.toggle('show', open);
    sidebarToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    sidebarToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    document.body.classList.toggle('sidebar-open', open && window.innerWidth <= 900);
}

function toggleSidebar() {
    setSidebar(!sidebar.classList.contains('show'));
}

function closeSidebar() {
    setSidebar(false);
}

document.addEventListener('click', function (event) {
    if (window.innerWidth <= 900 && sidebar.classList.contains('show') && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
        closeSidebar();
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && sidebar.classList.contains('show')) {
        closeSidebar();
        sidebarToggle.focus();
    }
});

window.addEventListener('resize', function () {
    if (window.innerWidth > 900) closeSidebar();
});
</script>
<script src="<?= $admin_base ?>/../assets/js/file-preview.js?v=<?= filemtime(__DIR__ . '/../../assets/js/file-preview.js') ?>"></script>
</body>
</html>
