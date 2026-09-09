<?php if (!isset($summary)) { http_response_code(404); exit; } ?>
<section class="stats-grid" aria-label="Ringkasan laporan saya">
<?php foreach ([['Total Laporan','total','file-lines','count'],['Laporan Bulan Ini','bulan','calendar-days','income'],['Laporan Tahun Ini','tahun','calendar-check','balance'],['Dengan Lampiran','lampiran','paperclip','expense']] as [$label,$key,$icon,$class]): ?>
    <article class="stat <?= $class ?>"><span class="stat-symbol"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></span><p><?= $label ?></p><strong><?= (int) $summary[$key] ?></strong><small>Data milik Anda</small></article>
<?php endforeach; ?>
</section>