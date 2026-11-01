<?php $pageTitle = 'Panel docente'; ?>
<div class="page-head"><div><h1>Panel docente</h1><p>Resumen del curso de Paquetes Informáticos.</p></div>
    <div class="d-flex gap-2 flex-wrap"><a class="btn btn-soft" href="<?= url('admin/estudiantes/nuevo') ?>"><i class="bi bi-person-plus"></i> Nuevo estudiante</a><a class="btn btn-primary" href="<?= url('admin/reportes') ?>"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></div></div>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['bi-people', '', $kpi['students'], 'Total de estudiantes'],
        ['bi-person-check', 'blue', $kpi['active'], 'Activos (últimos 30 días)'],
        ['bi-collection', '', $kpi['modules'], 'Módulos disponibles'],
        ['bi-pencil-square', 'violet', $kpi['exercises'], 'Ejercicios registrados'],
        ['bi-clipboard-check', 'amber', $kpi['assessments'], 'Evaluaciones realizadas'],
        ['bi-graph-up', '', $kpi['average'] . '%', 'Promedio general de resultados'],
    ] as [$icon, $tone, $val, $label]): ?>
        <div class="col-6 col-md-4"><div class="card-x stat-card"><div class="stat-icon <?= $tone ?>"><i class="bi <?= $icon ?>"></i></div><div><div class="stat-value"><?= e($val) ?></div><div class="stat-label"><?= e($label) ?></div></div></div></div>
    <?php endforeach; ?>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="card-x"><div class="card-x-head"><h2>Actividad de los estudiantes · 14 días</h2></div><div class="card-x-body"><canvas id="chActivity" height="100" role="img" aria-label="Actividad diaria"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card-x h-100"><div class="card-x-head"><h2>Resultados de evaluaciones</h2></div><div class="card-x-body d-grid place-items-center"><?php if ($passed + $failed === 0): ?><div class="empty">Sin evaluaciones aún</div><?php else: ?><canvas id="chAssess" height="200" role="img" aria-label="Aprobados y no aprobados"></canvas><?php endif; ?></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card-x"><div class="card-x-head"><h2>Avance promedio por módulo</h2></div><div class="card-x-body"><canvas id="chModules" height="150" role="img" aria-label="Avance por módulo"></canvas></div></div></div>
    <div class="col-lg-6">
        <div class="card-x mb-4"><div class="card-x-head"><h2>Entregas por revisar <span class="badge text-bg-warning ms-1"><?= $kpi['pending'] ?></span></h2><a class="small" href="<?= url('admin/entregas') ?>">Ver todas</a></div>
            <?php if (!$pendingSubs): ?><div class="empty py-4">No hay entregas pendientes.</div><?php endif; ?>
            <?php foreach ($pendingSubs as $s): ?><a class="item-row" href="<?= url('admin/entregas/' . $s['id']) ?>"><div class="feed-icon"><i class="bi bi-file-earmark-excel"></i></div><div class="flex-grow-1"><div class="fw-semibold"><?= e($s['title']) ?></div><div class="small text-muted"><?= e($s['name']) ?> · <?= e(time_ago($s['created_at'])) ?></div></div><i class="bi bi-chevron-right text-muted"></i></a><?php endforeach; ?></div>
        <div class="card-x"><div class="card-x-head"><h2>Estudiantes con más puntos</h2><a class="small" href="<?= url('admin/seguimiento') ?>">Seguimiento</a></div>
            <?php foreach ($top as $s): ?><a class="item-row" href="<?= url('admin/seguimiento/' . $s['id']) ?>"><div class="avatar"><?= e(mb_strtoupper(mb_substr($s['name'], 0, 1))) ?></div><div class="flex-grow-1"><div class="fw-semibold"><?= e($s['name']) ?></div><div class="progress mt-1" style="height:.35rem"><div class="progress-bar" style="width:<?= (float)$s['progress'] ?>%"></div></div></div><strong><?= (int)$s['points'] ?> pts</strong></a><?php endforeach; ?></div>
    </div>
</div>
<?php ob_start(); ?>
<script>
const g = '#217346';
new Chart(document.getElementById('chActivity'), { type: 'bar', data: { labels: <?= json_encode($dayLabels) ?>, datasets: [{ label: 'Actividades', data: <?= json_encode($dayValues) ?>, backgroundColor: g, borderRadius: 5 }] }, options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
<?php if ($passed + $failed > 0): ?>new Chart(document.getElementById('chAssess'), { type: 'doughnut', data: { labels: ['Aprobados', 'No aprobados'], datasets: [{ data: [<?= $passed ?>, <?= $failed ?>], backgroundColor: [g, '#e5e7eb'] }] }, options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } } });<?php endif; ?>
new Chart(document.getElementById('chModules'), { type: 'bar', data: { labels: <?= json_encode(array_map(fn($m, $i) => 'M' . ($i + 1), $moduleStats, array_keys($moduleStats))) ?>, datasets: [{ label: '% promedio', data: <?= json_encode(array_column($moduleStats, 'avg')) ?>, backgroundColor: '#16a34a', borderRadius: 5 }] }, options: { indexAxis: 'y', plugins: { legend: { display: false }, tooltip: { callbacks: { title: i => <?= json_encode(array_column($moduleStats, 'title')) ?>[i[0].dataIndex] } } }, scales: { x: { beginAtZero: true, max: 100 } } } });
</script>
<?php $scripts = ob_get_clean(); ?>
