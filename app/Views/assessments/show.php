<?php $pageTitle = $a['title']; $isStudent = Auth::role() === 'estudiante'; ?>
<nav class="small mb-2"><a href="<?= url('evaluaciones') ?>">Evaluaciones</a> <span class="text-muted">›</span> <?= e($a['title']) ?></nav>
<div class="page-head"><div><h1><?= e($a['title']) ?></h1><p><?= e($a['description']) ?></p></div></div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-x p-4">
            <h2 class="h6 mb-3">Información</h2>
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-question-circle text-success"></i> <strong><?= (int)$questions ?></strong> preguntas <?= $a['randomize'] ? '(aleatorias del banco de ' . (int)$pool . ')' : '' ?></li>
                <li class="mb-2"><i class="bi bi-clock text-success"></i> Tiempo límite: <strong><?= (int)$a['time_limit_min'] ?> minutos</strong></li>
                <li class="mb-2"><i class="bi bi-bullseye text-success"></i> Para aprobar: <strong><?= (int)$a['pass_percent'] ?>%</strong></li>
                <li><i class="bi bi-arrow-repeat text-success"></i> Intentos: <strong><?= $a['max_attempts'] ? $finished . ' de ' . (int)$a['max_attempts'] : 'ilimitados' ?></strong></li>
            </ul>
            <?php if (!$isStudent): ?>
                <div class="alert alert-info small mb-0">Modo previsualización: solo los estudiantes pueden realizar la evaluación.</div>
            <?php elseif ($open): ?>
                <a class="btn btn-primary w-100" href="<?= url('evaluaciones/intento/' . (int)$open['id']) ?>"><i class="bi bi-play-fill"></i> Continuar intento en curso</a>
            <?php elseif ($canStart): ?>
                <form method="post" action="<?= url('evaluaciones/' . (int)$a['id'] . '/iniciar') ?>" data-confirm="¿Iniciar la evaluación? El tiempo empezará a correr de inmediato.">
                    <?= csrf_field() ?><button class="btn btn-primary w-100 py-2"><i class="bi bi-play-fill"></i> Comenzar evaluación</button></form>
            <?php else: ?>
                <div class="alert alert-warning small mb-0"><?= $pool ? 'Ya usaste todos los intentos disponibles.' : 'Esta evaluación aún no tiene preguntas.' ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-x"><div class="card-x-head"><h2>Mis intentos</h2></div>
            <?php if (!$attempts): ?><div class="empty py-4">Aún no hay intentos.</div><?php else: ?>
            <div class="table-responsive"><table class="table table-x"><thead><tr><th>Fecha</th><th>Puntaje</th><th>Resultado</th><th></th></tr></thead><tbody>
                <?php foreach ($attempts as $t): ?>
                    <tr><td><?= e(fmt_date($t['started_at'])) ?></td>
                        <td><?= $t['status'] === 'in_progress' ? '—' : (int)$t['score'] . '/' . (int)$t['max_score'] . ' (' . round($t['percent']) . '%)' ?></td>
                        <td><?= $t['status'] === 'in_progress' ? '<span class="chip warning">En curso</span>' : ($t['passed'] ? '<span class="chip success">Aprobado</span>' : '<span class="chip danger">No aprobado</span>') . ($t['status'] === 'expired' ? ' <span class="chip">Tiempo agotado</span>' : '') ?></td>
                        <td class="text-end"><?php if ($t['status'] !== 'in_progress'): ?><a href="<?= url('evaluaciones/resultado/' . $t['id']) ?>">Ver detalle</a><?php endif; ?></td></tr>
                <?php endforeach; ?></tbody></table></div><?php endif; ?>
        </div>
    </div>
</div>
