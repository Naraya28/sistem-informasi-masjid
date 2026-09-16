<?php
require '../auth.php'; require '../../config/koneksi.php';
$id=(int)($_GET['id']??0); $stmt=mysqli_prepare($conn,'SELECT * FROM pengurus WHERE id=? AND deleted_at IS NULL'); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $data=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if(!$data){ $_SESSION['error']='Data pengurus tidak ditemukan.'; header('Location:index.php'); exit; }
$listJabatan=['Ketua','Wakil Ketua','Sekretaris','Bendahara','Humas','Keamanan'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $nama=trim($_POST['nama']??''); $jabatan=$_POST['jabatan']??''; $deskripsi=trim($_POST['deskripsi']??''); $foto=$data['foto'];
 if($nama===''||!in_array($jabatan,$listJabatan,true)){$_SESSION['error']='Data pengurus belum lengkap.';}
 else {
  $file=$_FILES['foto']??null;
  if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
   if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Foto baru gagal diterima. Silakan pilih file kembali.';
   } elseif ($file['size'] > 2000000) {
    $_SESSION['error'] = 'Ukuran foto pengurus maksimal 2 MB.';
   } else {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $size = getimagesize($file['tmp_name']);
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true) || !$size) {
     $_SESSION['error'] = 'Foto harus berupa gambar JPG, JPEG, PNG, atau WEBP yang valid.';
    } elseif ($size[1] <= $size[0]) {
     $_SESSION['error'] = 'Foto pengurus harus berbentuk portrait/tegak, bukan landscape atau persegi.';
    } else {
     $fotoBaru = 'pengurus_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
     if (move_uploaded_file($file['tmp_name'], '../../uploads/pengurus/' . $fotoBaru)) {
      $foto = $fotoBaru;
     } else {
      $_SESSION['error'] = 'Foto baru gagal diunggah. Foto lama tetap digunakan.';
     }
    }
   }
  }
  if(empty($_SESSION['error'])){$update=mysqli_prepare($conn,'UPDATE pengurus SET nama=?,jabatan=?,foto=?,deskripsi=? WHERE id=? AND deleted_at IS NULL'); mysqli_stmt_bind_param($update,'ssssi',$nama,$jabatan,$foto,$deskripsi,$id); mysqli_stmt_execute($update); $_SESSION['success']='Data pengurus berhasil diperbarui.'; header('Location:index.php'); exit;}
 }
}
$page_title='Edit Pengurus';$active_menu='pengurus';$admin_base='..';include '../includes/header.php';
?>
<div class="admin-page-header"><div><h1>Edit Pengurus</h1><p>Perbarui identitas pengurus tanpa mengubah struktur data.</p></div></div>
<form method="POST" enctype="multipart/form-data" class="modern-form admin-editor">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-user-pen"></i></span><div><h2>Informasi Pengurus</h2><p>Nama dan jabatan yang tampil pada halaman publik.</p></div></div>
        <div class="form-grid two-columns">
            <div class="form-group"><label>Nama Lengkap</label><input name="nama" value="<?= htmlspecialchars($data['nama'],ENT_QUOTES,'UTF-8') ?>" required></div>
            <div class="form-group"><label>Jabatan</label><select name="jabatan" required><?php foreach($listJabatan as $item):?><option <?= $data['jabatan']===$item?'selected':'' ?>><?= $item ?></option><?php endforeach;?></select></div>
        </div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-camera"></i></span><div><h2>Foto &amp; Deskripsi</h2><p>Foto saat ini tetap digunakan jika tidak memilih file baru.</p></div></div>
        <div class="form-grid media-columns">
            <div class="file-upload portrait-upload has-file" data-file-upload>
                <img data-file-preview src="../../uploads/pengurus/<?= rawurlencode($data['foto']) ?>" onerror="this.src='../../assets/img/default_user.png'" alt="Foto <?= htmlspecialchars($data['nama'],ENT_QUOTES,'UTF-8') ?>">
                <div class="file-upload-copy"><i class="fa fa-camera-rotate"></i><strong>Ganti foto</strong><span>JPG, PNG atau WEBP &bull; maks. 2 MB</span><small><b data-file-name>Foto saat ini</b><em data-file-size></em></small></div>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" aria-label="Ganti foto pengurus">
            </div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="6" required><?= htmlspecialchars($data['deskripsi'],ENT_QUOTES,'UTF-8') ?></textarea><small class="form-hint">Tuliskan tanggung jawab atau keterangan singkat pengurus.</small></div>
        </div>
    </section>
    <div class="editor-actions"><a class="btn-kembali" href="index.php">Kembali</a><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Update Data</button></div>
</form>
<?php include '../includes/footer.php'; ?>
