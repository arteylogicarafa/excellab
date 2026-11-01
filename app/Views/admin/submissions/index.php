<?php $pageTitle = 'Entregas'; ?>
<div class="page-head"><div><h1><i class="bi bi-inbox text-success"></i> Entregas de casos prácticos</h1><p>Revisa y califica los archivos entregados por los estudiantes.</p></div></div>
<form class="card-x p-3 mb-3" method="get" action="<?= url('admin/entregas') ?>"><div class="row g-2">
    <div class="col-6 col-md-2"><select class="form-select" name="mode" aria-label="Modalidad"><option value="file" <?= $f['mode'] === 'file' ? 'selected' : '' ?>>Archivos Excel</option><option value="platform" <?= $f['mode'] === 'platform' ? 'selected' : '' ?>>En plataforma</option><option value="" <?= $f['mode'] === '' ? 'selected' : '' ?>>Todas</option></select></div>
    <div class="col-6 col-md-2"><select class="form-select" name="status" aria-label="Estado"><option value="">Todos los estados</option><option value="submitted" <?= $f['status'] === 'submitted' ? 'selected' : '' ?>>Pendientes</option><option value="reviewed" <?= $f['status'] === 'reviewed' ? 'selected' : '' ?>>Revisadas</option></select></div>
    <div class="col-md-3"><select class="form-select" name="case" aria-label="Caso"><option value="">Todos los casos</option><?php foreach ($cases as $c): ?><option value="<?= $c['id'] ?>" <?= $f['case'] == $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><select class="form-select" name="student" aria-label="Estudiante"><option value="">Todos los estudiantes</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>" <?= $f['student'] == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-auto"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button></div></div></form>
<div class="card-x"><div class="table-responsive"><table class="table table-x table-hover align-middle">
    <thead><tr><th>Fecha</th><th>Estudiante</th><th>Caso</th><th>Archivo</th><th>Nota</th><th>Estado</th><th></th></tr></thead><tbody>
    <?php if (!$rows): ?><tr><td colspan="7"><div class="empty py-4"><i class="bi bi-inbox"></i>No hay entregas con estos filtros.</div></td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?><tr>
        <td class="text-nowrap"><?= e(fmt_date($r['created_at'])) ?></td><td><?= e($r['student']) ?></td><td><?= e($r['case_title']) ?></td>
        <td><?= $r['original_name'] ? '<i class="bi bi-file-earmark-excel text-success"></i> ' . e($r['original_name']) : '<span class="text-muted">Auto-calificado</span>' ?></td>
        <td><?= $r['score'] !== null ? round($r['score'], 1) . ' / ' . (int)$r['max_points'] : '—' ?></td><td><?= status_badge($r['status']) ?></td>
        <td class="text-end"><a class="btn btn-sm btn-soft" href="<?= url('admin/entregas/' . $r['id']) ?>"><?= $r['status'] === 'submitted' ? 'Revisar' : 'Ver' ?></a></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
