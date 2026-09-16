<?php
$newsImage = article_news_image($newsArticle['gambar'] ?? null);
$newsDate = article_news_date_parts($newsArticle['tanggal'] ?? null);
?>
<article class="institution-news-card">
    <a class="institution-news-media" href="detail_artikel.php?id=<?= (int) $newsArticle['id_artikel'] ?>" aria-label="Baca <?= article_news_escape($newsArticle['judul']) ?>">
        <?php if ($newsImage): ?>
            <img src="<?= article_news_escape($newsImage) ?>" alt="<?= article_news_escape($newsArticle['judul']) ?>" loading="lazy">
        <?php else: ?>
            <span class="institution-news-fallback"><i class="fa-solid fa-mosque" aria-hidden="true"></i><span>Masjid Al-Firdaus</span></span>
        <?php endif; ?>
        <?php if ($newsDate['day'] !== ''): ?>
            <time class="institution-news-date" datetime="<?= article_news_escape(date('Y-m-d', strtotime($newsArticle['tanggal']))) ?>">
                <strong><?= article_news_escape($newsDate['day']) ?></strong>
                <span><?= article_news_escape($newsDate['month']) ?> <?= article_news_escape($newsDate['year']) ?></span>
            </time>
        <?php endif; ?>
    </a>
    <div class="institution-news-body">
        <p class="institution-news-category"><?= article_news_escape($newsArticle['nama_kategori'] ?: 'Tanpa Kategori') ?></p>
        <h3><a href="detail_artikel.php?id=<?= (int) $newsArticle['id_artikel'] ?>"><?= article_news_escape($newsArticle['judul']) ?></a></h3>
        <p class="institution-news-excerpt"><?= article_news_escape(article_news_excerpt($newsArticle['deskripsi'])) ?></p>
        <a class="institution-news-link" href="detail_artikel.php?id=<?= (int) $newsArticle['id_artikel'] ?>">Baca Selengkapnya <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
