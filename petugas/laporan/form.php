<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../includes/laporan.php';
require_once __DIR__ . '/../../includes/csrf.php';
if (!isset($editing)) { http_response_code(404); exit; }
$owner = (int) $_SESSION['user_id'];
$errors = [];
$values = ['judul' => '', 'jenis' => '', 'tanggal' => date('Y-m-d'), 'deskripsi' => ''];
$oldFile = '';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    if ($editing) {
        $rows = laporan_rows($conn, 'SELECT id,judul_laporan,jenis_laporan,tanggal,deskripsi,file FROM laporan WHERE id=? AND created_by=? AND deleted_at IS NULL', 'ii', [$id, $owner]);
        if (!$rows) { http_response_code(404); exit('Laporan tidak ditemukan atau tidak dapat diakses.'); }
        $data = $rows[0];
        $values = ['judul' => $data['judul_laporan'], 'jenis' => $data['jenis_laporan'], 'tanggal' => $data['tanggal'], 'deskripsi' => $data['deskripsi'] ?? ''];
        $oldFile = (string) ($data['file'] ?? '');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $errors['form'] = 'Permintaan tidak valid. Muat ulang halaman dan coba kembali.';
        } else {
            [$values, $errors] = laporan_validate($_POST);
            $newFile = '';
            if (!$errors && isset($_FILES['file'])) {
                [$newFile, $uploadError] = laporan_upload($_FILES['file']);
                if ($uploadError !== null) { $errors['file'] = $uploadError; }
            }
            if (!$errors && $newFile !== '') {
                if (!move_uploaded_file($_FILES['file']['tmp_name'], laporan_upload_path($newFile))) {
                    $errors['file'] = 'Lampiran tidak dapat disimpan.';
                }
            }
            if (!$errors) {
                if ($editing) {
                    $file = $newFile !== '' ? $newFile : $oldFile;
                    $stmt = laporan_query($conn, 'UPDATE laporan SET judul_laporan=?,jenis_laporan=?,tanggal=?,deskripsi=?,file=? WHERE id=? AND created_by=? AND deleted_at IS NULL', 'sssssii', [$values['judul'], $values['jenis'], $values['tanggal'], $values['deskripsi'], $file, $id, $owner]);
                } else {
                    $stmt = laporan_query($conn, 'INSERT INTO laporan (judul_laporan,jenis_laporan,tanggal,deskripsi,file,created_by) VALUES (?,?,?,?,?,?)', 'sssssi', [$values['judul'], $values['jenis'], $values['tanggal'], $values['deskripsi'], $newFile, $owner]);
                }
                mysqli_stmt_close($stmt);
                $_SESSION['success'] = $editing ? 'Laporan berhasil diperbarui.' : 'Laporan berhasil ditambahkan.';
                header('Location: ' . app_url('petugas/laporan/index.php'), true, 303);
                exit;
            }
            http_response_code(422);
        }
    }
} catch (RuntimeException $error) {
    http_response_code(500);
    $errors['form'] = $error->getMessage();
}
$title = $editing ? 'Edit Laporan' : 'Buat Laporan';
$active = 'laporan';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PENGELOLAAN LAPORAN</p><h1><?= $editing ? 'Edit Laporan' : 'Buat Laporan' ?></h1><p>Lengkapi informasi laporan masjid dengan jelas.</p></div><a class="button secondary" href="index.php">Kembali</a></div>
<section class="panel form-panel">
<?php if ($errors): ?><div class="alert error" role="alert"><strong>Laporan belum disimpan.</strong><ul><?php foreach ($errors as $error): ?><li><?= laporan_escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" class="transaction-form"><?= csrf_field() ?><div class="form-grid">
<div class="field full"><label for="judul">Judul Laporan</label><input id="judul" name="judul" maxlength="200" value="<?= laporan_escape($values['judul']) ?>" required></div>
<div class="field"><label for="jenis">Jenis Laporan</label><select id="jenis" name="jenis" required><option value="">Pilih Jenis</option><?php foreach (laporan_jenis() as $item): ?><option value="<?= laporan_escape($item) ?>" <?= $values['jenis'] === $item ? 'selected' : '' ?>><?= laporan_escape($item) ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="tanggal">Tanggal</label><input id="tanggal" name="tanggal" type="date" value="<?= laporan_escape($values['tanggal']) ?>" required></div>
<div class="field full"><label for="deskripsi">Deskripsi <span class="subtle">(opsional)</span></label><textarea id="deskripsi" name="deskripsi" rows="7" maxlength="65535"><?= laporan_escape($values['deskripsi']) ?></textarea></div>
<div class="field full"><label for="file">Lampiran <span class="subtle">(opsional, maksimal 5 MB)</span></label><input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"><?php if ($oldFile !== ''): ?><small>Lampiran saat ini: <?= laporan_escape(basename($oldFile)) ?>. Pilih file baru hanya jika perlu menggantinya.</small><?php else: ?><small>Format: PDF, DOC, DOCX, JPG, JPEG, PNG.</small><?php endif; ?></div>
</div><div class="form-actions"><button class="button primary" type="submit"><?= $editing ? 'Simpan Perubahan' : 'Simpan Laporan' ?></button><a class="button secondary" href="index.php">Batal</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
