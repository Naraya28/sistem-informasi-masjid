<?php
if (!isset($list, $monitoring)) { http_response_code(404); exit; }
?>
<?php if (!$list['rows']): ?>
<div class="empty-state"><h2><?= $list['search'] !== '' || $list['jenis'] !== '' ? 'Transaksi tidak ditemukan.' : 'Belum ada transaksi keuangan.' ?></h2><p><?= $monitoring ? 'Data yang dicatat Bendahara akan ditampilkan di sini.' : 'Catat pemasukan dan pengeluaran agar keuangan tersusun rapi.' ?></p>
<?php if (!$monitoring): ?><a class="button primary" href="<?= keuangan_escape(app_url('bendahara/keuangan/tambah.php')) ?>"><?= $list['search'] !== '' || $list['jenis'] !== '' ? 'Tambah Transaksi' : 'Tambah Transaksi Pertama' ?></a><?php endif; ?></div>
<?php else: ?>
<div class="table-responsive"><table><thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th><th><?= $monitoring ? 'Diinput Oleh' : 'Aksi' ?></th></tr></thead><tbody>
<?php foreach ($list['rows'] as $i => $row): ?>
<tr><td><?= $list['offset'] + $i + 1 ?></td><td class="nowrap"><?= keuangan_escape(date('d-m-Y', strtotime($row['tanggal']))) ?></td><td><span class="type-badge <?= $row['jenis'] === 'pemasukan' ? 'income' : 'expense' ?>"><?= $row['jenis'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' ?></span></td><td class="category"><?= keuangan_escape($row['kategori']) ?></td><td class="description"><?= keuangan_escape($row['keterangan'] ?: '—') ?></td><td class="amount"><?= keuangan_rupiah($row['jumlah']) ?></td><td><?php if ($monitoring): ?><?= keuangan_escape($row['creator'] ?? 'Data Lama') ?><?php else: ?><a class="edit-link" href="<?= keuangan_escape(app_url('bendahara/keuangan/edit.php') . '?id=' . (int)$row['id_keuangan']) ?>">Edit</a><?php endif; ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
