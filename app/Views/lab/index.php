<?php
$pageTitle = 'Laboratorio de ejercicios';
$types = ['multiple_choice', 'true_false', 'complete_formula', 'identify_function', 'solve_operation', 'analyze_table', 'practical'];
?>
<div class="page-head"><div><h1>Laboratorio de ejercicios</h1><p>Practica con ejercicios de distintos tipos y recibe retroalimentación al instante.</p></div><div class="text-muted small"><?= count($rows) ?> ejercicios</div></div>
<form class="card-x p-3 mb-4" method="get" action="<?= url('laboratorio') ?>">
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Buscar ejercicio…"></div>
        <div class="col-6 col-md-2"><select class="form-select" name="module" aria-label="Módulo"><option value="">Todos los módulos</option>
            <?php foreach ($modules as $i => $m): ?><option value="<?= $m['id'] ?>" <?= $f['module'] == $m['id'] ? 'selected' : '' ?>>M<?= $i + 1 ?> · <?= e($m['title']) ?></option><?php endforeach; ?></select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="difficulty" aria-label="Dificultad"><option value="">Toda dificultad</option>
            <?php foreach (['basico', 'intermedio', 'avanzado'] as $d): ?><option value="<?= $d ?>" <?= $f['difficulty'] === $d ? 'selected' : '' ?>><?= diff_label($d) ?></option><?php endforeach; ?></select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="type" aria-label="Tipo"><option value="">Todo tipo</option>
            <?php foreach ($types as $t): ?><option value="<?= $t ?>" <?= $f['type'] === $t ? 'selected' : '' ?>><?= e(type_label($t)) ?></option><?php endforeach; ?></select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="state" aria-label="Estado"><option value="">Todos</option><option value="pending" <?= $f['state'] === 'pending' ? 'selected' : '' ?>>Pendientes</option><option value="solved" <?= $f['state'] === 'solved' ? 'selected' : '' ?>>Resueltos</option></select></div>
        <div class="col-md-4"><select class="form-select" name="topic" aria-label="Tema"><option value="">Todos los temas</option>
            <?php foreach ($topics as $t): ?><option <?= $f['topic'] === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-8 d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button><a class="btn btn-light" href="<?= url('laboratorio') ?>">Limpiar</a></div>
    </div>
</form>
<?php if (!$rows): ?><div class="card-x empty"><i class="bi bi-search"></i>No hay ejercicios con esos filtros.</div><?php endif; ?>
<div class="card-x">
    <?php foreach ($rows as $r): $s = $status[$r['id']] ?? null; ?>
        <a class="item-row" href="<?= url('laboratorio/' . $r['id']) ?>">
            <div class="item-check <?= !empty($s['solved']) ? 'done' : '' ?>"><?= !empty($s['solved']) ? '<i class="bi bi-check-lg"></i>' : ($s ? '<i class="bi bi-arrow-repeat text-muted"></i>' : '') ?></div>
            <div class="flex-grow-1"><div class="fw-semibold"><?= e($r['title']) ?></div><div class="small text-muted"><?= e($r['module_title']) ?> · <?= e($r['topic']) ?></div></div>
            <span class="chip d-none d-md-inline-flex"><?= e(type_label($r['type'])) ?></span>
            <span class="chip <?= diff_class($r['difficulty']) ?>"><?= e(diff_label($r['difficulty'])) ?></span>
            <span class="chip green"><?= (int)$r['points'] ?> pts</span>
        </a>
    <?php endforeach; ?>
</div>
