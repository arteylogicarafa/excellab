<?php
$pageTitle = 'Casos prácticos';
$icons = ['Administración' => 'bi-building', 'Ventas' => 'bi-cart3', 'Inventario' => 'bi-box-seam', 'Recursos Humanos' => 'bi-people', 'Educación' => 'bi-mortarboard'];
?>
<div class="page-head"><div><h1>Casos prácticos</h1><p>Situaciones inspiradas en actividades reales: resuélvelas aquí o entrega tu archivo de Excel.</p></div></div>
<?php if (!$grouped): ?><div class="card-x empty"><i class="bi bi-briefcase"></i>No hay casos disponibles.</div><?php endif; ?>
<?php foreach ($grouped as $cat => $list): ?>
    <h2 class="h5 mt-4 mb-3"><i class="bi <?= $icons[$cat] ?? 'bi-briefcase' ?> text-success"></i> <?= e($cat) ?></h2>
    <div class="row g-3">
        <?php foreach ($list as $c): ?>
            <div class="col-md-6 col-xl-4">
                <a class="card-x module-card" href="<?= url('casos/' . $c['id']) ?>">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="chip <?= $c['mode'] === 'file' ? 'warning' : 'green' ?>"><i class="bi <?= $c['mode'] === 'file' ? 'bi-file-earmark-excel' : 'bi-pc-display' ?>"></i> <?= $c['mode'] === 'file' ? 'Entrega en Excel' : 'En la plataforma' ?></span>
                        <?php if ($c['subs']): ?><span class="chip success"><?= $c['best'] !== null ? round($c['best'], 1) . ' pts' : 'Entregado' ?></span><?php endif; ?>
                    </div>
                    <h3 class="h6 mb-1"><?= e($c['title']) ?></h3>
                    <p class="small text-muted mb-2"><?= e(mb_strimwidth($c['context'], 0, 130, '…')) ?></p>
                    <div class="small text-muted"><i class="bi bi-star"></i> <?= (int)$c['points'] ?> pts</div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
