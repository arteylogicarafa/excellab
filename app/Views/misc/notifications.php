<?php $pageTitle = 'Notificaciones'; ?>
<div class="page-head"><div><h1>Notificaciones</h1><p>Avisos sobre tus logros, entregas y evaluaciones.</p></div>
    <form method="post" action="<?= url('notificaciones/leer') ?>"><?= csrf_field() ?><button class="btn btn-soft"><i class="bi bi-check2-all"></i> Marcar todas como leídas</button></form></div>
<div class="card-x">
    <?php if (!$rows): ?><div class="empty"><i class="bi bi-bell-slash"></i>No tienes notificaciones.</div><?php endif; ?>
    <?php foreach ($rows as $n): ?>
        <a class="item-row <?= $n['is_read'] ? '' : 'bg-light' ?>" href="<?= url('notificaciones/ir/' . $n['id']) ?>">
            <div class="feed-icon"><i class="bi <?= $n['is_read'] ? 'bi-bell' : 'bi-bell-fill' ?>"></i></div>
            <div class="flex-grow-1"><div class="fw-semibold"><?= e($n['title']) ?></div><div class="small text-muted"><?= e($n['message']) ?></div></div>
            <div class="small text-muted"><?= e(time_ago($n['created_at'])) ?></div>
        </a>
    <?php endforeach; ?>
</div>
