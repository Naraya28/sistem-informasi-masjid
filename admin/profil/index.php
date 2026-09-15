<?php
require '../auth.php';require '../../config/koneksi.php';$profil=mysqli_fetch_assoc(mysqli_query($conn,'SELECT * FROM profil_masjid LIMIT 1'));$page_title='Profil Masjid';$active_menu='profil';$admin_base='..';include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Profil Masjid</h1><p>Kelola informasi identitas yang tampil pada halaman publik.</p></div></div>
<?php if(!$profil):?><div class="alert alert-error">Data profil belum tersedia di database.</div><?php else:?>
<form action="tambah.php" method="POST" class="modern-form admin-editor settings-editor">
    <input type="hidden" name="id" value="<?= (int)$profil['id_profil'] ?>">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-id-card"></i></span><div><p class="section-label">Identitas</p><h2>Informasi Utama</h2></div></div>
        <div class="form-group"><label>Judul Profil</label><input name="judul" value="<?= htmlspecialchars($profil['judul'],ENT_QUOTES,'UTF-8') ?>" required></div>
        <div class="form-group"><label>Profil Singkat</label><textarea class="textarea-medium" name="profil_singkat" rows="4" required><?= htmlspecialchars($profil['profil_singkat'],ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-landmark"></i></span><div><p class="section-label">Sejarah &amp; Identitas</p><h2>Asal Nama</h2></div></div>
        <div class="form-group"><label>Asal Nama Masjid</label><textarea class="textarea-medium" name="asal_nama" rows="4" required><?= htmlspecialchars($profil['asal_nama'],ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-compass"></i></span><div><p class="section-label">Arah Masjid</p><h2>Visi &amp; Misi</h2></div></div>
        <div class="form-grid two-columns editorial-columns">
            <div class="form-group"><label>Visi</label><textarea class="textarea-short" name="visi" rows="4" required><?= htmlspecialchars($profil['visi'],ENT_QUOTES,'UTF-8') ?></textarea></div>
            <div class="form-group"><label>Misi</label><textarea class="textarea-tall" name="misi" rows="6" required><?= htmlspecialchars($profil['misi'],ENT_QUOTES,'UTF-8') ?></textarea></div>
        </div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-location-dot"></i></span><div><p class="section-label">Lokasi</p><h2>Alamat Masjid</h2></div></div>
        <div class="form-group"><label>Alamat</label><textarea class="textarea-short" name="alamat" rows="3" required><?= htmlspecialchars($profil['alamat'],ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <div class="editor-actions editor-actions-informed"><div class="editor-action-note"><i class="fa fa-circle-info"></i><span>Perubahan akan ditampilkan pada halaman publik.</span></div><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Simpan Perubahan</button></div>
</form>
<?php endif;?>
<?php include '../includes/footer.php';?>
