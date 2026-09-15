<?php
require_once __DIR__ . '/../auth.php';
if (!isset($editing)) { http_response_code(404); exit; }
require_once __DIR__ . '/../../includes/keuangan.php';
require_once __DIR__ . '/../../includes/csrf.php';
$owner = (int) $_SESSION['user_id'];
$errors = [];
$values = ['tanggal'=>date('Y-m-d'),'jenis'=>'pemasukan','kategori'=>'','jumlah'=>'','keterangan'=>''];
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
try {
    if ($editing) {
        $rows = keuangan_rows($conn, 'SELECT tanggal,jenis,kategori,jumlah,keterangan FROM keuangan WHERE id_keuangan=? AND created_by=?', 'ii', [$id,$owner]);
        if (!$rows) { http_response_code(404); exit('Transaksi tidak ditemukan atau tidak dapat diakses.'); }
        $values = $rows[0];
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $errors['form'] = 'Permintaan tidak valid. Muat ulang halaman dan coba kembali.';
        } else {
            [$values,$errors] = keuangan_validate($_POST);
            if (!$errors) {
                $params = [$values['tanggal'],$values['jenis'],$values['kategori'],(int)$values['jumlah'],$values['keterangan']];
                if ($editing) {
                    $stmt = keuangan_query($conn, 'UPDATE keuangan SET tanggal=?,jenis=?,kategori=?,jumlah=?,keterangan=? WHERE id_keuangan=? AND created_by=?', 'sssisii', array_merge($params,[$id,$owner]));
                } else {
                    $stmt = keuangan_query($conn, 'INSERT INTO keuangan (tanggal,jenis,kategori,jumlah,keterangan,created_by) VALUES (?,?,?,?,?,?)', 'sssisi', array_merge($params,[$owner]));
                }
                mysqli_stmt_close($stmt);
                $_SESSION['success'] = $editing ? 'Transaksi berhasil diperbarui.' : 'Transaksi berhasil ditambahkan.';
                header('Location: ' . app_url('bendahara/keuangan/index.php'), true, 303);
                exit;
            }
            http_response_code(422);
        }
    }
} catch (RuntimeException $error) { http_response_code(500); $errors['form'] = $error->getMessage(); }
$title = $editing ? 'Edit Transaksi' : 'Tambah Transaksi';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">PENCATATAN KEUANGAN</p><h1><?= $title ?></h1><p><?= $editing ? 'Perbarui data transaksi yang dipilih.' : 'Catat pemasukan atau pengeluaran baru.' ?></p></div></div>
<section class="transaction-editor">
<?php if ($errors): ?><div class="alert error" role="alert"><strong>Transaksi belum disimpan.</strong><ul><?php foreach ($errors as $error): ?><li><?= keuangan_escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" class="transaction-form">
<?= csrf_field() ?>
<section class="form-section">
<div class="form-section-heading"><span><i class="fa-solid fa-receipt" aria-hidden="true"></i></span><div><small>INFORMASI TRANSAKSI</small><h2>Rincian Utama</h2><p>Masukkan tanggal, jenis, kategori, dan nominal transaksi.</p></div></div>
<div class="form-grid">
<div class="field"><label for="jenis">Jenis Transaksi</label><select id="jenis" name="jenis" required><option value="pemasukan" <?= $values['jenis']==='pemasukan'?'selected':'' ?>>Pemasukan</option><option value="pengeluaran" <?= $values['jenis']==='pengeluaran'?'selected':'' ?>>Pengeluaran</option></select></div>
<div class="field"><label for="tanggal">Tanggal</label><input id="tanggal" name="tanggal" type="date" min="1000-01-01" max="9999-12-31" value="<?= keuangan_escape($values['tanggal']) ?>" required></div>
<div class="field"><label for="kategori">Kategori</label><input id="kategori" name="kategori" maxlength="100" value="<?= keuangan_escape($values['kategori']) ?>" placeholder="Contoh: Infak Jumat" required><small>Maksimal 100 karakter.</small></div>
<div class="field"><label for="jumlah">Jumlah</label><div class="currency-input"><span>Rp</span><input id="jumlah" name="jumlah" type="number" inputmode="numeric" min="1" max="2147483647" step="1" value="<?= keuangan_escape($values['jumlah']) ?>" placeholder="Contoh: 150000" required></div><small>Bilangan bulat tanpa titik atau koma.</small></div>
</div>
</section>
<section class="form-section">
<div class="form-section-heading"><span><i class="fa-solid fa-align-left" aria-hidden="true"></i></span><div><small>KETERANGAN</small><h2>Catatan Tambahan</h2><p>Tambahkan informasi pendukung bila diperlukan.</p></div></div>
<div class="field"><label for="keterangan">Keterangan <span class="subtle">(opsional)</span></label><textarea id="keterangan" name="keterangan" rows="5" maxlength="2000" placeholder="Tambahkan rincian transaksi bila diperlukan"><?= keuangan_escape($values['keterangan']) ?></textarea><small>Maksimal 2.000 karakter.</small></div>
</section>
<div class="form-actions"><div class="form-action-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>Pastikan nominal dan jenis transaksi sudah benar.</span></div><div class="form-action-buttons"><a class="button secondary" href="index.php">Kembali</a><button class="button primary" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?= $editing ? 'Simpan Perubahan' : 'Simpan Transaksi' ?></button></div></div>
</form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
