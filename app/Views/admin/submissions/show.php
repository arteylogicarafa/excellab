<?php $pageTitle = 'Entrega · ' . $s['case_title']; ?>
<nav class="small mb-2"><a href="<?= url('admin/entregas') ?>">Entregas</a> <span class="text-muted">›</span> Detalle</nav>
<div class="page-head"><div><h1><?= e($s['case_title']) ?></h1><p>Entregado por <strong><?= e($s['student']) ?></strong> (<?= e($s['email']) ?>) el <?= e(fmt_date($s['created_at'])) ?></p></div><?= status_badge($s['status']) ?></div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-x mb-4"><div class="card-x-head"><h2>Contenido de la entrega</h2></div><div class="card-x-body">
            <?php if ($s['file_path']): ?>
                <div class="d-flex align-items-center gap-3"><i class="bi bi-file-earmark-excel text-success" style="font-size:2.4rem"></i><div><div class="fw-semibold"><?= e($s['original_name']) ?></div><a class="btn btn-sm btn-primary mt-1" href="<?= url('archivo/' . (int)$s['id']) ?>"><i class="bi bi-download"></i> Descargar archivo</a></div></div>
            <?php else: foreach ($questions as $i => $q): $r = $results[$i] ?? null; ?>
                <div class="mb-2"><div class="small text-muted"><?= $i + 1 ?>. <?= e($q['label']) ?></div>
                    <div><i class="bi <?= !empty($r['ok']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i> Respuesta del estudiante: <code><?= e($r['answer'] ?? '') ?: '(vacía)' ?></code> <span class="text-muted small">· esperada: <code><?= e($q['answer']) ?></code></span></div></div>
            <?php endforeach; endif; ?>
        </div></div>
        <?php if ($s['criteria']): ?><div class="card-x"><div class="card-x-head"><h2>Criterios de evaluación</h2></div><div class="card-x-body"><?= nl2br(e($s['criteria'])) ?></div></div><?php endif; ?>
    </div>
    <div class="col-lg-5">
        <div class="card-x"><div class="card-x-head"><h2>Calificación</h2></div>
            <form class="card-x-body needs-validation" method="post" action="<?= url('admin/entregas/' . (int)$s['id'] . '/calificar') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="score">Puntaje (0 a <?= (int)$s['max_points'] ?>)</label><input class="form-control" type="number" id="score" name="score" min="0" max="<?= (int)$s['max_points'] ?>" step="0.5" value="<?= e($s['score'] ?? '') ?>" required></div>
                <div class="mb-3"><label class="form-label" for="feedback">Retroalimentación para el estudiante</label><textarea class="form-control" id="feedback" name="feedback" rows="5" maxlength="2000"><?= e($s['feedback'] ?? '') ?></textarea></div>
                <button class="btn btn-primary w-100"><i class="bi bi-check2-circle"></i> Guardar calificación</button>
            </form>
        </div>
    </div>
</div>
