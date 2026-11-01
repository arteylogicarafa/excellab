<?php
$pageTitle = 'Inicio';
$first = explode(' ', Auth::user()['name'])[0];
$h = (int)date('G');
$greet = $h < 12 ? 'Buenos días' : ($h < 19 ? 'Buenas tardes' : 'Buenas noches');
$modLabels = array_map(fn($m, $i) => 'M' . ($i + 1), $mods, array_keys($mods));
$modPercents = array_map(fn($m) => $m['lessons_total'] ? round($m['lessons_done'] / $m['lessons_total'] * 100) : 0, $mods);
?>
<div class="hero mb-4">
    <div class="d-flex flex-wrap align-items-center gap-4">
        <div class="ring" style="--p: <?= (float)$overall ?>"><span><?= round($overall) ?>%</span></div>
        <div class="flex-grow-1">
            <h1><?= e($greet) ?>, <?= e($first) ?> 👋</h1>
            <p class="mb-3 opacity-75">Tu progreso general en Excel es del <?= round($overall) ?>%. <?= $next ? 'Sigue donde lo dejaste.' : '¡Completaste todas las lecciones disponibles!' ?></p>
            <?php if ($next): ?>
                <a class="btn btn-light" href="<?= url('aprender/leccion/' . $next['id']) ?>"><i class="bi bi-play-fill"></i> Continuar aprendiendo</a>
                <span class="ms-2 small opacity-75 d-block d-sm-inline mt-2 mt-sm-0"><?= e($next['module_title']) ?> · <?= e($next['title']) ?></span>
            <?php else: ?>
                <a class="btn btn-light" href="<?= url('retos') ?>"><i class="bi bi-trophy"></i> Ir a los retos</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon"><i class="bi bi-collection"></i></div><div><div class="stat-value"><?= $modsDone ?><small class="text-muted fs-6">/<?= count($mods) ?></small></div><div class="stat-label">Módulos completados</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon blue"><i class="bi bi-pencil-square"></i></div><div><div class="stat-value"><?= $counts['attempted'] ?></div><div class="stat-label">Ejercicios realizados</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon amber"><i class="bi bi-star-fill"></i></div><div><div class="stat-value"><?= $counts['points'] ?></div><div class="stat-label">Puntos acumulados</div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card-x stat-card"><div class="stat-icon violet"><i class="bi bi-trophy"></i></div><div><div class="stat-value"><?= $pendingTotal ?></div><div class="stat-label">Retos pendientes</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-x mb-4">
            <div class="card-x-head"><h2>Puntos ganados · últimos 14 días</h2></div>
            <div class="card-x-body"><canvas id="chartPoints" height="110" aria-label="Gráfico de puntos por día" role="img"></canvas></div>
        </div>
        <div class="card-x mb-4">
            <div class="card-x-head"><h2>Avance por módulo</h2><a class="small" href="<?= url('aprender') ?>">Ver módulos</a></div>
            <div class="card-x-body"><canvas id="chartModules" height="90" aria-label="Avance por módulo" role="img"></canvas></div>
        </div>
        <div class="card-x">
            <div class="card-x-head"><h2>Retos pendientes</h2><a class="small" href="<?= url('retos') ?>">Ver todos</a></div>
            <?php if (!$pending): ?><div class="empty"><i class="bi bi-emoji-sunglasses"></i>¡Superaste todos los retos!</div><?php endif; ?>
            <?php foreach ($pending as $c): ?>
                <a class="item-row" href="<?= url('retos/' . $c['id']) ?>">
                    <div class="stat-icon amber" style="width:38px;height:38px;font-size:1.1rem"><i class="bi bi-lightning-charge"></i></div>
                    <div class="flex-grow-1"><div class="fw-semibold"><?= e($c['title']) ?></div><div class="small text-muted"><i class="bi bi-clock"></i> <?= (int)$c['est_minutes'] ?> min · <?= (int)$c['points'] ?> pts</div></div>
                    <span class="chip <?= diff_class($c['difficulty']) ?>"><?= e(diff_label($c['difficulty'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-x mb-4">
            <div class="card-x-head"><h2>Últimas actividades</h2></div>
            <div class="card-x-body py-2">
                <?php if (!$recent): ?><div class="empty py-4"><i class="bi bi-clock-history"></i>Aún no tienes actividad.</div><?php endif; ?>
                <?php foreach ($recent as [$icon, $tone, $text, $when, $link]): ?>
                    <a class="feed-item text-reset" href="<?= url($link) ?>">
                        <div class="feed-icon"><i class="bi <?= e($icon) ?>"></i></div>
                        <div><div><?= e($text) ?></div><div class="small text-muted"><?= e(time_ago($when)) ?></div></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-x">
            <div class="card-x-head"><h2>Logros obtenidos</h2><a class="small" href="<?= url('logros') ?>">Ver todos</a></div>
            <div class="card-x-body">
                <?php if (!$achievements): ?><div class="empty py-3"><i class="bi bi-award"></i>Completa tu primera lección para ganar una insignia.</div><?php endif; ?>
                <div class="row g-2">
                    <?php foreach ($achievements as $a): ?>
                        <div class="col-4"><div class="achv p-2" title="<?= e($a['description']) ?>"><div class="icon" style="width:44px;height:44px;font-size:1.3rem"><i class="bi <?= e($a['icon']) ?>"></i></div><h3 style="font-size:.74rem"><?= e($a['name']) ?></h3></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ob_start(); ?>
<script>
const green = '#217346', soft = 'rgba(22,163,74,.15)';
new Chart(document.getElementById('chartPoints'), {
  type: 'line',
  data: { labels: <?= json_encode($chartPoints['labels']) ?>, datasets: [{ label: 'Puntos', data: <?= json_encode($chartPoints['values']) ?>, borderColor: green, backgroundColor: soft, fill: true, tension: .35, pointRadius: 3 }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('chartModules'), {
  type: 'bar',
  data: { labels: <?= json_encode($modLabels) ?>, datasets: [{ label: '% completado', data: <?= json_encode($modPercents) ?>, backgroundColor: green, borderRadius: 6 }] },
  options: { plugins: { legend: { display: false }, tooltip: { callbacks: { title: (i) => <?= json_encode(array_column($mods, 'title')) ?>[i[0].dataIndex] } } }, scales: { y: { beginAtZero: true, max: 100 } } }
});
</script>
<?php $scripts = ob_get_clean(); ?>
