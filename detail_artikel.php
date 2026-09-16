<?php
require __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/article_news.php';

$idInput = $_GET['id'] ?? null;
$id = is_scalar($idInput) ? filter_var($idInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
$article = $id === false ? null : article_news_find($conn, (int) $id);
$related = $article ? article_news_related($conn, $article) : [];
$categories = article_news_categories($conn);
if (!$article) {
    http_response_code(404);
}

$page_title = $article ? $article['judul'] . ' | Masjid Al-Firdaus' : 'Artikel Tidak Ditemukan | Masjid Al-Firdaus';
$page_description = $article ? article_news_excerpt($article['deskripsi'], 160) : 'Artikel yang diminta tidak ditemukan.';
$page_stylesheets = ['assets/css/article-news.css'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main class="institution-detail">
    <section class="institution-detail-hero">
        <div class="news-shell">
            <h1><?= $article ? article_news_escape($article['judul']) : 'Artikel Tidak Ditemukan' ?></h1>
            <nav aria-label="Breadcrumb">
                <ol>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="artikel.php">Artikel</a></li>
                    <li aria-current="page"><span><?= $article ? 'Detail Berita' : 'Tidak Ditemukan' ?></span></li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="news-shell institution-detail-shell">
        <?php if (!$article): ?>
            <section class="institution-not-found" aria-labelledby="not-found-title">
                <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                <h2 id="not-found-title">Berita Tidak Tersedia</h2>
                <p>Artikel yang Anda cari tidak tersedia atau sudah dipindahkan dari halaman publik.</p>
                <a href="artikel.php"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke Artikel</a>
            </section>
        <?php else: $mainImage = article_news_image($article['gambar']); ?>
            <div class="institution-detail-layout">
                <article class="institution-article-main">
                    <div class="institution-article-image">
                        <?php if ($mainImage): ?>
                            <img src="<?= article_news_escape($mainImage) ?>" alt="<?= article_news_escape($article['judul']) ?>">
                        <?php else: ?>
                            <div class="institution-news-fallback"><i class="fa-solid fa-mosque" aria-hidden="true"></i><span>Masjid Al-Firdaus</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="institution-article-meta">
                        <span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?= article_news_escape(article_news_date($article['tanggal'])) ?></span>
                        <a href="artikel.php?kategori=<?= (int) $article['id_kategori'] ?>"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?= article_news_escape($article['nama_kategori'] ?: 'Tanpa Kategori') ?></a>
                    </div>
                    <div class="institution-article-body"><?= nl2br(article_news_escape($article['deskripsi'])) ?></div>
                    <a class="institution-back-link" href="artikel.php"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke Artikel</a>
                </article>

                <aside class="institution-detail-sidebar">
                    <section class="institution-sidebar-block" aria-labelledby="category-title">
                        <h2 id="category-title">Kategori</h2>
                        <ul class="institution-category-list">
                            <?php foreach ($categories as $category): ?>
                                <li><a href="artikel.php?kategori=<?= (int) $category['id_kategori'] ?>"><span><?= article_news_escape($category['nama_kategori']) ?> <small><?= (int) $category['article_count'] ?></small></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                    <?php if ($related): ?>
                        <section class="institution-sidebar-block" aria-labelledby="related-title">
                            <h2 id="related-title">Artikel Terkait</h2>
                            <div class="institution-related-list">
                                <?php foreach ($related as $item): $relatedImage = article_news_image($item['gambar']); ?>
                                    <article class="institution-related-item">
                                        <a class="institution-related-thumb" href="detail_artikel.php?id=<?= (int) $item['id_artikel'] ?>" aria-label="Baca <?= article_news_escape($item['judul']) ?>">
                                            <?php if ($relatedImage): ?><img src="<?= article_news_escape($relatedImage) ?>" alt="" loading="lazy">
                                            <?php else: ?><span><i class="fa-solid fa-mosque" aria-hidden="true"></i></span><?php endif; ?>
                                        </a>
                                        <div>
                                            <p><?= article_news_escape($item['nama_kategori'] ?: 'Tanpa Kategori') ?> &middot; <?= article_news_escape(article_news_date($item['tanggal'])) ?></p>
                                            <h3><a href="detail_artikel.php?id=<?= (int) $item['id_artikel'] ?>"><?= article_news_escape($item['judul']) ?></a></h3>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
