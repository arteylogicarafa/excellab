<?php $pageTitle = $lesson['title']; ?>
<nav class="small mb-2"><a href="<?= url('aprender') ?>">Aprender</a> <span class="text-muted">›</span> <a href="<?= url('aprender/modulo/' . (int)$lesson['mid']) ?>"><?= e($lesson['module_title']) ?></a> <span class="text-muted">›</span> <?= e($lesson['title']) ?></nav>
<div class="page-head">
    <div><div class="module-num">Lección <?= $position ?> de <?= $total ?></div><h1><?= e($lesson['title']) ?></h1><p><?= e($lesson['description']) ?></p></div>
    <?php if ($done): ?><span class="chip success fs-6 px-3 py-2"><i class="bi bi-check-circle-fill"></i> Lección completada</span><?php endif; ?>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-x mb-4"><div class="card-x-head"><h2><i class="bi bi-book text-success"></i> Explicación</h2></div>
            <div class="card-x-body lesson-body"><?= clean_html((string)$lesson['theory']) ?></div></div>
        <div class="card-x mb-4"><div class="card-x-head"><h2><i class="bi bi-lightbulb text-success"></i> Ejemplo práctico</h2></div>
            <div class="card-x-body lesson-body">
                <?= clean_html((string)$lesson['example']) ?>
                <?php if ($sheet): partial('sheet', ['data' => $sheet, 'caption' => 'Hoja de ejemplo']); endif; ?>
                <?php if (!empty($lesson['image_url'])): ?>
                    <img class="lesson-img mt-2" src="<?= e(preg_match('#^https?://#', $lesson['image_url']) ? $lesson['image_url'] : asset(preg_replace('#^assets/#', '', $lesson['image_url']))) ?>" alt="Ilustración de la lección: <?= e($lesson['title']) ?>">
                <?php endif; ?>
            </div>
        </div>
        <?php if ($exercises): ?>
            <h2 class="h5 mb-3"><i class="bi bi-pencil-square text-success"></i> Ejercicio de comprobación</h2>
            <?php foreach ($exercises as $ex): partial('exercise', ['ex' => $ex, 'context' => 'lesson']); endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="card-x p-3 position-sticky" style="top: 80px">
            <?php if (Auth::role() === 'estudiante'): ?>
                <?php if (!$done): ?>
                    <form method="post" action="<?= url('aprender/leccion/' . (int)$lesson['id'] . '/completar') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-primary w-100 py-2"><i class="bi bi-check2-circle"></i> Marcar como completada</button>
                    </form>
                    <div class="small text-muted mt-2">Resuelve el ejercicio de comprobación y marca la lección cuando la hayas entendido.</div>
                <?php else: ?>
                    <div class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> ¡Ya completaste esta lección!</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="small text-muted"><i class="bi bi-eye"></i> Estás en modo previsualización: el progreso no se registra.</div>
            <?php endif; ?>
            <hr>
            <div class="d-flex justify-content-between gap-2">
                <?php if ($prev): ?><a class="btn btn-light btn-sm" href="<?= url('aprender/leccion/' . $prev['id']) ?>"><i class="bi bi-arrow-left"></i> Anterior</a><?php else: ?><span></span><?php endif; ?>
                <?php if ($next): ?><a class="btn btn-light btn-sm" href="<?= url('aprender/leccion/' . $next['id']) ?>">Siguiente <i class="bi bi-arrow-right"></i></a><?php endif; ?>
            </div>
        </div>
    </div>
</div>
