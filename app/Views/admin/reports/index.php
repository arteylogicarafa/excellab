<?php $pageTitle = 'Reportes'; ?>
<div class="page-head"><div><h1><i class="bi bi-file-earmark-bar-graph text-success"></i> Reportes</h1><p>Filtra la actividad por estudiante, módulo, tipo de actividad y fecha. Exporta el resultado a CSV (se abre en Excel).</p></div>
    <a class="btn btn-primary" href="<?= url('admin/reportes?' . $query . ($query ? '&' : '') . 'export=1') ?>"><i class="bi bi-download"></i> Exportar CSV</a></div>
<form class="card-x p-3 mb-3" method="get" action="<?= url('admin/reportes') ?>"><div class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label small mb-1">Estudiante</label><select class="form-select" name="student"><option value="">Todos</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>" <?= $f['student'] == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><label class="form-label small mb-1">Módulo</label><select class="form-select" name="module"><option value="">Todos</option><?php foreach ($modules as $m): ?><option value="<?= $m['id'] ?>" <?= $f['module'] == $m['id'] ? 'selected' : '' ?>><?= e($m['title']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><label class="form-label small mb-1">Actividad</label><select class="form-select" name="type"><option value="">Todas</option><?php foreach ($types as $k => $l): ?><option value="<?= $k ?>" <?= $f['type'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
    <div class="col-6 col-md-2"><label class="form-label small mb-1">Desde</label><input class="form-control" type="date" name="from" value="<?= e($f['from']) ?>"></div>
    <div class="col-6 col-md-2"><label class="form-label small mb-1">Hasta</label><input class="form-control" type="date" name="to" value="<?= e($f['to']) ?>"></div>
    <div class="col-12 d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Aplicar filtros</button><a class="btn btn-light" href="<?= url('admin/reportes') ?>">Limpiar</a></div></div></form>
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card-x stat-card"><div class="stat-icon"><i class="bi bi-list-check"></i></div><div><div class="stat-value"><?= (int)$summary['total'] ?></div><div class="stat-label">Registros</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card-x stat-card"><div class="stat-icon blue"><i class="bi bi-people"></i></div><div><div class="stat-value"><?= (int)$summary['students'] ?></div><div class="stat-label">Estudiantes</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card-x stat-card"><div class="stat-icon amber"><i class="bi bi-check2-circle"></i></div><div><div class="stat-value"><?= $summary['all_ex'] ? round($summary['ok_ex'] / $summary['all_ex'] * 100) . '%' : '—' ?></div><div class="stat-label">Aciertos en ejercicios</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card-x stat-card"><div class="stat-icon violet"><i class="bi bi-clipboard-check"></i></div><div><div class="stat-value"><?= $summary['avg_assess'] !== null ? round($summary['avg_assess']) . '%' : '—' ?></div><div class="stat-label">Promedio en evaluaciones</div></div></div></div>
</div>
<div class="card-x"><div class="table-responsive"><table class="table table-x table-hover align-middle">
    <thead><tr><th>Fecha</th><th>Estudiante</th><th>Actividad</th><th>Detalle</th><th>Módulo</th><th>Resultado</th><th>Puntaje</th></tr></thead><tbody>
    <?php if (!$rows): ?><tr><td colspan="7"><div class="empty py-4"><i class="bi bi-search"></i>No hay registros con estos filtros.</div></td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?><tr>
        <td class="small text-nowrap"><?= e(fmt_date($r['occurred_at'])) ?></td><td><a href="<?= url('admin/seguimiento/' . $r['student_id']) ?>"><?= e($r['student']) ?></a></td>
        <td><span class="chip"><?= e($types[$r['kind']]) ?></span></td><td><?= e($r['activity']) ?></td><td class="small text-muted"><?= e($r['module_title'] ?? '—') ?></td>
        <td><?php $good = in_array($r['result'], ['Correcto', 'Superado', 'Aprobado', 'Completada', 'Revisado'], true); ?><span class="chip <?= $good ? 'success' : (in_array($r['result'], ['Pendiente'], true) ? 'warning' : 'danger') ?>"><?= e($r['result']) ?></span></td>
        <td><?= $r['score'] !== null ? e(round((float)$r['score'], 1)) . ($r['kind'] === 'assessment' ? '%' : '') : '—' ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php if (count($rows) >= 300): ?><div class="p-3 small text-muted border-top">Se muestran los 300 registros más recientes. Usa «Exportar CSV» para obtener todos.</div><?php endif; ?></div>
