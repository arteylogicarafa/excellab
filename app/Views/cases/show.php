<?php $pageTitle = $c['title']; $isStudent = Auth::role() === 'estudiante'; ?>
<nav class="small mb-2"><a href="<?= url('casos') ?>">Casos prácticos</a> <span class="text-muted">›</span> <?= e($c['title']) ?></nav>
<div class="page-head"><div><h1><?= e($c['title']) ?></h1>
    <div class="d-flex flex-wrap gap-2 mt-2"><span class="chip green"><?= e($c['category']) ?></span><span class="chip <?= $c['mode'] === 'file' ? 'warning' : 'green' ?>"><?= $c['mode'] === 'file' ? 'Entrega en Excel' : 'Se resuelve en la plataforma' ?></span><span class="chip"><i class="bi bi-star-fill"></i> <?= (int)$c['points'] ?> pts</span></div></div>
    <?php if ($sheet): ?><a class="btn btn-soft" href="<?= url('casos/' . (int)$c['id'] . '/plantilla') ?>"><i class="bi bi-file-earmark-excel"></i> Descargar datos en Excel</a><?php endif; ?></div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-x mb-4"><div class="card-x-body">
            <h2 class="h6 text-success text-uppercase" style="letter-spacing:.06em">Contexto</h2><p><?= nl2br(e($c['context'])) ?></p>
            <h2 class="h6 text-success text-uppercase" style="letter-spacing:.06em">Problema</h2><p><?= nl2br(e($c['problem'])) ?></p>
            <?php if ($sheet): partial('sheet', ['data' => $sheet, 'caption' => 'Datos de trabajo']); endif; ?>
            <h2 class="h6 text-success text-uppercase mt-3" style="letter-spacing:.06em">Instrucciones</h2><p><?= nl2br(e($c['instructions'])) ?></p>
            <h2 class="h6 text-success text-uppercase" style="letter-spacing:.06em">Criterios de evaluación</h2><p class="mb-0"><?= nl2br(e($c['criteria'])) ?></p>
        </div></div>

        <?php if ($isStudent): ?>
        <div class="card-x"><div class="card-x-head"><h2><?= $c['mode'] === 'file' ? 'Entregar archivo' : 'Tus respuestas' ?></h2></div>
            <?php if ($c['mode'] === 'platform'): ?>
                <form class="card-x-body needs-validation" method="post" action="<?= url('casos/' . (int)$c['id'] . '/entregar') ?>" novalidate>
                    <?= csrf_field() ?>
                    <?php foreach ($questions as $i => $q): ?>
                        <div class="mb-3"><label class="form-label fw-semibold" for="q<?= $i ?>"><?= $i + 1 ?>. <?= e($q['label']) ?></label>
                            <input class="form-control" id="q<?= $i ?>" name="answers[<?= $i ?>]" required maxlength="200" autocomplete="off" placeholder="<?= ($q['kind'] ?? 'text') === 'number' ? 'Resultado numérico' : 'Respuesta' ?>"></div>
                    <?php endforeach; ?>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i> Enviar respuestas</button>
                </form>
            <?php else: ?>
                <form class="card-x-body needs-validation" method="post" action="<?= url('casos/' . (int)$c['id'] . '/entregar') ?>" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label fw-semibold" for="file">Archivo de Excel (.xlsx, .xls o .csv)</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Tamaño máximo: <?= (int)$maxMb ?> MB. Puedes volver a entregar; el docente revisa la entrega más reciente.</div></div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-upload"></i> Entregar</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="card-x"><div class="card-x-head"><h2>Mis entregas</h2></div>
            <?php if (!$isStudent): ?><div class="empty py-4">Modo previsualización.</div><?php elseif (!$subs): ?><div class="empty py-4">Aún no has entregado este caso.</div><?php endif; ?>
            <?php foreach ($subs as $k => $s): $res = $c['mode'] === 'platform' ? array_values(array_filter((array)json_decode((string)$s['answers_json'], true), 'is_array')) : []; ?>
                <div class="item-row d-block">
                    <div class="d-flex justify-content-between"><strong class="small"><?= e(fmt_date($s['created_at'])) ?></strong><?= status_badge($s['status']) ?></div>
                    <?php if ($s['original_name']): ?><div class="small text-muted"><i class="bi bi-paperclip"></i> <?= e($s['original_name']) ?></div><?php endif; ?>
                    <?php if ($s['score'] !== null): ?><div class="mt-1"><span class="chip green"><?= round($s['score'], 1) ?> / <?= (int)$c['points'] ?> pts</span></div><?php endif; ?>
                    <?php if ($res && $k === 0): foreach ($res as $i => $r): ?>
                        <div class="small mt-1"><i class="bi <?= !empty($r['ok']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i> Pregunta <?= (int)$i + 1 ?></div>
                    <?php endforeach; endif; ?>
                    <?php if ($s['feedback']): ?><div class="small mt-1 text-muted"><?= e($s['feedback']) ?></div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
