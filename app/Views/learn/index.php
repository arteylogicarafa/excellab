<?php $pageTitle = 'Aprender Excel'; ?>
<div class="page-head"><div><h1>Aprender Excel</h1><p>Ocho módulos para pasar de cero a aplicar Excel en situaciones reales.</p></div></div>
<div class="row g-3">
    <?php foreach ($mods as $i => $m):
        $pct = $m['lessons_total'] ? round($m['lessons_done'] / $m['lessons_total'] * 100) : 0; ?>
        <div class="col-md-6 col-xl-4">
            <a class="card-x module-card" href="<?= url('aprender/modulo/' . $m['id']) ?>">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon"><i class="bi <?= e($m['icon']) ?>"></i></div>
                    <?php if ($pct === 100): ?><span class="chip success"><i class="bi bi-check-circle-fill"></i> Completado</span>
                    <?php elseif ($pct > 0): ?><span class="chip warning">En curso</span><?php else: ?><span class="chip">Por empezar</span><?php endif; ?>
                </div>
                <div class="module-num">Módulo <?= $i + 1 ?></div>
                <h2 class="h5 mb-1"><?= e($m['title']) ?></h2>
                <p class="text-muted small mb-3"><?= e($m['description']) ?></p>
                <div class="d-flex justify-content-between small text-muted mb-1"><span><?= (int)$m['lessons_done'] ?> de <?= (int)$m['lessons_total'] ?> lecciones</span><span><?= $pct ?>%</span></div>
                <div class="progress"><div class="progress-bar" style="width: <?= $pct ?>%"></div></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
