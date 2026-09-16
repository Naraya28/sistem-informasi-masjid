<?php
require '../auth.php';require '../../config/koneksi.php';$id=(int)($_GET['id']??0);$stmt=mysqli_prepare($conn,'SELECT * FROM artikel WHERE id_artikel=? AND deleted_at IS NULL');mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$data=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));if(!$data){$_SESSION['error']='Artikel tidak ditemukan.';header('Location:index.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){$kategori=(int)($_POST['id_kategori']??0);$judul=trim($_POST['judul']??'');$deskripsi=trim($_POST['deskripsi']??'');$gambar=$data['gambar'];if($kategori<1||$judul===''||$deskripsi===''){$_SESSION['error']='Data artikel belum lengkap.';}else{$file=$_FILES['gambar']??null;if($file&&$file['error']===UPLOAD_ERR_OK){$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));$size=getimagesize($file['tmp_name']);if($file['size']>3000000||!in_array($ext,['jpg','jpeg','png','webp'],true)||!$size||$size[0]<=$size[1]){$_SESSION['error']='Gambar baru harus landscape, valid, dan maksimal 3 MB.';}else{$gambar='artikel_'.time().'_'.bin2hex(random_bytes(3)).'.'.$ext;move_uploaded_file($file['tmp_name'],'../../uploads/kegiatan/'.$gambar);}}if(empty($_SESSION['error'])){$update=mysqli_prepare($conn,'UPDATE artikel SET id_kategori=?,gambar=?,judul=?,deskripsi=? WHERE id_artikel=? AND deleted_at IS NULL');mysqli_stmt_bind_param($update,'isssi',$kategori,$gambar,$judul,$deskripsi,$id);mysqli_stmt_execute($update);$_SESSION['success']='Artikel berhasil diperbarui.';header('Location:index.php');exit;}}}
$categories=mysqli_query($conn,'SELECT * FROM kategori_kegiatan ORDER BY nama_kategori');$page_title='Edit Artikel';$active_menu='kegiatan';$admin_base='..';include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Edit Artikel</h1><p>Perbarui presentasi artikel tanpa mengubah alur publikasi.</p></div></div>
<form method="POST" enctype="multipart/form-data" class="modern-form admin-editor article-editor">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-circle-info"></i></span><div><h2>Informasi Artikel</h2><p>Kategori dan judul artikel kegiatan.</p></div></div>
        <div class="form-grid article-meta-grid">
            <div class="form-group"><label>Kategori</label><select name="id_kategori" required><?php while($cat=mysqli_fetch_assoc($categories)):?><option value="<?= (int)$cat['id_kategori'] ?>" <?= (int)$data['id_kategori']===(int)$cat['id_kategori']?'selected':'' ?>><?= htmlspecialchars($cat['nama_kategori'],ENT_QUOTES,'UTF-8') ?></option><?php endwhile;?></select></div>
            <div class="form-group"><label>Judul</label><input name="judul" value="<?= htmlspecialchars($data['judul'],ENT_QUOTES,'UTF-8') ?>" required></div>
        </div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-align-left"></i></span><div><h2>Konten</h2><p>Pastikan informasi kegiatan tetap akurat dan mudah dibaca.</p></div></div>
        <div class="form-group"><label>Deskripsi</label><textarea class="article-content-field" name="deskripsi" rows="8" required><?= htmlspecialchars($data['deskripsi'],ENT_QUOTES,'UTF-8') ?></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-image"></i></span><div><h2>Gambar Artikel</h2><p>Gambar saat ini tetap dipakai jika tidak memilih file baru.</p></div></div>
        <div class="file-upload landscape-upload has-file" data-file-upload>
            <img data-file-preview src="../../uploads/kegiatan/<?= rawurlencode($data['gambar']) ?>" onerror="this.src='../../assets/img/banner.jpg'" alt="Gambar artikel saat ini">
            <div class="file-upload-copy"><i class="fa fa-camera-rotate"></i><strong>Ganti gambar</strong><span>JPG, PNG atau WEBP &bull; landscape &bull; maks. 3 MB</span><small><b data-file-name>Gambar saat ini</b><em data-file-size></em></small></div>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" aria-label="Ganti gambar artikel">
        </div>
    </section>
    <div class="editor-actions"><a class="btn-kembali" href="index.php">Kembali</a><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Update Artikel</button></div>
</form>
<?php include '../includes/footer.php';?>
