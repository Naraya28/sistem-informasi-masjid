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
    <header class="kepengurusan-heading">
        <p class="section-eyebrow">Pelayanan Masjid</p>
        <h1 class="organisasi-title">Kepengurusan Masjid</h1>
        <p>Informasi struktur yang mendukung pengelolaan dan pelayanan Masjid Al-Firdaus.</p>
    </header>

    <nav class="kepengurusan-tabs" aria-label="Bagian kepengurusan">
        <a href="#takmir"><i class="fa-solid fa-mosque" aria-hidden="true"></i>Takmir Masjid</a>
        <a href="#organisasi"><i class="fa-solid fa-sitemap" aria-hidden="true"></i>Organisasi</a>
    </nav>

    <section class="kepengurusan-section takmir-section" id="takmir">
        <div class="kepengurusan-section-heading">
            <div><p class="section-eyebrow">Takmir Masjid</p><h2>Takmir Masjid Al-Firdaus</h2></div>
        </div>
        <div class="takmir-empty">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
            <div><h3>Data Takmir belum tersedia</h3><p>Informasi anggota Takmir akan ditampilkan pada bagian ini ketika data tersedia.</p></div>
        </div>
    </section>

    <section class="kepengurusan-section" id="organisasi">
        <div class="kepengurusan-section-heading">
            <div><p class="section-eyebrow">Organisasi</p><h2>Struktur Organisasi Masjid</h2></div>
            <span><?= (int) mysqli_num_rows($data) ?> anggota</span>
        </div>
        <div class="organisasi-grid-modern">
            <?php while($row = mysqli_fetch_assoc($data)) { ?>
                <article class="organisasi-card-modern">
                    <div class="organisasi-image-modern">
                        <img src="uploads/pengurus/<?= rawurlencode($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='assets/img/default_user.png'">
                    </div>

                    <div class="organisasi-content-modern">
                        <h3><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="organisasi-jabatan"><?= htmlspecialchars($row['jabatan'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="organisasi-deskripsi"><?= htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php } ?>
        </div>
    </section>
</section>

<?php include 'includes/footer.php'; ?>
