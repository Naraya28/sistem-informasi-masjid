<?php
require_once __DIR__ . '/csrf.php';

function recycle_bin_entity_config(?string $entity = null): array
{
    $entities = [
        'pengurus' => [
            'label' => 'Pengurus', 'table' => 'pengurus', 'pk' => 'id',
            'file_field' => 'foto', 'upload_dir' => dirname(__DIR__) . '/uploads/pengurus',
            'upload_prefix' => 'uploads/pengurus/', 'flow' => 'admin_direct',
        ],
        'artikel' => [
            'label' => 'Artikel', 'table' => 'artikel', 'pk' => 'id_artikel',
            'file_field' => 'gambar', 'upload_dir' => dirname(__DIR__) . '/uploads/kegiatan',
            'upload_prefix' => 'uploads/kegiatan/', 'flow' => 'admin_direct',
        ],
        'pesan' => [
            'label' => 'Pesan', 'table' => 'pesan', 'pk' => 'id',
            'file_field' => null, 'upload_dir' => null, 'upload_prefix' => null,
            'flow' => 'admin_direct',
        ],
        'keuangan' => [
            'label' => 'Keuangan', 'table' => 'keuangan', 'pk' => 'id_keuangan',
            'file_field' => null, 'upload_dir' => null, 'upload_prefix' => null,
            'flow' => 'approval', 'owner_field' => 'created_by',
        ],
        'laporan' => [
            'label' => 'Laporan', 'table' => 'laporan', 'pk' => 'id',
            'file_field' => 'file', 'upload_dir' => dirname(__DIR__) . '/uploads/laporan',
            'upload_prefix' => 'uploads/laporan/', 'flow' => 'approval',
            'owner_field' => 'created_by',
        ],
    ];

    if ($entity === null) {
        return $entities;
    }
    if (!isset($entities[$entity])) {
        throw new RuntimeException('Jenis data Tempat Sampah tidak valid.');
    }
    return $entities[$entity];
}

function recycle_bin_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new RuntimeException('Data Tempat Sampah belum dapat diproses.');
    }
    if ($types !== '' && !mysqli_stmt_bind_param($stmt, $types, ...$params)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Data Tempat Sampah belum dapat diproses.');
    }
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Data Tempat Sampah belum dapat diproses.');
    }
    return $stmt;
}

function recycle_bin_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = recycle_bin_query($conn, $sql, $types, $params);
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}

function recycle_bin_label(string $entity, array $row): string
{
    switch ($entity) {
        case 'pengurus':
            return trim((string) ($row['nama'] ?? '')) ?: 'Pengurus #' . (int) ($row['id'] ?? 0);
        case 'artikel':
            return trim((string) ($row['judul'] ?? '')) ?: 'Artikel #' . (int) ($row['id_artikel'] ?? 0);
        case 'pesan':
            $subject = trim((string) ($row['subjek'] ?? ''));
            $sender = trim((string) ($row['nama'] ?? ''));
            return $subject !== '' ? $subject . ($sender !== '' ? ' — ' . $sender : '') : ($sender ?: 'Pesan #' . (int) ($row['id'] ?? 0));
        case 'keuangan':
            return (trim((string) ($row['kategori'] ?? '')) ?: 'Transaksi') . ' — Rp ' . number_format((int) ($row['jumlah'] ?? 0), 0, ',', '.');
        case 'laporan':
            return trim((string) ($row['judul_laporan'] ?? '')) ?: 'Laporan #' . (int) ($row['id'] ?? 0);
    }
    throw new RuntimeException('Jenis data Tempat Sampah tidak valid.');
}

function recycle_bin_original_file_path(array $config, array $row): ?string
{
    $field = $config['file_field'];
    if ($field === null) {
        return null;
    }
    $name = trim((string) ($row[$field] ?? ''));
    if ($name === '' || basename($name) !== $name) {
        return null;
    }
    return $config['upload_prefix'] . $name;
}

