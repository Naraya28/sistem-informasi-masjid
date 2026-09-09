<?php
// Shared financial queries and validation; callers must enforce their role guard.
function keuangan_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function keuangan_rupiah($value): string
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function keuangan_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new RuntimeException('Query unavailable');
        }
        if ($types !== '') {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Query failed');
        }
        return $stmt;
    } catch (Throwable $error) {
        throw new RuntimeException('Data keuangan belum dapat diproses. Silakan coba lagi.');
    }
}

function keuangan_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = keuangan_query($conn, $sql, $types, $params);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function keuangan_input(array $input, string $key): string
{
    return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
}

function keuangan_validate(array $input): array
{
    $values = [];
    foreach (['tanggal', 'jenis', 'kategori', 'jumlah', 'keterangan'] as $key) {
        $values[$key] = keuangan_input($input, $key);
    }
    $errors = [];
    $date = $values['tanggal'];
    if (!preg_match('/^([1-9][0-9]{3})-([0-9]{2})-([0-9]{2})$/D', $date, $parts)
        || !checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) {
        $errors['tanggal'] = 'Masukkan tanggal kalender yang valid.';
    }
    if (!in_array($values['jenis'], ['pemasukan', 'pengeluaran'], true)) {
        $errors['jenis'] = 'Pilih pemasukan atau pengeluaran.';
    }
    if ($values['kategori'] === '' || mb_strlen($values['kategori'], 'UTF-8') > 100) {
        $errors['kategori'] = 'Kategori wajib diisi, maksimal 100 karakter.';
    }
    if (!preg_match('/^[1-9][0-9]{0,9}$/D', $values['jumlah']) || (float) $values['jumlah'] > 2147483647) {
        $errors['jumlah'] = 'Jumlah harus bilangan bulat positif, maksimal Rp 2.147.483.647.';
    }
    if (mb_strlen($values['keterangan'], 'UTF-8') > 2000) {
        $errors['keterangan'] = 'Keterangan maksimal 2.000 karakter.';
    }
    return [$values, $errors];
}

function keuangan_summary(mysqli $conn, ?int $owner): array
{
    $where = $owner === null ? '' : ' WHERE created_by = ?';
    return keuangan_rows($conn, "SELECT COUNT(*) total, COALESCE(SUM(CASE WHEN jenis='pemasukan' THEN jumlah ELSE 0 END),0) pemasukan, COALESCE(SUM(CASE WHEN jenis='pengeluaran' THEN jumlah ELSE 0 END),0) pengeluaran FROM keuangan" . $where, $owner === null ? '' : 'i', $owner === null ? [] : [$owner])[0];
}

function keuangan_list(mysqli $conn, ?int $owner, array $input, int $limit = 10): array
{
    $search = mb_substr(keuangan_input($input, 'cari'), 0, 200, 'UTF-8');
    $jenis = keuangan_input($input, 'jenis');
    if (!in_array($jenis, ['pemasukan', 'pengeluaran'], true)) {
        $jenis = '';
    }
    $conditions = ['1=1'];
    $types = '';
    $params = [];
    if ($owner !== null) {
        $conditions[] = 'k.created_by = ?';
        $types .= 'i';
        $params[] = $owner;
    }
    if ($jenis !== '') {
        $conditions[] = 'k.jenis = ?';
        $types .= 's';
        $params[] = $jenis;
    }
    if ($search !== '') {
        $conditions[] = "(k.kategori LIKE ? ESCAPE '!' OR k.keterangan LIKE ? ESCAPE '!')";
        $pattern = '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $types .= 'ss';
        $params[] = $pattern;
        $params[] = $pattern;
    }
    $where = ' WHERE ' . implode(' AND ', $conditions);
    $total = (int) keuangan_rows($conn, 'SELECT COUNT(*) total FROM keuangan k' . $where, $types, $params)[0]['total'];
    $pages = max(1, (int) ceil($total / $limit));
    $requested = filter_var($input['page'] ?? 1, FILTER_VALIDATE_INT);
    $page = min($pages, max(1, $requested ?: 1));
    $offset = ($page - 1) * $limit;
    $rows = keuangan_rows($conn, 'SELECT k.*, u.nama_lengkap AS creator FROM keuangan k LEFT JOIN users u ON u.id=k.created_by' . $where . ' ORDER BY k.tanggal DESC,k.id_keuangan DESC LIMIT ? OFFSET ?', $types . 'ii', array_merge($params, [$limit, $offset]));
    return compact('rows', 'total', 'pages', 'page', 'offset', 'search', 'jenis');
}

function keuangan_page_url(array $list, int $page): string
{
    return '?' . http_build_query(['cari' => $list['search'], 'jenis' => $list['jenis'], 'page' => $page]);
}
