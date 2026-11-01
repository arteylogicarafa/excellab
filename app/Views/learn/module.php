<?php $pageTitle = $mod['title']; $pct = $lessons ? round($done / count($lessons) * 100) : 0; ?>
<nav class="small mb-2"><a href="<?= url('aprender') ?>">Aprender</a> <span class="text-muted">›</span> <?= e($mod['title']) ?></nav>
<div class="page-head">
    <div class="d-flex align-items-center gap-3"><div class="stat-icon" style="width:56px;height:56px;font-size:1.7rem"><i class="bi <?= e($mod['icon']) ?>"></i></div>
        <div><h1><?= e($mod['title']) ?></h1><p><?= e($mod['description']) ?></p></div></div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-soft" href="<?= url('laboratorio?module=' . (int)$mod['id']) ?>"><i class="bi bi-journal-check"></i> Ejercicios (<?= $exCount ?>)</a>
        <?php if ($assessment): ?><a class="btn btn-soft" href="<?= url('evaluaciones/' . (int)$assessment['id']) ?>"><i class="bi bi-clipboard-check"></i> Evaluación</a><?php endif; ?>
        <?php if ($cert && Auth::role() === 'estudiante'): ?><a class="btn btn-primary" href="<?= url('certificado/' . (int)$mod['id']) ?>"><i class="bi bi-patch-check"></i> Certificado</a><?php endif; ?>
    </div>
</div>
<div class="card-x mb-4 p-3 d-flex align-items-center gap-3">
    <div class="flex-grow-1"><div class="d-flex justify-content-between small mb-1"><span><strong><?= $done ?></strong> de <?= count($lessons) ?> lecciones completadas</span><span><?= $pct ?>%</span></div><div class="progress"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div></div>
</div>
<div class="card-x">
    <?php if (!$lessons): ?><div class="empty"><i class="bi bi-journal"></i>Este módulo aún no tiene lecciones.</div><?php endif; ?>
    <?php foreach ($lessons as $i => $l): ?>
        <a class="item-row" href="<?= url('aprender/leccion/' . $l['id']) ?>">
            <div class="item-check <?= $l['done'] ? 'done' : '' ?>"><?= $l['done'] ? '<i class="bi bi-check-lg"></i>' : '<span class="text-muted small">' . ($i + 1) . '</span>' ?></div>
            <div class="flex-grow-1"><div class="fw-semibold"><?= e($l['title']) ?></div><div class="small text-muted"><?= e($l['description']) ?></div></div>
            <i class="bi bi-chevron-right text-muted"></i>
        </a>
    <?php endforeach; ?>
</div>
