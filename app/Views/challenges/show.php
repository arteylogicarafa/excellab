<?php $pageTitle = $c['title']; $placeholder = ['number' => 'Escribe el resultado numérico', 'formula' => '=FÓRMULA(…)', 'text' => 'Escribe tu respuesta'][$c['answer_kind']]; ?>
<nav class="small mb-2"><a href="<?= url('retos') ?>">Excel Challenge</a> <span class="text-muted">›</span> <?= e($c['title']) ?></nav>
<div class="page-head"><div><h1><?= e($c['title']) ?></h1>
    <div class="d-flex flex-wrap gap-2 mt-2"><span class="chip <?= diff_class($c['difficulty']) ?>"><?= e(diff_label($c['difficulty'])) ?></span><span class="chip"><i class="bi bi-clock"></i> <?= (int)$c['est_minutes'] ?> min estimados</span><span class="chip green"><i class="bi bi-star-fill"></i> <?= (int)$c['points'] ?> pts</span>
    <?php if ($solved): ?><span class="chip success"><i class="bi bi-check-circle-fill"></i> Superado</span><?php endif; ?></div></div></div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-x mb-4"><div class="card-x-body">
            <h2 class="h6 text-success text-uppercase" style="letter-spacing:.06em">Situación</h2><p><?= nl2br(e($c['situation'])) ?></p>
            <h2 class="h6 text-success text-uppercase" style="letter-spacing:.06em">Objetivo</h2><p><?= e($c['objective']) ?></p>
            <?php if ($sheet): partial('sheet', ['data' => $sheet, 'caption' => 'Datos de entrada']); endif; ?>
            <h2 class="h6 text-success text-uppercase mt-3" style="letter-spacing:.06em">Instrucciones</h2><p class="mb-0"><?= nl2br(e($c['instructions'])) ?></p>
            <?php if ($c['hint']): ?><details class="mt-3"><summary class="text-muted small" style="cursor:pointer"><i class="bi bi-lightbulb"></i> Ver pista</summary><p class="small mt-2 mb-0"><?= e($c['hint']) ?></p></details><?php endif; ?>
        </div></div>
        <div class="card-x"><div class="card-x-body" id="challengeBox" data-url="<?= url('retos/' . (int)$c['id'] . '/comprobar') ?>">
            <label class="form-label fw-semibold" for="challengeAnswer">Tu resultado</label>
            <div class="input-group">
                <input class="form-control <?= $c['answer_kind'] === 'formula' ? 'formula-input' : '' ?>" id="challengeAnswer" type="text" maxlength="500" autocomplete="off" placeholder="<?= e($placeholder) ?>">
                <button class="btn btn-primary" id="challengeBtn" type="button"><i class="bi bi-check2-circle"></i> Validar resultado</button>
            </div>
            <div class="feedback" role="status" aria-live="polite"></div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card-x p-3 mb-3 text-center"><div class="text-muted small">Tiempo en este reto</div><div class="timer" id="challengeTimer">00:00</div></div>
        <div class="card-x"><div class="card-x-head"><h2>Mis intentos</h2></div>
            <?php if (!$attempts): ?><div class="empty py-4">Aún sin intentos.</div><?php endif; ?>
            <?php foreach ($attempts as $a): ?>
                <div class="item-row"><i class="bi <?= $a['is_correct'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i>
                    <div class="flex-grow-1 small"><code><?= e($a['answer']) ?></code><div class="text-muted"><?= e(fmt_date($a['created_at'])) ?> · <?= (int)$a['seconds_spent'] ?> s</div></div>
                    <?php if ($a['points']): ?><span class="chip success">+<?= (int)$a['points'] ?></span><?php endif; ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
