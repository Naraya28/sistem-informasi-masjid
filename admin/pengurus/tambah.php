<?php
require '../auth.php';
require '../../config/koneksi.php';
$listJabatan = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Humas', 'Keamanan'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? ''); $jabatan = $_POST['jabatan'] ?? ''; $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($nama === '' || !in_array($jabatan, $listJabatan, true)) { $_SESSION['error']='Data pengurus belum lengkap.'; }
    elseif (in_array($jabatan, ['Ketua','Wakil Ketua'], true) && mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pengurus WHERE jabatan='".mysqli_real_escape_string($conn,$jabatan)."' AND deleted_at IS NULL"))>0) { $_SESSION['error']="Jabatan $jabatan sudah terisi."; }
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
<div class="admin-page-header"><div><h1>Tambah Pengurus</h1><p>Lengkapi identitas dan peran pengurus masjid.</p></div></div>
<form method="POST" enctype="multipart/form-data" class="modern-form admin-editor">
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-user"></i></span><div><h2>Informasi Pengurus</h2><p>Data utama yang akan tampil pada struktur organisasi.</p></div></div>
        <div class="form-grid two-columns">
            <div class="form-group"><label>Nama Lengkap</label><input name="nama" maxlength="100" required></div>
            <div class="form-group"><label>Jabatan</label><select name="jabatan" required><option value="">Pilih Jabatan</option><?php foreach($listJabatan as $item): ?><option><?= $item ?></option><?php endforeach; ?></select></div>
        </div>
    </section>
    <section class="editor-section">
        <div class="editor-section-heading"><span><i class="fa fa-camera"></i></span><div><h2>Foto &amp; Deskripsi</h2><p>Gunakan foto portrait yang jelas dan deskripsi singkat.</p></div></div>
        <div class="form-grid media-columns">
            <div class="file-upload portrait-upload" data-file-upload>
                <img data-file-preview hidden alt="Preview foto pengurus">
                <div class="file-upload-copy"><i class="fa fa-cloud-arrow-up"></i><strong>Pilih foto</strong><span>JPG, PNG atau WEBP &bull; maks. 2 MB</span><small><b data-file-name>Belum ada file</b><em data-file-size></em></small></div>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" aria-label="Pilih foto pengurus" required>
            </div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="6" required></textarea><small class="form-hint">Tuliskan tanggung jawab atau keterangan singkat pengurus.</small></div>
        </div>
    </section>
    <div class="editor-actions"><a class="btn-kembali" href="index.php">Kembali</a><button class="btn-simpan" type="submit"><i class="fa fa-floppy-disk"></i> Simpan Data</button></div>
</form>
<?php include '../includes/footer.php'; ?>
