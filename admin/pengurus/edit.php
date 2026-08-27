<?php
require '../auth.php'; require '../../config/koneksi.php';
$id=(int)($_GET['id']??0); $stmt=mysqli_prepare($conn,'SELECT * FROM pengurus WHERE id=?'); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $data=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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
  if(empty($_SESSION['error'])){$update=mysqli_prepare($conn,'UPDATE pengurus SET nama=?,jabatan=?,foto=?,deskripsi=? WHERE id=?'); mysqli_stmt_bind_param($update,'ssssi',$nama,$jabatan,$foto,$deskripsi,$id); mysqli_stmt_execute($update); $_SESSION['success']='Data pengurus berhasil diperbarui.'; header('Location:index.php'); exit;}
 }
}
$page_title='Edit Pengurus';$active_menu='pengurus';$admin_base='..';include '../includes/header.php';
?>
<div class="form-card"><h1>Edit Pengurus</h1><form method="POST" enctype="multipart/form-data" class="modern-form"><div class="form-group"><label>Nama</label><input name="nama" value="<?= htmlspecialchars($data['nama'],ENT_QUOTES,'UTF-8') ?>" required></div><div class="form-group"><label>Jabatan</label><select name="jabatan" required><?php foreach($listJabatan as $item):?><option <?= $data['jabatan']===$item?'selected':'' ?>><?= $item ?></option><?php endforeach;?></select></div><div class="form-group"><label>Foto Saat Ini</label><img class="form-preview" src="../../uploads/pengurus/<?= rawurlencode($data['foto']) ?>" onerror="this.src='../../assets/img/default_user.png'" alt=""></div><div class="form-group"><label>Ganti Foto (opsional)</label><input type="file" name="foto" accept="image/jpeg,image/png,image/webp"><small class="form-hint">Gunakan foto portrait/tegak, format JPG/PNG/WEBP, maksimal 2 MB.</small></div><div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="5" required><?= htmlspecialchars($data['deskripsi'],ENT_QUOTES,'UTF-8') ?></textarea></div><button class="btn-simpan">Update Data</button> <a class="btn-kembali" href="index.php">Kembali</a></form></div>
<?php include '../includes/footer.php'; ?>
