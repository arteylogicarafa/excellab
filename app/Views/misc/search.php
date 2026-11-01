<?php
$pageTitle = 'Buscar';
$links = ['Lecciones' => 'aprender/leccion/', 'Ejercicios' => 'laboratorio/', 'Retos' => 'retos/', 'Casos prácticos' => 'casos/', 'Evaluaciones' => 'evaluaciones/'];
$icons = ['Lecciones' => 'bi-book', 'Ejercicios' => 'bi-pencil-square', 'Retos' => 'bi-trophy', 'Casos prácticos' => 'bi-briefcase', 'Evaluaciones' => 'bi-clipboard-check'];
$total = array_sum(array_map('count', $results));
?>
<div class="page-head"><div><h1>Buscar contenidos</h1><p>Lecciones, ejercicios, retos, casos y evaluaciones.</p></div></div>
<form class="mb-4" method="get" action="<?= url('buscar') ?>"><div class="input-group input-group-lg"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Por ejemplo: BUSCARV, porcentaje, inventario…" minlength="2" autofocus><button class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button></div></form>
<?php if ($q !== '' && mb_strlen($q) < 2): ?><div class="alert alert-warning">Escribe al menos 2 caracteres.</div><?php endif; ?>
<?php if (mb_strlen($q) >= 2): ?>
    <p class="text-muted"><?= $total ?> resultado<?= $total === 1 ? '' : 's' ?> para “<?= e($q) ?>”</p>
    <?php if (!$total): ?><div class="card-x empty"><i class="bi bi-search"></i>No encontramos coincidencias. Prueba con otra palabra.</div><?php endif; ?>
    <?php foreach ($results as $group => $items): if (!$items) continue; ?>
        <h2 class="h6 text-uppercase text-muted mt-4 mb-2" style="letter-spacing:.06em"><?= e($group) ?></h2>
        <div class="card-x">
            <?php foreach ($items as $r): ?>
                <a class="item-row" href="<?= url($links[$group] . $r['id']) ?>"><div class="feed-icon"><i class="bi <?= $icons[$group] ?>"></i></div>
                    <div class="flex-grow-1"><div class="fw-semibold"><?= e($r['title']) ?></div><div class="small text-muted"><?= e(mb_strimwidth((string)$r['extra'], 0, 110, '…')) ?><?= $r['ctx'] ? ' · ' . e($r['ctx']) : '' ?></div></div>
                    <i class="bi bi-chevron-right text-muted"></i></a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
