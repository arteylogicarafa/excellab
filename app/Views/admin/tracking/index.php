<?php $pageTitle = 'Seguimiento'; ?>
<div class="page-head"><div><h1><i class="bi bi-graph-up-arrow text-success"></i> Progreso individual</h1><p>Consulta el avance, las actividades y las calificaciones de cada estudiante.</p></div>
    <form class="d-flex gap-2" method="get"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar estudiante…"><button class="btn btn-primary"><i class="bi bi-search"></i></button></form></div>
<div class="card-x"><div class="table-responsive"><table class="table table-x table-hover align-middle">
    <thead><tr><th>Estudiante</th><th style="min-width:180px">Progreso general</th><th>Puntos</th><th>Última actividad</th><th>Estado</th><th></th></tr></thead><tbody>
    <?php if (!$students): ?><tr><td colspan="6"><div class="empty py-4">No hay estudiantes.</div></td></tr><?php endif; ?>
    <?php foreach ($students as $s): ?><tr>
        <td><div class="d-flex align-items-center gap-2"><div class="avatar"><?= e(mb_strtoupper(mb_substr($s['name'], 0, 1))) ?></div><div><div class="fw-semibold"><?= e($s['name']) ?></div><div class="small text-muted"><?= e($s['email']) ?></div></div></div></td>
        <td><div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1"><div class="progress-bar" style="width:<?= (float)$s['progress'] ?>%"></div></div><span class="small"><?= round($s['progress']) ?>%</span></div></td>
        <td><strong><?= (int)$s['points'] ?></strong></td><td class="small text-muted"><?= $s['last_activity'] ? e(time_ago($s['last_activity'])) : 'Sin actividad' ?></td><td><?= status_badge($s['status']) ?></td>
        <td class="text-end"><a class="btn btn-sm btn-soft" href="<?= url('admin/seguimiento/' . $s['id']) ?>">Ver detalle</a></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
