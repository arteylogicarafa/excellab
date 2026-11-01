<?php $pageTitle = 'Resultado · ' . $a['title']; $ok = (bool)$t['passed']; ?>
<nav class="small mb-2"><a href="<?= url('evaluaciones') ?>">Evaluaciones</a> <span class="text-muted">›</span> Resultado</nav>
<div class="card-x p-4 mb-4 text-center" style="border-top:5px solid <?= $ok ? '#16a34a' : '#ef4444' ?>">
    <div class="display-5 fw-bold <?= $ok ? 'text-success' : 'text-danger' ?>"><?= round($t['percent']) ?>%</div>
    <h1 class="h4 mb-1"><?= $ok ? '¡Evaluación aprobada!' : 'No alcanzaste el puntaje mínimo' ?></h1>
    <p class="text-muted mb-2"><?= e($a['title']) ?> · <?= (int)$t['score'] ?> de <?= (int)$t['max_score'] ?> puntos · mínimo <?= (int)$a['pass_percent'] ?>%</p>
    <?php if ($t['status'] === 'expired'): ?><span class="chip warning">Se agotó el tiempo</span><?php endif; ?>
    <?php if (Auth::isStaff()): ?><div class="small text-muted mt-1">Estudiante: <?= e($t['student_name']) ?></div><?php endif; ?>
    <div class="mt-3 d-flex gap-2 justify-content-center">
        <a class="btn btn-light" href="<?= url(Auth::isStaff() ? 'admin/seguimiento/' . (int)$t['student_id'] : 'evaluaciones/' . (int)$a['id']) ?>">Volver</a>
        <?php if (!Auth::isStaff()): ?><a class="btn btn-primary" href="<?= url('evaluaciones/' . (int)$a['id']) ?>">Intentar de nuevo</a><?php endif; ?>
    </div>
</div>
<?php if (!$detail): ?><div class="card-x empty"><i class="bi bi-info-circle"></i>No hay detalle de respuestas para este intento.</div><?php endif; ?>
<?php foreach ($questions as $i => $ex): $d = $detail[$ex['id']] ?? null; if (!$d) continue; ?>
    <div class="exercise-card">
        <div class="d-flex gap-2 mb-2 flex-wrap align-items-center"><span class="chip green">Pregunta <?= $i + 1 ?></span>
            <span class="chip <?= $d['ok'] ? 'success' : 'danger' ?>"><i class="bi <?= $d['ok'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>"></i> <?= $d['ok'] ? 'Correcta' : 'Incorrecta' ?></span>
            <span class="chip"><?= (int)$ex['points'] ?> pts</span></div>
        <div class="exercise-statement mb-2"><?= nl2br(e($ex['statement'])) ?></div>
        <?php if (!empty($ex['sheet'])) partial('sheet', ['data' => $ex['sheet']]); ?>
        <div class="small">Tu respuesta: <code><?= $d['answer'] === '' ? '(sin responder)' : e($d['answer']) ?></code></div>
        <?php
        $correctText = $ex['answer_kind'] === 'option'
            ? implode(' / ', array_column(array_filter($ex['options'], fn($o) => $o['is_correct']), 'label'))
            : trim(explode('||', (string)$ex['correct_answer'])[0]);
        ?>
        <?php if (!$d['ok']): ?><div class="small mt-1">Respuesta correcta: <code><?= e($correctText) ?></code></div><?php endif; ?>
        <?php if ($ex['explanation']): ?><div class="small text-muted mt-2"><strong>Explicación:</strong> <?= e($ex['explanation']) ?></div><?php endif; ?>
    </div>
<?php endforeach; ?>
