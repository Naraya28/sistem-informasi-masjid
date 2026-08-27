<?php
require '../auth.php';
require '../../config/koneksi.php';
$listJabatan = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Humas', 'Keamanan'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? ''); $jabatan = $_POST['jabatan'] ?? ''; $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($nama === '' || !in_array($jabatan, $listJabatan, true)) { $_SESSION['error']='Data pengurus belum lengkap.'; }
    elseif (in_array($jabatan, ['Ketua','Wakil Ketua'], true) && mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pengurus WHERE jabatan='".mysqli_real_escape_string($conn,$jabatan)."'"))>0) { $_SESSION['error']="Jabatan $jabatan sudah terisi."; }
    else {
        $foto=''; $file=$_FILES['foto']??null; $allowed=['jpg','jpeg','png','webp'];
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Foto pengurus wajib diunggah.';
        } elseif ($file['size'] > 2000000) {
            $_SESSION['error'] = 'Ukuran foto pengurus maksimal 2 MB.';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $size = getimagesize($file['tmp_name']);
            if (!in_array($ext, $allowed, true) || !$size) {
                $_SESSION['error'] = 'Foto harus berupa gambar JPG, JPEG, PNG, atau WEBP yang valid.';
            } elseif ($size[1] <= $size[0]) {
                $_SESSION['error'] = 'Foto pengurus harus berbentuk portrait/tegak, bukan landscape atau persegi.';
            } else {
                $foto = 'pengurus_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], '../../uploads/pengurus/' . $foto)) {
                    $stmt = mysqli_prepare($conn, 'INSERT INTO pengurus (nama,jabatan,foto,deskripsi) VALUES (?,?,?,?)');
                    mysqli_stmt_bind_param($stmt, 'ssss', $nama, $jabatan, $foto, $deskripsi);
                    mysqli_stmt_execute($stmt);
                    $_SESSION['success'] = 'Data pengurus berhasil ditambahkan.';
                    header('Location: index.php');
                    exit;
                }
                $_SESSION['error'] = 'Foto gagal diunggah.';
            }
        }
    }
}
$page_title='Tambah Pengurus'; $active_menu='pengurus'; $admin_base='..'; include '../includes/header.php';
?>
<div class="form-card"><h1>Tambah Pengurus</h1><form method="POST" enctype="multipart/form-data" class="modern-form"><div class="form-group"><label>Nama Lengkap</label><input name="nama" maxlength="100" required></div><div class="form-group"><label>Jabatan</label><select name="jabatan" required><option value="">Pilih Jabatan</option><?php foreach($listJabatan as $item): ?><option><?= $item ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Foto (maks. 2 MB)</label><input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required><small class="form-hint">Gunakan foto portrait/tegak, format JPG/PNG/WEBP, maksimal 2 MB.</small></div><div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="5" required></textarea></div><button class="btn-simpan" type="submit">Simpan Data</button> <a class="btn-kembali" href="index.php">Kembali</a></form></div>
<?php include '../includes/footer.php'; ?>
