<?php
$pageTitle = 'Excel Challenge';
$groups = ['basico' => [], 'intermedio' => [], 'avanzado' => []];
foreach ($rows as $r) $groups[$r['difficulty']][] = $r;
?>
<div class="page-head"><div><h1>Excel Challenge</h1><p>Resuelve situaciones reales, valida tu resultado y gana puntos e insignias.</p></div>
    <div class="d-flex gap-2"><span class="chip green fs-6 px-3 py-2"><i class="bi bi-trophy"></i> <?= $solved ?>/<?= count($rows) ?> superados</span><span class="chip warning fs-6 px-3 py-2"><i class="bi bi-star-fill"></i> <?= $earned ?> pts</span></div></div>
<?php foreach ($groups as $d => $list): if (!$list) continue; ?>
    <h2 class="h5 mt-4 mb-3"><span class="chip <?= diff_class($d) ?> fs-6"><?= e(diff_label($d)) ?></span></h2>
    <div class="row g-3">
        <?php foreach ($list as $c): ?>
            <div class="col-md-6 col-xl-4">
                <a class="card-x module-card" href="<?= url('retos/' . $c['id']) ?>">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="stat-icon <?= $c['solved'] ? '' : 'amber' ?>" style="width:40px;height:40px"><i class="bi <?= $c['solved'] ? 'bi-trophy-fill' : 'bi-lightning-charge' ?>"></i></div>
                        <?php if ($c['solved']): ?><span class="chip success"><i class="bi bi-check-circle-fill"></i> Superado</span><?php elseif ($c['attempts']): ?><span class="chip warning"><?= (int)$c['attempts'] ?> intentos</span><?php endif; ?>
                    </div>
                    <h3 class="h6 mb-1"><?= e($c['title']) ?></h3>
                    <p class="small text-muted mb-3"><?= e($c['objective']) ?></p>
                    <div class="small text-muted"><i class="bi bi-clock"></i> <?= (int)$c['est_minutes'] ?> min &nbsp; <i class="bi bi-star"></i> <?= (int)$c['points'] ?> pts</div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
<?php if (!$rows): ?><div class="card-x empty"><i class="bi bi-trophy"></i>No hay retos disponibles.</div><?php endif; ?>
