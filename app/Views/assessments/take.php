<?php $pageTitle = $a['title']; ?>
<div class="page-head"><div><h1><?= e($a['title']) ?></h1><p>Responde todas las preguntas. Al terminar el tiempo, la evaluación se enviará automáticamente.</p></div></div>
<form id="examForm" method="post" action="<?= url('evaluaciones/intento/' . (int)$t['id'] . '/enviar') ?>" data-confirm="¿Enviar la evaluación? No podrás modificar tus respuestas.">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-9">
            <?php foreach ($questions as $i => $ex): partial('exercise', ['ex' => $ex, 'exam' => true, 'num' => $i + 1]); endforeach; ?>
        </div>
        <div class="col-lg-3">
            <div class="card-x p-3 timer-box text-center">
                <div class="text-muted small">Tiempo restante</div>
                <div class="timer" id="examTimer" data-remaining="<?= (int)$remaining ?>">--:--</div>
                <div class="small text-muted my-2"><?= count($questions) ?> preguntas · aprueba con <?= (int)$a['pass_percent'] ?>%</div>
                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-send"></i> Enviar evaluación</button>
            </div>
        </div>
    </div>
</form>
