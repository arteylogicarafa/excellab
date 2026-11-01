<?php $pageTitle = 'Mis logros'; $earned = count(array_filter($rows, fn($r) => $r['awarded_at'])); ?>
<div class="page-head"><div><h1>Mis logros</h1><p>Has desbloqueado <strong><?= $earned ?></strong> de <?= count($rows) ?> insignias.</p></div></div>
<div class="row g-3">
    <?php foreach ($rows as $a): ?>
        <div class="col-6 col-md-4 col-xl-3"><div class="card-x achv <?= $a['awarded_at'] ? '' : 'locked' ?>">
            <div class="icon"><i class="bi <?= e($a['icon']) ?>"></i></div>
            <h3><?= e($a['name']) ?></h3><p><?= e($a['description']) ?></p>
            <?php if ($a['awarded_at']): ?><div class="chip success mt-2"><?= e(fmt_date($a['awarded_at'], false)) ?></div>
            <?php else: $cur = min((int)($counts[$a['metric']] ?? 0), (int)$a['threshold']); ?>
                <div class="progress mt-3" style="height:.4rem"><div class="progress-bar" style="width:<?= $a['threshold'] ? round($cur / $a['threshold'] * 100) : 0 ?>%"></div></div><div class="small text-muted mt-1"><?= $cur ?> / <?= (int)$a['threshold'] ?></div><?php endif; ?>
        </div></div>
    <?php endforeach; ?>
</div>