function recycle_bin_safe_file_path(array $config, array $row): array
{
    $field = $config['file_field'];
    if ($field === null) {
        return ['expected' => false, 'path' => null, 'warning' => null];
    }
    $name = trim((string) ($row[$field] ?? ''));
    if ($name === '') {
        return ['expected' => false, 'path' => null, 'warning' => null];
    }
    if (basename($name) !== $name) {
        return ['expected' => true, 'path' => null, 'warning' => 'Path file tersimpan tidak aman; file tidak dihapus.'];
    }
    $directory = realpath((string) $config['upload_dir']);
    if ($directory === false) {
        return ['expected' => true, 'path' => null, 'warning' => 'Folder upload tidak ditemukan; file tidak dihapus.'];
    }
    $candidate = $directory . DIRECTORY_SEPARATOR . $name;
    $path = realpath($candidate);
    if ($path === false || !is_file($path)) {
        return ['expected' => true, 'path' => null, 'warning' => 'File terkait tidak ditemukan; data database tetap dihapus permanen.'];
    }
    $prefix = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (strncmp($path, $prefix, strlen($prefix)) !== 0) {
        return ['expected' => true, 'path' => null, 'warning' => 'Lokasi file berada di luar folder yang diizinkan; file tidak dihapus.'];
    }
    return ['expected' => true, 'path' => $path, 'warning' => null];
}

function recycle_bin_soft_delete(
    mysqli $conn,
    string $entity,
    int $recordId,
    int $actorId,
    string $sourceFlow = 'admin_direct',
    ?int $deleteRequestId = null,
    bool $manageTransaction = true,
    ?int $expectedOwner = null
): array {
    $config = recycle_bin_entity_config($entity);
    if ($recordId < 1 || $actorId < 1 || $sourceFlow !== $config['flow']) {
        throw new RuntimeException('Permintaan pemindahan data tidak valid.');
    }
    if ($sourceFlow === 'approval' && (!$deleteRequestId || $deleteRequestId < 1 || !$expectedOwner || $expectedOwner < 1)) {
        throw new RuntimeException('Referensi persetujuan penghapusan tidak valid.');
    }
    if ($sourceFlow === 'admin_direct') {
        $deleteRequestId = null;
    }

    if ($manageTransaction) {
        mysqli_begin_transaction($conn);
    }
    try {
        $table = $config['table'];
        $pk = $config['pk'];
        $rows = recycle_bin_rows($conn, "SELECT * FROM `$table` WHERE `$pk`=? FOR UPDATE", 'i', [$recordId]);
        $target = $rows[0] ?? null;
        if (!$target) {
            throw new RuntimeException('Data tidak ditemukan.');
        }
        if (!empty($target['deleted_at'])) {
            throw new RuntimeException('Data sudah berada di Tempat Sampah.');
        }
        if ($expectedOwner !== null && (int) ($target[$config['owner_field']] ?? 0) !== $expectedOwner) {
            throw new RuntimeException('Pemilik data berubah atau tidak sesuai dengan permintaan.');
        }
        $activeLifecycle = recycle_bin_rows(
            $conn,
            "SELECT id FROM recycle_bin_items WHERE entity_type=? AND record_id=? AND status='trashed' LIMIT 1 FOR UPDATE",
            'si',
            [$entity, $recordId]
        );
        if ($activeLifecycle) {
            throw new RuntimeException('Data sudah memiliki lifecycle aktif di Tempat Sampah.');
        }

        $update = recycle_bin_query(
            $conn,
            "UPDATE `$table` SET deleted_at=NOW(),deleted_by=?,delete_request_id=? WHERE `$pk`=? AND deleted_at IS NULL",
            'iii',
            [$actorId, $deleteRequestId, $recordId]
        );
        $affected = mysqli_stmt_affected_rows($update);
        mysqli_stmt_close($update);
        if ($affected !== 1) {
            throw new RuntimeException('Data tidak dapat dipindahkan ke Tempat Sampah.');
        }

        $label = mb_substr(recycle_bin_label($entity, $target), 0, 255, 'UTF-8');
        $filePath = recycle_bin_original_file_path($config, $target);
        $insert = recycle_bin_query(
            $conn,
            "INSERT INTO recycle_bin_items (entity_type,record_id,display_label,deleted_by,delete_request_id,source_flow,status,deleted_at,original_file_path,created_at) VALUES (?,?,?,?,?,?,'trashed',NOW(),?,NOW())",
            'sisiiss',
            [$entity, $recordId, $label, $actorId, $deleteRequestId, $sourceFlow, $filePath]
        );
        $itemId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($insert);

        if ($manageTransaction) {
            mysqli_commit($conn);
        }
        return ['item_id' => $itemId, 'entity' => $entity, 'record_id' => $recordId, 'label' => $label];
    } catch (Throwable $error) {
        if ($manageTransaction) {
            mysqli_rollback($conn);
        }
        if ($error instanceof RuntimeException) {
            throw $error;
        }
        throw new RuntimeException('Data belum dapat dipindahkan ke Tempat Sampah.');
    }
}

