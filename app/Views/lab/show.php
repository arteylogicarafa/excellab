<?php $pageTitle = $ex['title']; ?>
<nav class="small mb-2"><a href="<?= url('laboratorio') ?>">Laboratorio</a> <span class="text-muted">›</span> <?= e($ex['title']) ?></nav>
<div class="page-head"><div><h1><?= e($ex['title']) ?></h1><p><?= e($ex['module_title']) ?></p></div>
    <div class="d-flex gap-2">
        <?php if ($prevId): ?><a class="btn btn-light" href="<?= url('laboratorio/' . $prevId) ?>"><i class="bi bi-arrow-left"></i></a><?php endif; ?>
        <?php if ($nextId): ?><a class="btn btn-light" href="<?= url('laboratorio/' . $nextId) ?>">Siguiente <i class="bi bi-arrow-right"></i></a><?php endif; ?>
    </div></div>
<div class="row g-4">
    <div class="col-lg-8"><?php partial('exercise', ['ex' => $ex, 'context' => 'lab']); ?></div>
    <div class="col-lg-4">
        <div class="card-x"><div class="card-x-head"><h2>Mis intentos</h2></div>
            <?php if (Auth::role() !== 'estudiante'): ?><div class="empty py-4">Modo previsualización.</div>
            <?php elseif (!$attempts): ?><div class="empty py-4">Aún no has intentado este ejercicio.</div><?php endif; ?>
            <?php foreach ($attempts as $a): ?>
                <div class="item-row">
                    <i class="bi <?= $a['is_correct'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i>
                    <div class="flex-grow-1 small"><div class="text-truncate" style="max-width:180px"><code><?= e($a['answer']) ?></code></div><div class="text-muted"><?= e(fmt_date($a['created_at'])) ?></div></div>
                    <?php if ($a['is_correct']): ?><span class="chip success">+<?= (int)$a['points'] ?></span><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
