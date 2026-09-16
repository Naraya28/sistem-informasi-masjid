<?php
require_once __DIR__ . '/csrf.php';

function laporan_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function laporan_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new RuntimeException('Data laporan belum dapat diproses.');
    }
    if ($types !== '' && !mysqli_stmt_bind_param($stmt, $types, ...$params)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Data laporan belum dapat diproses.');
    }
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Data laporan belum dapat diproses.');
    }
    return $stmt;
}

function laporan_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = laporan_query($conn, $sql, $types, $params);
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}

function laporan_jenis(): array
{
    return ['Keuangan', 'Kegiatan', 'Donasi', 'Pembangunan'];
}

function laporan_input(array $input, string $key): string
{
    return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
}

function laporan_validate(array $input): array
{
    $values = [
        'judul' => laporan_input($input, 'judul'),
        'jenis' => laporan_input($input, 'jenis'),
        'tanggal' => laporan_input($input, 'tanggal'),
        'deskripsi' => laporan_input($input, 'deskripsi'),
    ];
    $errors = [];
    if ($values['judul'] === '' || mb_strlen($values['judul'], 'UTF-8') > 200) {
        $errors['judul'] = 'Judul wajib diisi, maksimal 200 karakter.';
    }
    if (!in_array($values['jenis'], laporan_jenis(), true)) {
        $errors['jenis'] = 'Pilih jenis laporan yang tersedia.';
    }
    if (!preg_match('/^([1-9][0-9]{3})-([0-9]{2})-([0-9]{2})$/D', $values['tanggal'], $parts)
        || !checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) {
        $errors['tanggal'] = 'Masukkan tanggal kalender yang valid.';
    }
    if (mb_strlen($values['deskripsi'], 'UTF-8') > 65535) {
        $errors['deskripsi'] = 'Deskripsi terlalu panjang.';
    }
    return [$values, $errors];
}

function laporan_upload(array $file): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['', null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return ['', 'Lampiran tidak dapat diproses.'];
    }
    if ($file['size'] < 1 || $file['size'] > 5000000) {
        return ['', 'Lampiran maksimal 5 MB.'];
    }
    $allowed = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ];
    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!isset($allowed[$extension]) || !class_exists('finfo')) {
        return ['', 'Format lampiran tidak didukung.'];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, $allowed[$extension], true)) {
        return ['', 'Isi lampiran tidak sesuai dengan format yang dipilih.'];
    }
    return ['laporan_' . bin2hex(random_bytes(16)) . '.' . $extension, null];
}

function laporan_upload_path(string $name): string
{
    return dirname(__DIR__) . '/uploads/laporan/' . $name;
}

function laporan_list(mysqli $conn, ?int $owner, array $input, int $limit = 10): array
{
    $search = mb_substr(laporan_input($input, 'search'), 0, 200, 'UTF-8');
    $jenis = laporan_input($input, 'jenis');
    if (!in_array($jenis, laporan_jenis(), true)) {
        $jenis = '';
    }
    $conditions = ['l.deleted_at IS NULL'];
    $types = '';
    $params = [];
    if ($owner !== null) {
        $conditions[] = 'l.created_by = ?';
        $types .= 'i';
        $params[] = $owner;
    }
    if ($search !== '') {
        $conditions[] = '(l.judul_laporan LIKE ? ESCAPE \'!\' OR l.deskripsi LIKE ? ESCAPE \'!\')';
        $pattern = '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $types .= 'ss';
        $params[] = $pattern;
        $params[] = $pattern;
    }
    if ($jenis !== '') {
        $conditions[] = 'l.jenis_laporan = ?';
        $types .= 's';
        $params[] = $jenis;
    }
    $where = ' WHERE ' . implode(' AND ', $conditions);
    $total = (int) (laporan_rows($conn, 'SELECT COUNT(*) total FROM laporan l' . $where, $types, $params)[0]['total'] ?? 0);
    $pages = max(1, (int) ceil($total / $limit));
    $requested = filter_var($input['page'] ?? 1, FILTER_VALIDATE_INT);
    $page = min($pages, max(1, $requested ?: 1));
    $offset = ($page - 1) * $limit;
    $rows = laporan_rows($conn, 'SELECT l.*, u.nama_lengkap AS creator FROM laporan l LEFT JOIN users u ON u.id=l.created_by' . $where . ' ORDER BY l.tanggal DESC, l.id DESC LIMIT ? OFFSET ?', $types . 'ii', array_merge($params, [$limit, $offset]));
    return compact('rows', 'total', 'pages', 'page', 'offset', 'search', 'jenis');
}

function laporan_summary(mysqli $conn, int $owner): array
{
    $row = laporan_rows($conn, "SELECT COUNT(*) total, SUM(tanggal >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) bulan, SUM(YEAR(tanggal)=YEAR(CURDATE())) tahun, SUM(file IS NOT NULL AND file <> '') lampiran FROM laporan WHERE created_by=? AND deleted_at IS NULL", 'i', [$owner])[0];
    return $row ?: ['total' => 0, 'bulan' => 0, 'tahun' => 0, 'lampiran' => 0];
}

function laporan_page_url(array $list, int $page): string
{
    return '?' . http_build_query(['search' => $list['search'], 'jenis' => $list['jenis'], 'page' => $page]);
}
