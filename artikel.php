<?php
require 'config/koneksi.php';
$page_title = 'Artikel Masjid';
$data = mysqli_query($conn, "SELECT a.*, k.nama_kategori FROM artikel a LEFT JOIN kategori_kegiatan k ON a.id_kategori = k.id_kategori ORDER BY a.tanggal DESC");
include 'includes/header.php';
include 'includes/navbar.php';
?>
<div class="artikel-wrapper">
    <h1 class="artikel-title">Artikel Terbaru</h1>
    <?php if (mysqli_num_rows($data) === 0): ?><p class="empty-state">Belum ada artikel.</p><?php endif; ?>
    <?php while ($row = mysqli_fetch_assoc($data)): ?>
        <div class="artikel-horizontal">
            <img src="uploads/kegiatan/<?= rawurlencode($row['gambar']) ?>" alt="<?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='assets/img/banner.jpg'">
            <div class="artikel-content">
                <span class="kategori-user"><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?></span>
                <a href="detail_artikel.php?id=<?= (int) $row['id_artikel'] ?>" class="artikel-link"><?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?></a>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
