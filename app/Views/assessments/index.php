<?php $pageTitle = 'Evaluaciones'; ?>
<div class="page-head"><div><h1>Evaluaciones</h1><p>Pon a prueba lo aprendido. Las preguntas se sortean y se califican automáticamente.</p></div></div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-x">
            <?php if (!$rows): ?><div class="empty"><i class="bi bi-clipboard"></i>No hay evaluaciones disponibles.</div><?php endif; ?>
            <?php foreach ($rows as $a): ?>
                <a class="item-row" href="<?= url('evaluaciones/' . $a['id']) ?>">
                    <div class="item-check <?= $a['passed'] ? 'done' : '' ?>"><?= $a['passed'] ? '<i class="bi bi-check-lg"></i>' : '' ?></div>
                    <div class="flex-grow-1"><div class="fw-semibold"><?= e($a['title']) ?></div>
                        <div class="small text-muted"><i class="bi bi-clock"></i> <?= (int)$a['time_limit_min'] ?> min · aprueba con <?= (int)$a['pass_percent'] ?>% · <?= (int)$a['pool'] ?> preguntas en el banco</div></div>
                    <?php if ($a['tries']): ?><span class="chip <?= $a['passed'] ? 'success' : 'warning' ?>">Mejor: <?= round($a['best']) ?>%</span><?php endif; ?>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-x"><div class="card-x-head"><h2>Historial reciente</h2></div>
            <?php if (!$history): ?><div class="empty py-4">Aún no has realizado evaluaciones.</div><?php endif; ?>
            <?php foreach ($history as $t): ?>
                <a class="item-row" href="<?= url('evaluaciones/resultado/' . $t['id']) ?>">
                    <i class="bi <?= $t['passed'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i>
                    <div class="flex-grow-1 small"><div class="fw-semibold text-truncate" style="max-width:190px"><?= e($t['title']) ?></div><div class="text-muted"><?= e(fmt_date($t['finished_at'])) ?></div></div>
                    <strong><?= round($t['percent']) ?>%</strong>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