function recycle_bin_get_item(mysqli $conn, int $itemId, bool $lock = false): ?array
{
    if ($itemId < 1) {
        return null;
    }
    $rows = recycle_bin_rows(
        $conn,
        'SELECT r.*,u.nama_lengkap AS deleted_by_name FROM recycle_bin_items r LEFT JOIN users u ON u.id=r.deleted_by WHERE r.id=? LIMIT 1' . ($lock ? ' FOR UPDATE' : ''),
        'i',
        [$itemId]
    );
    return $rows[0] ?? null;
}

function recycle_bin_restore(mysqli $conn, int $itemId, int $adminId): array
{
    if ($itemId < 1 || $adminId < 1) {
        throw new RuntimeException('Permintaan pemulihan tidak valid.');
    }
    mysqli_begin_transaction($conn);
    try {
        $item = recycle_bin_get_item($conn, $itemId, true);
        if (!$item || $item['status'] !== 'trashed') {
            throw new RuntimeException('Data sudah diproses atau tidak ditemukan di Tempat Sampah.');
        }
        $config = recycle_bin_entity_config($item['entity_type']);
        $table = $config['table'];
        $pk = $config['pk'];
        $recordId = (int) $item['record_id'];
        $rows = recycle_bin_rows($conn, "SELECT * FROM `$table` WHERE `$pk`=? FOR UPDATE", 'i', [$recordId]);
        $target = $rows[0] ?? null;
        if (!$target || empty($target['deleted_at'])) {
            throw new RuntimeException('Record sumber tidak ditemukan atau sudah aktif.');
        }

        if ($item['entity_type'] === 'pengurus' && in_array($target['jabatan'] ?? '', ['Ketua', 'Wakil Ketua'], true)) {
            $duplicate = recycle_bin_rows($conn, 'SELECT id FROM pengurus WHERE jabatan=? AND deleted_at IS NULL AND id<>? LIMIT 1', 'si', [$target['jabatan'], $recordId]);
            if ($duplicate) {
                throw new RuntimeException('Jabatan tersebut sudah diisi pengurus aktif. Sesuaikan data aktif sebelum memulihkan.');
            }
        }

        $restore = recycle_bin_query($conn, "UPDATE `$table` SET deleted_at=NULL,deleted_by=NULL,delete_request_id=NULL WHERE `$pk`=? AND deleted_at IS NOT NULL", 'i', [$recordId]);
        $restored = mysqli_stmt_affected_rows($restore);
        mysqli_stmt_close($restore);
        if ($restored !== 1) {
            throw new RuntimeException('Data tidak dapat dipulihkan.');
        }

        $registry = recycle_bin_query($conn, "UPDATE recycle_bin_items SET status='restored',restored_by=?,restored_at=NOW() WHERE id=? AND status='trashed'", 'ii', [$adminId, $itemId]);
        $registryAffected = mysqli_stmt_affected_rows($registry);
        mysqli_stmt_close($registry);
        if ($registryAffected !== 1) {
            throw new RuntimeException('Status pemulihan tidak dapat disimpan.');
        }
        mysqli_commit($conn);
        return ['entity' => $item['entity_type'], 'label' => $item['display_label']];
    } catch (Throwable $error) {
        mysqli_rollback($conn);
        if ($error instanceof RuntimeException) {
            throw $error;
        }
        throw new RuntimeException('Data belum dapat dipulihkan.');
    }
}

