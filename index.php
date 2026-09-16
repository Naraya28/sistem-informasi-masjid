<?php
require __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/article_news.php';
$homeNews = article_news_latest($conn, 3);
$page_title = 'SIM Masjid Al-Firdaus';
$page_stylesheets = ['assets/css/article-news.css'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- BAB 3.5 CONFLICT POINT: versi Fakhri -->

<section class="hero">
    <img src="assets/img/banner.jpg" class="hero-img" alt="Masjid Al-Firdaus">
    <div class="hero-overlay"></div>
    <div class="hero-content"><h1>MASJID <br> AL-FIRDAUS</h1><p>~ Dari Masjid Membangun Umat ~</p></div>
</section>
<section class="glass-wrapper">
    <section class="info-box">
        <article class="box"><div class="feature-card-head"><div class="box-icon"><i class="fa-solid fa-mosque"></i></div><span class="feature-number" aria-hidden="true">01</span></div><span class="feature-kicker">Sekilas Masjid</span><h2>Tentang Kami</h2><p>Berawal dari sebuah langgar kecil di daerah Candi Sidoarjo, Masjid Al-Firdaus terus membangun umat dan masyarakat.</p><span class="feature-accent" aria-hidden="true"></span></article>
        <article class="box"><div class="feature-card-head"><div class="box-icon"><i class="fa-solid fa-book-open"></i></div><span class="feature-number" aria-hidden="true">02</span></div><span class="feature-kicker">Pengelolaan</span><h2>Manajemen Masjid</h2><p>Menerapkan manajemen masjid modern dan profesional untuk seluruh jamaah.</p><span class="feature-accent" aria-hidden="true"></span></article>
        <article class="box"><div class="feature-card-head"><div class="box-icon"><i class="fa-solid fa-hand-holding-heart"></i></div><span class="feature-number" aria-hidden="true">03</span></div><span class="feature-kicker">Dukungan Umat</span><h2>Dukungan Jamaah</h2><p>Dukung dakwah melalui sedekah, zakat, infak, dan wakaf.</p><span class="feature-accent" aria-hidden="true"></span></article>
    </section>
    <section class="about"><h1>Selamat Datang Di Masjid Al-Firdaus</h1><p>Masjid Al-Firdaus terus berusaha membangun umat dan menyejahterakan masyarakat melalui dakwah dan kegiatan sosial.</p></section>
</section>
<section class="home-news" aria-labelledby="home-news-title">
    <div class="news-shell">
        <header class="home-news-heading">
            <div>
                <p class="institution-eyebrow">Kabar Masjid</p>
                <h2 id="home-news-title">Berita Terbaru</h2>
                <p>Informasi kegiatan dan kabar terbaru dari Masjid Al-Firdaus.</p>
            </div>
            <a href="artikel.php">Lihat Semua Artikel <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </header>
        <?php if ($homeNews): ?>
            <div class="institution-news-grid">
                <?php foreach ($homeNews as $newsArticle) include __DIR__ . '/includes/article_news_card.php'; ?>
            </div>
        <?php else: ?>
            <div class="institution-news-empty"><i class="fa-regular fa-newspaper" aria-hidden="true"></i><p>Belum ada berita yang dipublikasikan.</p></div>
        <?php endif; ?>
    </div>
</section>
<script src="assets/js/home.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
