<?php

function article_news_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function article_news_excerpt(?string $content, int $length = 155): string
{
    $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $content)) ?? '');
    return mb_strimwidth($plain, 0, $length, '...', 'UTF-8');
}

function article_news_date(?string $date): string
{
    $time = strtotime((string) $date);
    if ($time === false) {
        return '';
    }
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('j', $time) . ' ' . $months[(int) date('n', $time)] . ' ' . date('Y', $time);
}

function article_news_date_parts(?string $date): array
{
    $time = strtotime((string) $date);
    if ($time === false) {
        return ['day' => '', 'month' => '', 'year' => ''];
    }
    $months = [1 => 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
    return [
        'day' => date('d', $time),
        'month' => $months[(int) date('n', $time)],
        'year' => date('Y', $time),
    ];
}

function article_news_image(?string $name): ?string
{
    $name = trim((string) $name);
    if ($name === '' || basename($name) !== $name || !is_file(dirname(__DIR__) . '/uploads/kegiatan/' . $name)) {
        return null;
    }
    return 'uploads/kegiatan/' . rawurlencode($name);
}

function article_news_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new RuntimeException('Artikel belum dapat dimuat.');
    }
    if ($types !== '' && !mysqli_stmt_bind_param($stmt, $types, ...$params)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Artikel belum dapat dimuat.');
    }
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Artikel belum dapat dimuat.');
    }
    return $stmt;
}

function article_news_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = article_news_query($conn, $sql, $types, $params);
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}

function article_news_categories(mysqli $conn): array
{
    $rows = article_news_rows(
        $conn,
        'SELECT k.id_kategori,k.nama_kategori,COUNT(a.id_artikel) AS article_count
         FROM kategori_kegiatan k
         INNER JOIN artikel a ON a.id_kategori=k.id_kategori AND a.deleted_at IS NULL
         GROUP BY k.id_kategori,k.nama_kategori ORDER BY k.nama_kategori'
    );
    $categories = [];
    foreach ($rows as $row) {
        $categories[(int) $row['id_kategori']] = $row;
    }
    return $categories;
}

function article_news_latest(mysqli $conn, int $limit = 3): array
{
    $limit = min(6, max(1, $limit));
    return article_news_rows(
        $conn,
        'SELECT a.id_artikel,a.id_kategori,a.gambar,a.judul,a.deskripsi,a.tanggal,k.nama_kategori
         FROM artikel a LEFT JOIN kategori_kegiatan k ON k.id_kategori=a.id_kategori
         WHERE a.deleted_at IS NULL ORDER BY a.tanggal DESC,a.id_artikel DESC LIMIT ?',
        'i',
        [$limit]
    );
}

function article_news_list(mysqli $conn, array $input, int $limit = 6): array
{
    $searchInput = $input['q'] ?? '';
    $search = is_string($searchInput) ? mb_substr(trim($searchInput), 0, 120, 'UTF-8') : '';
    $categoryInput = $input['kategori'] ?? null;
    $category = is_scalar($categoryInput) ? filter_var($categoryInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    $category = $category === false ? 0 : (int) $category;
    $categories = article_news_categories($conn);
    if ($category > 0 && !isset($categories[$category])) {
        $category = 0;
    }

    $conditions = ['a.deleted_at IS NULL'];
    $types = '';
    $params = [];
    if ($search !== '') {
        $conditions[] = "(a.judul LIKE ? ESCAPE '!' OR a.deskripsi LIKE ? ESCAPE '!')";
        $pattern = '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $types .= 'ss';
        array_push($params, $pattern, $pattern);
    }
    if ($category > 0) {
        $conditions[] = 'a.id_kategori=?';
        $types .= 'i';
        $params[] = $category;
    }
    $hasFilters = $search !== '' || $category > 0;
    $where = ' WHERE ' . implode(' AND ', $conditions);
    $total = (int) (article_news_rows($conn, 'SELECT COUNT(*) total FROM artikel a' . $where, $types, $params)[0]['total'] ?? 0);
    $pages = max(1, (int) ceil($total / $limit));
    $pageInput = $input['page'] ?? 1;
    $requested = is_scalar($pageInput) ? filter_var($pageInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    $page = min($pages, $requested === false ? 1 : (int) $requested);
    $offset = ($page - 1) * $limit;
    $rows = article_news_rows(
        $conn,
        'SELECT a.id_artikel,a.id_kategori,a.gambar,a.judul,a.deskripsi,a.tanggal,k.nama_kategori
         FROM artikel a LEFT JOIN kategori_kegiatan k ON k.id_kategori=a.id_kategori' . $where .
        ' ORDER BY a.tanggal DESC,a.id_artikel DESC LIMIT ? OFFSET ?',
        $types . 'ii',
        array_merge($params, [$limit, $offset])
    );
    return compact('search', 'category', 'categories', 'hasFilters', 'rows', 'total', 'pages', 'page', 'offset');
}

function article_news_find(mysqli $conn, int $id): ?array
{
    return article_news_rows(
        $conn,
        'SELECT a.id_artikel,a.id_kategori,a.gambar,a.judul,a.deskripsi,a.tanggal,k.nama_kategori
         FROM artikel a LEFT JOIN kategori_kegiatan k ON k.id_kategori=a.id_kategori
         WHERE a.id_artikel=? AND a.deleted_at IS NULL LIMIT 1',
        'i',
        [$id]
    )[0] ?? null;
}

function article_news_related(mysqli $conn, array $article): array
{
    return article_news_rows(
        $conn,
        'SELECT a.id_artikel,a.gambar,a.judul,a.deskripsi,a.tanggal,k.nama_kategori
         FROM artikel a LEFT JOIN kategori_kegiatan k ON k.id_kategori=a.id_kategori
         WHERE a.deleted_at IS NULL AND a.id_artikel<>?
         ORDER BY (a.id_kategori=?) DESC,a.tanggal DESC,a.id_artikel DESC LIMIT 3',
        'ii',
        [(int) $article['id_artikel'], (int) $article['id_kategori']]
    );
}

function article_news_page_url(array $list, int $page): string
{
    $query = [];
    if ($list['search'] !== '') $query['q'] = $list['search'];
    if ($list['category'] > 0) $query['kategori'] = $list['category'];
    if ($page > 1) $query['page'] = $page;
    return 'artikel.php' . ($query ? '?' . http_build_query($query) : '');
}