function recycle_bin_purge(mysqli $conn, int $itemId, int $adminId): array
{
    if ($itemId < 1 || $adminId < 1) {
        throw new RuntimeException('Permintaan penghapusan permanen tidak valid.');
    }
    mysqli_begin_transaction($conn);
    try {
        $item = recycle_bin_get_item($conn, $itemId, true);
        if (!$item || $item['status'] !== 'trashed') {
            throw new RuntimeException('Data sudah diproses atau tidak ditemukan di Tempat Sampah.');
        }
        $config = recycle_bin_entity_config($item['entity_type']);
        $table = $config['table'];
        $pk = $config['pk'];
        $recordId = (int) $item['record_id'];
        $rows = recycle_bin_rows($conn, "SELECT * FROM `$table` WHERE `$pk`=? FOR UPDATE", 'i', [$recordId]);
        $target = $rows[0] ?? null;
        if (!$target || empty($target['deleted_at'])) {
            throw new RuntimeException('Record sumber tidak ditemukan atau tidak berstatus terhapus.');
        }
        $file = recycle_bin_safe_file_path($config, $target);

        $delete = recycle_bin_query($conn, "DELETE FROM `$table` WHERE `$pk`=? AND deleted_at IS NOT NULL", 'i', [$recordId]);
        $deleted = mysqli_stmt_affected_rows($delete);
        mysqli_stmt_close($delete);
        if ($deleted !== 1) {
            throw new RuntimeException('Data tidak dapat dihapus permanen.');
        }

        $registry = recycle_bin_query($conn, "UPDATE recycle_bin_items SET status='purged',purged_by=?,purged_at=NOW() WHERE id=? AND status='trashed'", 'ii', [$adminId, $itemId]);
        $registryAffected = mysqli_stmt_affected_rows($registry);
        mysqli_stmt_close($registry);
        if ($registryAffected !== 1) {
            throw new RuntimeException('Status penghapusan permanen tidak dapat disimpan.');
        }
        mysqli_commit($conn);

        $warning = $file['warning'];
        if ($file['path'] !== null && !@unlink($file['path'])) {
            $warning = 'Data sudah dihapus permanen, tetapi file terkait gagal dihapus.';
        }
        return ['entity' => $item['entity_type'], 'label' => $item['display_label'], 'warning' => $warning];
    } catch (Throwable $error) {
        mysqli_rollback($conn);
        if ($error instanceof RuntimeException) {
            throw $error;
        }
        throw new RuntimeException('Data belum dapat dihapus permanen.');
    }
}

function recycle_bin_count_active(mysqli $conn): int
{
    return (int) (recycle_bin_rows($conn, "SELECT COUNT(*) total FROM recycle_bin_items WHERE status='trashed' AND entity_type IN ('pengurus','artikel','pesan','keuangan','laporan')")[0]['total'] ?? 0);
}

function recycle_bin_summary(mysqli $conn): array
{
    $summary = array_fill_keys(array_keys(recycle_bin_entity_config()), 0);
    foreach (recycle_bin_rows($conn, "SELECT entity_type,COUNT(*) total FROM recycle_bin_items WHERE status='trashed' AND entity_type IN ('pengurus','artikel','pesan','keuangan','laporan') GROUP BY entity_type") as $row) {
        if (isset($summary[$row['entity_type']])) {
            $summary[$row['entity_type']] = (int) $row['total'];
        }
    }
    $summary['total'] = array_sum($summary);
    return $summary;
}

function recycle_bin_list(mysqli $conn, array $input, int $limit = 15): array
{
    $search = mb_substr(trim(is_string($input['search'] ?? null) ? $input['search'] : ''), 0, 200, 'UTF-8');
    $entity = is_string($input['entity'] ?? null) ? $input['entity'] : '';
    if ($entity !== '' && !isset(recycle_bin_entity_config()[$entity])) {
        $entity = '';
    }
    $conditions = ["r.status='trashed'", "r.entity_type IN ('pengurus','artikel','pesan','keuangan','laporan')"];
    $types = '';
    $params = [];
    if ($entity !== '') {
        $conditions[] = 'r.entity_type=?';
        $types .= 's';
        $params[] = $entity;
    }
    if ($search !== '') {
        $conditions[] = "r.display_label LIKE ? ESCAPE '!'";
        $types .= 's';
        $params[] = '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
    }
    $where = ' WHERE ' . implode(' AND ', $conditions);
    $total = (int) (recycle_bin_rows($conn, 'SELECT COUNT(*) total FROM recycle_bin_items r' . $where, $types, $params)[0]['total'] ?? 0);
    $pages = max(1, (int) ceil($total / $limit));
    $requested = filter_var($input['page'] ?? 1, FILTER_VALIDATE_INT);
    $page = min($pages, max(1, $requested ?: 1));
    $offset = ($page - 1) * $limit;
    $rows = recycle_bin_rows(
        $conn,
        'SELECT r.*,u.nama_lengkap AS deleted_by_name FROM recycle_bin_items r LEFT JOIN users u ON u.id=r.deleted_by' . $where . ' ORDER BY r.deleted_at DESC,r.id DESC LIMIT ? OFFSET ?',
        $types . 'ii',
        array_merge($params, [$limit, $offset])
    );
    return compact('rows', 'total', 'pages', 'page', 'offset', 'search', 'entity');
}

function recycle_bin_page_url(array $list, int $page): string
{
    return '?' . http_build_query(['search' => $list['search'], 'entity' => $list['entity'], 'page' => $page]);
}
