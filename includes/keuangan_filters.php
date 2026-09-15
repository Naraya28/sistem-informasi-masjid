<?php if (!isset($list)) { http_response_code(404); exit; } ?>
<form method="GET" class="finance-filters filter-bar admin-filter-bar">
    <div class="admin-filter-field admin-filter-search"><label for="cari">Cari transaksi</label><input id="cari" name="cari" maxlength="200" placeholder="Kategori atau keterangan" value="<?= keuangan_escape($list['search']) ?>"></div>
    <div class="admin-filter-field admin-filter-select"><label for="jenis">Jenis transaksi</label><select id="jenis" name="jenis"><option value="">Semua</option><option value="pemasukan" <?= $list['jenis']==='pemasukan'?'selected':'' ?>>Pemasukan</option><option value="pengeluaran" <?= $list['jenis']==='pengeluaran'?'selected':'' ?>>Pengeluaran</option></select></div>
    <div class="admin-filter-actions"><button class="button primary btn" type="submit">Terapkan</button><a class="button secondary" href="index.php">Reset</a></div>
</form>
