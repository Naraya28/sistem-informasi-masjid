<?php
require __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/article_news.php';

try {
    $list = article_news_list($conn, $_GET);
} catch (RuntimeException $error) {
    http_response_code(500);
    $list = [
        'search' => '', 'category' => 0, 'categories' => [], 'hasFilters' => false,
        'rows' => [], 'total' => 0, 'pages' => 1, 'page' => 1, 'offset' => 0,
    ];
}

$page_title = 'Berita dan Kegiatan Masjid';
$page_description = 'Berita, kegiatan, dan informasi terbaru dari Masjid Al-Firdaus.';
$page_stylesheets = ['assets/css/article-news.css'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main class="news-archive">
    <section class="news-archive-hero" aria-labelledby="archive-title">
        <div class="news-shell">
            <p class="institution-eyebrow">Berita &amp; Kegiatan</p>
            <h1 id="archive-title">Kabar Terbaru Masjid Al-Firdaus</h1>
            <p>Temukan informasi kegiatan, pengumuman, dan kabar terkini dari lingkungan masjid.</p>
        </div>
    </section>

    <section class="news-shell news-archive-content" aria-labelledby="news-list-title">
        <div class="news-archive-heading">
            <div>
                <p class="institution-eyebrow"><?= $list['hasFilters'] ? 'Hasil Pencarian' : 'Arsip Berita' ?></p>
                <h2 id="news-list-title"><?= $list['hasFilters'] ? 'Berita yang Ditemukan' : 'Berita Terbaru' ?></h2>
            </div>
            <?php if ($list['total'] > 0): ?>
                <p>Menampilkan <?= $list['offset'] + 1 ?>&ndash;<?= min($list['offset'] + count($list['rows']), $list['total']) ?> dari <?= (int) $list['total'] ?> berita</p>
            <?php endif; ?>
        </div>

        <form class="news-filter-compact" method="GET" action="artikel.php" role="search">
            <label class="sr-only" for="article-search">Cari berita</label>
            <div class="news-filter-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input id="article-search" name="q" maxlength="120" value="<?= article_news_escape($list['search']) ?>" placeholder="Cari berita atau kegiatan..."></div>
            <label class="sr-only" for="article-category">Pilih kategori</label>
            <select id="article-category" name="kategori">
                <option value="">Semua Kategori</option>
                <?php foreach ($list['categories'] as $item): ?>
                    <option value="<?= (int) $item['id_kategori'] ?>" <?= $list['category'] === (int) $item['id_kategori'] ? 'selected' : '' ?>><?= article_news_escape($item['nama_kategori']) ?> (<?= (int) $item['article_count'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Cari</button>
            <?php if ($list['hasFilters']): ?><a href="artikel.php">Reset</a><?php endif; ?>
        </form>

        <?php if (!$list['rows']): ?>
            <div class="institution-news-empty light">
                <i class="fa-regular fa-newspaper" aria-hidden="true"></i>
                <h3><?= $list['hasFilters'] ? 'Tidak Ada Berita yang Ditemukan' : 'Belum Ada Berita' ?></h3>
                <p><?= $list['hasFilters'] ? 'Coba gunakan kata kunci atau kategori yang berbeda.' : 'Berita dan kegiatan masjid akan tampil di halaman ini.' ?></p>
                <?php if ($list['hasFilters']): ?><a href="artikel.php">Reset Pencarian</a><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="institution-news-grid">
                <?php foreach ($list['rows'] as $newsArticle) include __DIR__ . '/includes/article_news_card.php'; ?>
            </div>

            <?php if ($list['pages'] > 1): ?>
                <nav class="news-pagination" aria-label="Navigasi halaman berita">
                    <?php if ($list['page'] > 1): ?><a href="<?= article_news_escape(article_news_page_url($list, $list['page'] - 1)) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span>Sebelumnya</span></a><?php endif; ?>
                    <?php for ($number = max(1, $list['page'] - 2); $number <= min($list['pages'], $list['page'] + 2); $number++): ?>
                        <a href="<?= article_news_escape(article_news_page_url($list, $number)) ?>" <?= $number === $list['page'] ? 'aria-current="page"' : '' ?>><?= $number ?></a>
                    <?php endfor; ?>
                    <?php if ($list['page'] < $list['pages']): ?><a href="<?= article_news_escape(article_news_page_url($list, $list['page'] + 1)) ?>"><span>Berikutnya</span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
