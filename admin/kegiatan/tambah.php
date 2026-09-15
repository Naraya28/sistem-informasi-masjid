<?php
require '../auth.php';require '../../config/koneksi.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $kategori=(int)($_POST['id_kategori']??0);$judul=trim($_POST['judul']??'');$deskripsi=trim($_POST['deskripsi']??'');$file=$_FILES['gambar']??null;
 if($kategori<1||$judul===''||$deskripsi===''){$_SESSION['error']='Semua data artikel wajib diisi.';}
 elseif(!$file||$file['error']!==UPLOAD_ERR_OK||$file['size']>3000000){$_SESSION['error']='Gambar wajib diunggah dan maksimal 3 MB.';}
 else{$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));$size=getimagesize($file['tmp_name']);if(!in_array($ext,['jpg','jpeg','png','webp'],true)||!$size){$_SESSION['error']='Format gambar tidak valid.';}elseif($size[0]<=$size[1]){$_SESSION['error']='Gambar artikel harus landscape.';}else{$gambar='artikel_'.time().'_'.bin2hex(random_bytes(3)).'.'.$ext;if(move_uploaded_file($file['tmp_name'],'../../uploads/kegiatan/'.$gambar)){$stmt=mysqli_prepare($conn,'INSERT INTO artikel (id_kategori,gambar,judul,deskripsi,tanggal) VALUES (?,?,?,?,NOW())');mysqli_stmt_bind_param($stmt,'isss',$kategori,$gambar,$judul,$deskripsi);mysqli_stmt_execute($stmt);$_SESSION['success']='Artikel berhasil ditambahkan.';header('Location:index.php');exit;}$_SESSION['error']='Gambar gagal diunggah.';}}
}
$categories=mysqli_query($conn,'SELECT * FROM kategori_kegiatan ORDER BY nama_kategori');$page_title='Tambah Artikel';$active_menu='kegiatan';$admin_base='..';include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Tambah Artikel</h1><p>Susun informasi, konten, dan gambar artikel kegiatan.</p></div></div>
<form method="POST" enctype="multipart/form-data" class="modern-form admin-editor article-editor">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-circle-info"></i></span><div><h2>Informasi Artikel</h2><p>Tentukan kategori dan judul yang mudah dikenali.</p></div></div>
        <div class="form-grid article-meta-grid">
            <div class="form-group"><label>Kategori</label><select name="id_kategori" required><option value="">Pilih Kategori</option><?php while($cat=mysqli_fetch_assoc($categories)):?><option value="<?= (int)$cat['id_kategori'] ?>"><?= htmlspecialchars($cat['nama_kategori'],ENT_QUOTES,'UTF-8') ?></option><?php endwhile;?></select></div>
            <div class="form-group"><label>Judul</label><input name="judul" maxlength="255" required></div>
        </div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-align-left"></i></span><div><h2>Konten</h2><p>Tulis informasi kegiatan secara jelas dan ringkas.</p></div></div>
        <div class="form-group"><label>Deskripsi</label><textarea class="article-content-field" name="deskripsi" rows="8" required></textarea></div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-image"></i></span><div><h2>Gambar Artikel</h2><p>Gambar landscape akan tampil sebagai visual utama artikel.</p></div></div>
        <div class="file-upload landscape-upload" data-file-upload>
            <img data-file-preview hidden alt="Preview gambar artikel">
            <div class="file-upload-copy"><i class="fa fa-cloud-arrow-up"></i><strong>Pilih gambar</strong><span>JPG, PNG atau WEBP &bull; landscape &bull; maks. 3 MB</span><small><b data-file-name>Belum ada file</b><em data-file-size></em></small></div>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" aria-label="Pilih gambar artikel" required>
        </div>
    </section>
    <div class="editor-actions"><a class="btn-kembali" href="index.php">Kembali</a><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Simpan Artikel</button></div>
</form>
<?php include '../includes/footer.php';?>
