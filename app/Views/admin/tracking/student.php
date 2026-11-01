<?php $pageTitle = $u['name']; ?>
<nav class="small mb-2"><a href="<?= url('admin/seguimiento') ?>">Seguimiento</a> <span class="text-muted">›</span> <?= e($u['name']) ?></nav>
<div class="page-head"><div class="d-flex align-items-center gap-3"><div class="avatar" style="width:52px;height:52px;font-size:1.3rem"><?= e(mb_strtoupper(mb_substr($u['name'], 0, 1))) ?></div><div><h1><?= e($u['name']) ?></h1><p><?= e($u['email']) ?> · último acceso: <?= $u['last_login_at'] ? e(fmt_date($u['last_login_at'])) : 'nunca' ?></p></div></div>
    <div class="d-flex gap-2"><a class="btn btn-soft" href="<?= url('admin/reportes?student=' . (int)$u['id']) ?>"><i class="bi bi-file-earmark-bar-graph"></i> Reporte del estudiante</a><a class="btn btn-light" href="<?= url('admin/estudiantes/' . (int)$u['id'] . '/editar') ?>"><i class="bi bi-pencil"></i> Editar</a></div></div>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon"><i class="bi bi-speedometer2"></i></div><div><div class="stat-value"><?= round($overall) ?>%</div><div class="stat-label">Progreso general</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon amber"><i class="bi bi-star-fill"></i></div><div><div class="stat-value"><?= $counts['points'] ?></div><div class="stat-label">Puntos</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon blue"><i class="bi bi-pencil-square"></i></div><div><div class="stat-value"><?= $counts['exercises'] ?></div><div class="stat-label">Ejercicios correctos</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon violet"><i class="bi bi-trophy"></i></div><div><div class="stat-value"><?= $counts['challenges'] ?></div><div class="stat-label">Retos superados</div></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-x mb-4"><div class="card-x-head"><h2>Avance por módulo</h2></div>
            <?php foreach ($mods as $i => $m): $p = $m['lessons_total'] ? round($m['lessons_done'] / $m['lessons_total'] * 100) : 0; ?>
                <div class="item-row"><div class="flex-grow-1"><div class="d-flex justify-content-between small"><span><strong>M<?= $i + 1 ?></strong> · <?= e($m['title']) ?></span><span><?= (int)$m['lessons_done'] ?>/<?= (int)$m['lessons_total'] ?> · <?= $p ?>%</span></div><div class="progress mt-1"><div class="progress-bar" style="width:<?= $p ?>%"></div></div></div><?= $m['certificate_code'] ? '<span class="chip success">Certificado</span>' : '' ?></div>
            <?php endforeach; ?></div>
        <div class="card-x mb-4"><div class="card-x-head"><h2>Últimos ejercicios</h2></div>
            <div class="table-responsive"><table class="table table-x"><thead><tr><th>Fecha</th><th>Ejercicio</th><th>Respuesta</th><th>Resultado</th></tr></thead><tbody>
                <?php if (!$exAttempts): ?><tr><td colspan="4" class="text-center text-muted py-3">Sin intentos.</td></tr><?php endif; ?>
                <?php foreach ($exAttempts as $a): ?><tr><td class="small text-nowrap"><?= e(fmt_date($a['created_at'])) ?></td><td><?= e($a['title']) ?></td><td><code><?= e(mb_strimwidth($a['answer'], 0, 40, '…')) ?></code></td><td><?= $a['is_correct'] ? '<span class="chip success">+' . (int)$a['points'] . '</span>' : '<span class="chip danger">Incorrecto</span>' ?></td></tr><?php endforeach; ?></tbody></table></div></div>
        <div class="card-x"><div class="card-x-head"><h2>Retos</h2></div>
            <div class="table-responsive"><table class="table table-x"><thead><tr><th>Fecha</th><th>Reto</th><th>Tiempo</th><th>Resultado</th></tr></thead><tbody>
                <?php if (!$chAttempts): ?><tr><td colspan="4" class="text-center text-muted py-3">Sin intentos.</td></tr><?php endif; ?>
                <?php foreach ($chAttempts as $a): ?><tr><td class="small text-nowrap"><?= e(fmt_date($a['created_at'])) ?></td><td><?= e($a['title']) ?></td><td><?= (int)$a['seconds_spent'] ?> s</td><td><?= $a['is_correct'] ? '<span class="chip success">Superado</span>' : '<span class="chip danger">Fallido</span>' ?></td></tr><?php endforeach; ?></tbody></table></div></div>
    </div>
    <div class="col-lg-5">
        <div class="card-x mb-4"><div class="card-x-head"><h2>Puntos · 14 días</h2></div><div class="card-x-body"><canvas id="ch" height="140"></canvas></div></div>
        <div class="card-x mb-4"><div class="card-x-head"><h2>Evaluaciones</h2></div>
            <?php if (!$asAttempts): ?><div class="empty py-3">Sin evaluaciones.</div><?php endif; ?>
            <?php foreach ($asAttempts as $t): ?><a class="item-row" href="<?= url('evaluaciones/resultado/' . $t['id']) ?>"><i class="bi <?= $t['passed'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i><div class="flex-grow-1 small"><div class="fw-semibold"><?= e($t['title']) ?></div><div class="text-muted"><?= e(fmt_date($t['finished_at'])) ?></div></div><strong><?= round($t['percent']) ?>%</strong></a><?php endforeach; ?></div>
        <div class="card-x mb-4"><div class="card-x-head"><h2>Casos prácticos</h2></div>
            <?php if (!$cases): ?><div class="empty py-3">Sin entregas.</div><?php endif; ?>
            <?php foreach ($cases as $c): ?><a class="item-row" href="<?= url('admin/entregas/' . $c['id']) ?>"><div class="flex-grow-1 small"><div class="fw-semibold"><?= e($c['title']) ?></div><div class="text-muted"><?= e(fmt_date($c['created_at'])) ?></div></div><?= status_badge($c['status']) ?> <strong><?= $c['score'] !== null ? round($c['score'], 1) : '—' ?></strong></a><?php endforeach; ?></div>
        <div class="card-x mb-4"><div class="card-x-head"><h2>Insignias (<?= count($achievements) ?>)</h2></div><div class="card-x-body d-flex flex-wrap gap-2">
            <?php if (!$achievements): ?><span class="text-muted small">Sin insignias todavía.</span><?php endif; ?>
            <?php foreach ($achievements as $a): ?><span class="chip warning" title="<?= e($a['description']) ?>"><i class="bi <?= e($a['icon']) ?>"></i> <?= e($a['name']) ?></span><?php endforeach; ?></div></div>
        <div class="card-x"><div class="card-x-head"><h2>Historial de actividad</h2></div><div class="card-x-body py-2">
            <?php if (!$log): ?><div class="empty py-3">Sin registros.</div><?php endif; ?>
            <?php foreach ($log as $l): ?><div class="feed-item"><div class="feed-icon"><i class="bi bi-clock-history"></i></div><div><div><?= e(str_replace('_', ' ', $l['action'])) ?><?= $l['detail'] ? ' · ' . e(mb_strimwidth($l['detail'], 0, 60, '…')) : '' ?></div><div class="small text-muted"><?= e(fmt_date($l['created_at'])) ?></div></div></div><?php endforeach; ?></div></div>
    </div>
</div>
<?php ob_start(); ?>
<script>new Chart(document.getElementById('ch'), { type: 'line', data: { labels: <?= json_encode($chart['labels']) ?>, datasets: [{ data: <?= json_encode($chart['values']) ?>, borderColor: '#217346', backgroundColor: 'rgba(22,163,74,.15)', fill: true, tension: .35 }] }, options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });</script>
<?php $scripts = ob_get_clean(); ?>
