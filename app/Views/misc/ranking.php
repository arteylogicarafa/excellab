<?php $pageTitle = 'Ranking'; ?>
<div class="page-head"><div><h1>Ranking</h1><p>Una referencia amistosa de puntos. No forma parte de tu evaluación.</p></div></div>
<?php if (!$enabled): ?>
    <div class="card-x empty"><i class="bi bi-bar-chart"></i>El ranking está desactivado por tu docente.</div>
<?php else: ?>
    <div class="row g-4"><div class="col-lg-8">
        <div class="card-x">
            <?php if (!$list): ?><div class="empty">Aún no hay estudiantes.</div><?php endif; ?>
            <?php foreach ($list as $r): ?>
                <div class="rank-row <?= $r['id'] === Auth::id() ? 'me' : '' ?>"><div class="rank-pos"><?= $r['pos'] ?></div>
                    <div class="avatar"><?= e(mb_strtoupper(mb_substr($r['name'], 0, 1))) ?></div>
                    <div class="flex-grow-1 fw-semibold"><?= e($r['display']) ?><?= $r['id'] === Auth::id() ? ' <span class="chip green">Tú</span>' : '' ?></div>
                    <div class="fw-bold"><?= $r['points'] ?> <small class="text-muted fw-normal">pts</small></div></div>
            <?php endforeach; ?>
        </div>
        <?php if ($me && $me['pos'] > 10): ?><div class="card-x mt-3"><div class="rank-row me border-0"><div class="rank-pos"><?= $me['pos'] ?></div><div class="flex-grow-1 fw-semibold">Tu posición</div><div class="fw-bold"><?= $me['points'] ?> pts</div></div></div><?php endif; ?>
    </div></div>
<?php endif; ?>
