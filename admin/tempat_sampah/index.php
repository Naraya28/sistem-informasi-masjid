<?php
require __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/recycle_bin.php';

try {
    $trash = recycle_bin_list($conn, $_GET);
    $summary = recycle_bin_summary($conn);
} catch (RuntimeException $error) {
    http_response_code(500);
    exit(htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$entityConfig = recycle_bin_entity_config();
$otherTotal = $summary['total'] - $summary['pengurus'] - $summary['artikel'];
$page_title = 'Tempat Sampah';
$active_menu = 'tempat_sampah';
$admin_base = '..';
require __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-header trash-page-heading">
    <div><h1>Tempat Sampah</h1><p>Kelola data yang telah dihapus dan masih dapat dipulihkan.</p></div>
</div>

<section class="trash-summary" aria-label="Ringkasan Tempat Sampah">
    <article><span class="trash-summary-icon total"><i class="fa fa-trash-can"></i></span><div><small>Total di Sampah</small><strong><?= (int) $summary['total'] ?></strong></div></article>
    <article><span class="trash-summary-icon people"><i class="fa fa-users"></i></span><div><small>Pengurus</small><strong><?= (int) $summary['pengurus'] ?></strong></div></article>
    <article><span class="trash-summary-icon article"><i class="fa fa-newspaper"></i></span><div><small>Artikel</small><strong><?= (int) $summary['artikel'] ?></strong></div></article>
    <article><span class="trash-summary-icon other"><i class="fa fa-box-archive"></i></span><div><small>Data Lain</small><strong><?= (int) $otherTotal ?></strong></div></article>
</section>

<section class="content-card trash-workspace">
    <form method="GET" class="admin-filter-bar trash-filter">
        <input class="admin-filter-search" name="search" maxlength="200" value="<?= htmlspecialchars($trash['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari data yang dihapus..." aria-label="Cari data">
        <select class="admin-filter-select" name="entity" aria-label="Jenis data">
            <option value="">Semua Jenis Data</option>
            <?php foreach ($entityConfig as $key => $config): ?>
                <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" <?= $trash['entity'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <div class="admin-filter-actions">
            <button class="btn admin-filter-action" type="submit"><i class="fa fa-filter"></i> Filter</button>
            <a class="btn-secondary-ui" href="index.php">Reset</a>
        </div>
    </form>

    <div class="table-meta trash-table-meta"><strong>Data terhapus</strong><span><?= (int) $trash['total'] ?> data &bull; Halaman <?= (int) $trash['page'] ?>/<?= (int) $trash['pages'] ?></span></div>

    <?php if (!$trash['rows']): ?>
        <div class="trash-empty">
            <span><i class="fa fa-trash-can-arrow-up"></i></span>
            <h2>Tempat Sampah Kosong</h2>
            <p>Data yang dihapus akan tampil di sini dan dapat dipulihkan sebelum dihapus permanen.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive trash-table-wrap">
            <table class="trash-table">
                <thead><tr><th>Jenis</th><th>Data</th><th>Dihapus Oleh</th><th>Asal Penghapusan</th><th>Tanggal Dihapus</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($trash['rows'] as $item): $config = $entityConfig[$item['entity_type']]; ?>
                    <tr>
                        <td><span class="trash-type type-<?= htmlspecialchars($item['entity_type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><strong class="trash-label"><?= htmlspecialchars($item['display_label'] ?: $config['label'] . ' #' . (int) $item['record_id'], ENT_QUOTES, 'UTF-8') ?></strong><small>ID <?= (int) $item['record_id'] ?></small></td>
                        <td><?= htmlspecialchars($item['deleted_by_name'] ?: 'Akun tidak tersedia', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="trash-source <?= $item['source_flow'] === 'approval' ? 'approval' : 'direct' ?>"><?= $item['source_flow'] === 'approval' ? 'Persetujuan Admin' : 'Penghapusan Admin' ?></span></td>
                        <td class="nowrap"><?= htmlspecialchars(date('d-m-Y H:i', strtotime($item['deleted_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><div class="trash-actions">
                            <form method="POST" action="restore.php" onsubmit="return confirm('Pulihkan data ini ke daftar aktif?')"><?= csrf_field() ?><input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>"><button class="trash-action restore" type="submit"><i class="fa fa-rotate-left"></i> Restore</button></form>
                            <form method="POST" action="hapus_permanen.php" onsubmit="return confirm('Hapus permanen data ini? Tindakan ini tidak dapat dibatalkan.')"><?= csrf_field() ?><input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>"><button class="trash-action purge" type="submit"><i class="fa fa-trash"></i> Hapus Permanen</button></form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($trash['pages'] > 1): ?>
        <nav class="pagination" aria-label="Halaman Tempat Sampah">
            <?php for ($page = 1; $page <= $trash['pages']; $page++): ?><a class="<?= $page === $trash['page'] ? 'active-page' : '' ?>" href="<?= htmlspecialchars(recycle_bin_page_url($trash, $page), ENT_QUOTES, 'UTF-8') ?>"><?= $page ?></a><?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
