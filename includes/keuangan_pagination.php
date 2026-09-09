<?php if (!isset($list)) { http_response_code(404); exit; } ?>
<nav class="pagination" aria-label="Halaman transaksi"><span><?= (int)$list['total'] ?> transaksi · Halaman <?= $list['page'] ?> dari <?= $list['pages'] ?></span>
<?php if ($list['page'] > 1): ?><a href="<?= keuangan_escape(keuangan_page_url($list,$list['page']-1)) ?>">Sebelumnya</a><?php endif; ?>
<?php for ($p=max(1,$list['page']-2);$p<=min($list['pages'],$list['page']+2);$p++): ?><a <?= $p===$list['page']?'aria-current="page"':'' ?> href="<?= keuangan_escape(keuangan_page_url($list,$p)) ?>"><?= $p ?></a><?php endfor; ?>
<?php if ($list['page'] < $list['pages']): ?><a href="<?= keuangan_escape(keuangan_page_url($list,$list['page']+1)) ?>">Berikutnya</a><?php endif; ?>
</nav>
