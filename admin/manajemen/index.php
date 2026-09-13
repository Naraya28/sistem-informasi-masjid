<?php
require '../auth.php';require '../../config/koneksi.php';$row=mysqli_fetch_assoc(mysqli_query($conn,'SELECT * FROM manajemen_masjid LIMIT 1'));if(!$row){mysqli_query($conn,"INSERT INTO manajemen_masjid (judul) VALUES ('Manajemen Masjid')");$row=mysqli_fetch_assoc(mysqli_query($conn,'SELECT * FROM manajemen_masjid LIMIT 1'));}
if($_SERVER['REQUEST_METHOD']==='POST'){$judul=trim($_POST['judul']??'');$deskripsi=trim($_POST['deskripsi']??'');$sejarah=trim($_POST['sejarah']??'');$studi=trim($_POST['studi_banding']??'');$kontak=trim($_POST['kontak']??'');if($judul===''){$_SESSION['error']='Judul wajib diisi.';}elseif($kontak!==''&&!preg_match('/^[0-9+() .-]{6,50}$/',$kontak)){$_SESSION['error']='Format kontak tidak valid.';}else{$manajemenId=(int)$row['id'];$stmt=mysqli_prepare($conn,'UPDATE manajemen_masjid SET judul=?,deskripsi=?,sejarah=?,studi_banding=?,kontak=? WHERE id=?');mysqli_stmt_bind_param($stmt,'sssssi',$judul,$deskripsi,$sejarah,$studi,$kontak,$manajemenId);mysqli_stmt_execute($stmt);$_SESSION['success']='Data manajemen berhasil diperbarui.';header('Location:index.php');exit;}}
$page_title='Manajemen Masjid';$active_menu='manajemen';$admin_base='..';include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Manajemen Masjid</h1><p>Atur informasi pengelolaan yang tampil kepada pengunjung.</p></div></div>
<form method="POST" class="modern-form admin-editor settings-editor">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-circle-info"></i></span><div><p class="section-label">Informasi Utama</p><h2>Gambaran Manajemen</h2></div></div>
        <div class="form-group"><label>Judul</label><input name="judul" value="<?= htmlspecialchars($row['judul']??'',ENT_QUOTES,'UTF-8') ?>" required></div>
        <div class="form-group"><label>Deskripsi</label><textarea class="textarea-medium" name="deskripsi" rows="4"><?= htmlspecialchars($row['deskripsi']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-clock-rotate-left"></i></span><div><p class="section-label">Sejarah</p><h2>Perjalanan Pengelolaan</h2></div></div>
        <div class="form-group"><label>Sejarah</label><textarea class="textarea-tall" name="sejarah" rows="6"><?= htmlspecialchars($row['sejarah']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-people-arrows"></i></span><div><p class="section-label">Studi Banding</p><h2>Program Pembelajaran</h2></div></div>
        <div class="form-group"><label>Studi Banding</label><textarea class="textarea-medium" name="studi_banding" rows="4"><?= htmlspecialchars($row['studi_banding']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-address-book"></i></span><div><p class="section-label">Kontak Manajemen</p><h2>Narahubung</h2></div></div>
        <div class="form-group compact-field"><label>Kontak</label><input name="kontak" maxlength="50" value="<?= htmlspecialchars($row['kontak']??'',ENT_QUOTES,'UTF-8') ?>"><small class="form-hint">Gunakan nomor kontak yang aktif.</small></div>
    </section>
    <div class="editor-actions editor-actions-informed"><div class="editor-action-note"><i class="fa fa-circle-info"></i><span>Perubahan akan diterapkan pada halaman publik Manajemen Masjid.</span></div><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Simpan Perubahan</button></div>
</form>
<?php include '../includes/footer.php';?>
