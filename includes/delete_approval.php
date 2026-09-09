<?php
require_once __DIR__ . '/csrf.php';

function delete_approval_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new RuntimeException('Permintaan penghapusan belum dapat diproses.');
    }
    if ($types !== '' && !mysqli_stmt_bind_param($stmt, $types, ...$params)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Permintaan penghapusan belum dapat diproses.');
    }
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Permintaan penghapusan belum dapat diproses.');
    }
    return $stmt;
}

function delete_approval_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array
{
    $stmt = delete_approval_query($conn, $sql, $types, $params);
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}

function delete_approval_entity(string $entity): bool
{
    return in_array($entity, ['keuangan', 'laporan'], true);
}

function delete_approval_pending_count(mysqli $conn): int
{
    return (int) (delete_approval_rows($conn, "SELECT COUNT(*) total FROM delete_requests WHERE status='pending'")[0]['total'] ?? 0);
}

function delete_approval_pending_count_for(mysqli $conn, string $entity): int
{
    if (!delete_approval_entity($entity)) {
        return 0;
    }
    return (int) (delete_approval_rows($conn, "SELECT COUNT(*) total FROM delete_requests WHERE entity_type=? AND status='pending'", 's', [$entity])[0]['total'] ?? 0);
}

function delete_approval_pending_map(mysqli $conn, string $entity, int $requester, array $recordIds): array
{
    if (!delete_approval_entity($entity) || !$recordIds) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($recordIds), '?'));
    $types = 's' . str_repeat('i', count($recordIds));
    $params = array_merge([$entity], array_map('intval', $recordIds));
    $requesterCondition = '';
    if ($requester > 0) {
        $requesterCondition = ' AND requested_by=?';
        $types = 'si' . str_repeat('i', count($recordIds));
        $params = array_merge([$entity, $requester], array_map('intval', $recordIds));
    }
    $rows = delete_approval_rows($conn, "SELECT record_id, id FROM delete_requests WHERE entity_type=?$requesterCondition AND status='pending' AND record_id IN ($placeholders)", $types, $params);
    $map = [];
    foreach ($rows as $row) {
        $map[(int) $row['record_id']] = (int) $row['id'];
    }
    return $map;
}

function delete_approval_create(mysqli $conn, string $entity, int $recordId, int $requester): bool
{
    if (!delete_approval_entity($entity) || $recordId < 1 || $requester < 1) {
        throw new RuntimeException('Permintaan penghapusan tidak valid.');
    }
    $targetTable = $entity === 'keuangan' ? 'keuangan' : 'laporan';
    $targetId = $entity === 'keuangan' ? 'id_keuangan' : 'id';
    $target = delete_approval_rows($conn, "SELECT $targetId FROM $targetTable WHERE $targetId=? AND created_by=? LIMIT 1", 'ii', [$recordId, $requester]);
    if (!$target) {
        throw new RuntimeException('Data tidak ditemukan atau bukan milik Anda.');
    }
    $pending = delete_approval_rows($conn, "SELECT id FROM delete_requests WHERE entity_type=? AND record_id=? AND requested_by=? AND status='pending' LIMIT 1", 'sii', [$entity, $recordId, $requester]);
    if ($pending) {
        return false;
    }
    $stmt = delete_approval_query($conn, 'INSERT INTO delete_requests (entity_type,record_id,requested_by,status) VALUES (?,?,?,\'pending\')', 'sii', [$entity, $recordId, $requester]);
    mysqli_stmt_close($stmt);
    return true;
}

function delete_approval_target(mysqli $conn, string $entity, int $recordId, int $requester, bool $lock = false): ?array
{
    if (!delete_approval_entity($entity)) {
        return null;
    }
    $table = $entity === 'keuangan' ? 'keuangan' : 'laporan';
    $idColumn = $entity === 'keuangan' ? 'id_keuangan' : 'id';
    $fields = $entity === 'keuangan' ? "$idColumn AS target_id,tanggal,jenis,kategori,jumlah,keterangan,created_by" : "$idColumn AS target_id,tanggal,jenis_laporan AS jenis,judul_laporan AS judul,deskripsi,file,created_by";
    $sql = "SELECT $fields FROM $table WHERE $idColumn=? AND created_by=? LIMIT 1" . ($lock ? ' FOR UPDATE' : '');
    $rows = delete_approval_rows($conn, $sql, 'ii', [$recordId, $requester]);
    return $rows[0] ?? null;
}

function delete_approval_review(mysqli $conn, int $requestId, int $adminId, bool $approve): array
{
    if ($requestId < 1 || $adminId < 1) {
        throw new RuntimeException('Permintaan penghapusan tidak valid.');
    }
    mysqli_begin_transaction($conn);
    try {
        $requests = delete_approval_rows($conn, 'SELECT id,entity_type,record_id,requested_by,status FROM delete_requests WHERE id=? FOR UPDATE', 'i', [$requestId]);
        $request = $requests[0] ?? null;
        if (!$request || $request['status'] !== 'pending') {
            throw new RuntimeException('Permintaan ini sudah diproses atau tidak ditemukan.');
        }
        $target = delete_approval_target($conn, $request['entity_type'], (int) $request['record_id'], (int) $request['requested_by'], true);
        if (!$target) {
            throw new RuntimeException('Data target tidak ditemukan atau kepemilikannya berubah.');
        }
        $attachment = '';
        if ($approve) {
            $table = $request['entity_type'] === 'keuangan' ? 'keuangan' : 'laporan';
            $idColumn = $request['entity_type'] === 'keuangan' ? 'id_keuangan' : 'id';
            $delete = delete_approval_query($conn, "DELETE FROM $table WHERE $idColumn=? AND created_by=?", 'ii', [(int) $request['record_id'], (int) $request['requested_by']]);
            $affected = mysqli_stmt_affected_rows($delete);
            mysqli_stmt_close($delete);
            if ($affected !== 1) {
                throw new RuntimeException('Data target tidak dapat dihapus.');
            }
            $attachment = (string) ($target['file'] ?? '');
        }
        $status = $approve ? 'approved' : 'rejected';
        $update = delete_approval_query($conn, 'UPDATE delete_requests SET status=?,reviewed_by=?,reviewed_at=NOW() WHERE id=? AND status=\'pending\'', 'sii', [$status, $adminId, $requestId]);
        if (mysqli_stmt_affected_rows($update) !== 1) {
            mysqli_stmt_close($update);
            throw new RuntimeException('Permintaan ini sudah diproses.');
        }
        mysqli_stmt_close($update);
        mysqli_commit($conn);
        return ['entity' => $request['entity_type'], 'attachment' => $attachment];
    } catch (Throwable $error) {
        mysqli_rollback($conn);
        if ($error instanceof RuntimeException) {
            throw $error;
        }
        throw new RuntimeException('Permintaan penghapusan belum dapat diproses.');
    }
}

function delete_approval_attachment_path(string $file): ?string
{
    if ($file === '' || basename($file) !== $file) {
        return null;
    }
    $directory = realpath(dirname(__DIR__) . '/uploads/laporan');
    $path = realpath($directory . DIRECTORY_SEPARATOR . $file);
    if ($directory === false || $path === false || !is_file($path)) {
        return null;
    }
    $prefix = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    return strncmp($path, $prefix, strlen($prefix)) === 0 ? $path : null;
}
