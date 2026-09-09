<?php if (!isset($summary)) { http_response_code(404); exit; } ?>
<section class="stats-grid" aria-label="Ringkasan keuangan saya">
<?php foreach ([['Total Pemasukan','pemasukan','arrow-down','income'],['Total Pengeluaran','pengeluaran','arrow-up','expense'],['Saldo','saldo','wallet','balance'],['Jumlah Transaksi','total','receipt','count']] as [$label,$key,$icon,$class]): ?>
    <article class="stat <?= $class ?>"><span class="stat-symbol"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></span><p><?= $label ?></p><strong><?= $key === 'total' ? (int)$summary['total'] : keuangan_rupiah($key === 'saldo' ? $summary['pemasukan']-$summary['pengeluaran'] : $summary[$key]) ?></strong><small><?= $key === 'total' ? 'Transaksi tercatat' : 'Seluruh transaksi Anda' ?></small></article>
<?php endforeach; ?>
</section>
