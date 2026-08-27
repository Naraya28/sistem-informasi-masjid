<?php
require 'config/koneksi.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = mysqli_prepare($conn, 'SELECT a.*, k.nama_kategori FROM artikel a LEFT JOIN kategori_kegiatan k ON a.id_kategori = k.id_kategori WHERE a.id_artikel = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$page_title = $data ? $data['judul'] : 'Artikel Tidak Ditemukan';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="detail-wrapper">
    <?php if (!$data): ?>
        <div class="detail-card"><p>Artikel tidak ditemukan.</p><a href="artikel.php" class="btn-kembali-artikel">Kembali ke Artikel</a></div>
    <?php else: ?>
        <div class="detail-card">
            <img src="uploads/kegiatan/<?= rawurlencode($data['gambar']) ?>" alt="<?= htmlspecialchars($data['judul'], ENT_QUOTES, 'UTF-8') ?>" class="detail-image" onerror="this.src='assets/img/banner.jpg'">
            <div class="detail-content">
                <span class="detail-kategori"><?= htmlspecialchars($data['nama_kategori'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?></span>
                <p class="detail-tanggal">Diposting pada: <?= date('d-m-Y', strtotime($data['tanggal'])) ?></p>
                <h1 class="detail-judul"><?= htmlspecialchars($data['judul'], ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="detail-deskripsi"><?= nl2br(htmlspecialchars($data['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></div>
                <a href="artikel.php" class="btn-kembali-artikel">Kembali ke Artikel</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
