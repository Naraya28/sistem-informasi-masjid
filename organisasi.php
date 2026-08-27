<?php
include 'config/koneksi.php';
include 'includes/header.php';
include 'includes/navbar.php';

$data = mysqli_query($conn, 
"SELECT * FROM pengurus 

    ORDER BY FIELD(
        jabatan,
        'Ketua',
        'Wakil Ketua',
        'Sekretaris',
        'Bendahara',
        'Humas',
        'Keamanan'
    ),
    
    nama ASC
    
    ");
?>

<section class="organisasi-wrapper">
    <h1 class="organisasi-title">Struktur Organisasi Masjid</h1>

    <div class="organisasi-grid-modern">
        <?php while($row = mysqli_fetch_assoc($data)) { ?>
            <div class="organisasi-card-modern">
                <div class="organisasi-image-modern">
                    <img src="uploads/pengurus/<?= rawurlencode($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='assets/img/default_user.png'">
                </div>

                <div class="organisasi-content-modern">
                    <h3><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="organisasi-jabatan"><?= htmlspecialchars($row['jabatan'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="organisasi-deskripsi"><?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
