<?php
$pageTitle = $e['title'];
$key = $e['key'];
$qs = fn(array $over = []) => http_build_query(array_filter(array_merge($_GET, $over), fn($v) => $v !== '' && $v !== null));
?>
<div class="page-head"><div><h1><i class="bi <?= e($e['icon']) ?> text-success"></i> <?= e($e['title']) ?></h1><p><?= $total ?> registro<?= $total === 1 ? '' : 's' ?></p></div>
    <a class="btn btn-primary" href="<?= url('admin/' . $key . '/nuevo') ?>"><i class="bi bi-plus-lg"></i> Nuevo <?= e($e['singular']) ?></a></div>
<form class="card-x p-3 mb-3" method="get" action="<?= url('admin/' . $key) ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar…" aria-label="Buscar"></div>
        <?php foreach ($filters as $fl): ?>
            <div class="col-6 col-md-2"><select class="form-select" name="<?= e($fl['name']) ?>" aria-label="<?= e($fl['label']) ?>"><option value=""><?= e($fl['label']) ?>: todos</option>
                <?php foreach ($fl['options'] as $k => $label): ?><option value="<?= e($k) ?>" <?= (string)$fl['value'] === (string)$k ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
        <?php endforeach; ?>
        <div class="col-6 col-md-2"><select class="form-select" name="status" aria-label="Estado"><option value="">Activos e inactivos</option><option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Solo activos</option><option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Solo inactivos</option><option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archivados</option></select></div>
        <div class="col-auto d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button><a class="btn btn-light" href="<?= url('admin/' . $key) ?>">Limpiar</a></div>
    </div>
</form>
<div class="card-x">
    <div class="table-responsive">
        <table class="table table-x table-hover align-middle">
            <thead><tr><?php foreach ($e['columns'] as $c): ?><th><?= e($c[1]) ?></th><?php endforeach; ?><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?><tr><td colspan="<?= count($e['columns']) + 2 ?>"><div class="empty py-4"><i class="bi bi-inbox"></i>No hay registros con estos filtros.</div></td></tr><?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <?php foreach ($e['columns'] as $c): $v = $r[$c[0]] ?? ''; $kind = $c[2] ?? ''; ?>
                        <td><?php
                        if ($kind === 'date') echo e($v ? fmt_date($v) : 'Nunca');
                        elseif ($kind === 'diff') echo '<span class="chip ' . diff_class($v) . '">' . e(diff_label($v)) . '</span>';
                        elseif ($kind === 'type') echo e(type_label($v));
                        elseif ($kind === 'mode') echo $v === 'file' ? '<span class="chip warning">Archivo Excel</span>' : '<span class="chip green">Plataforma</span>';
                        elseif ($kind === 'pwd') echo $v ? '<span class="chip warning">Inicial</span>' : '<span class="chip success">Propia</span>';
                        elseif ($c[0] === 'title' || $c[0] === 'name') echo '<strong>' . e($v) . '</strong>';
                        else echo e($v);
                        ?></td>
                    <?php endforeach; ?>
                    <td><?= status_badge($r['status']) ?></td>
                    <td class="text-end text-nowrap">
                        <?php if (!empty($e['preview'])): ?><a class="btn btn-sm btn-light" title="Ver como estudiante" href="<?= url(str_replace('{id}', $r['id'], $e['preview'])) ?>"><i class="bi bi-eye"></i></a><?php endif; ?>
                        <?php foreach ($e['extra_actions'] ?? [] as [$href, $icon, $title]): ?><a class="btn btn-sm btn-light" title="<?= e($title) ?>" href="<?= url(str_replace('{id}', $r['id'], $href)) ?>"><i class="bi <?= e($icon) ?>"></i></a><?php endforeach; ?>
                        <a class="btn btn-sm btn-soft" title="Editar" href="<?= url('admin/' . $key . '/' . $r['id'] . '/editar') ?>"><i class="bi bi-pencil"></i></a>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown" aria-label="Cambiar estado"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php foreach (['active' => ['bi-check-circle', 'Habilitar'], 'inactive' => ['bi-pause-circle', 'Deshabilitar'], 'archived' => ['bi-archive', 'Archivar']] as $st => [$ic, $lb]): if ($st === $r['status']) continue; ?>
                                    <li><form method="post" action="<?= url('admin/' . $key . '/' . $r['id'] . '/estado') ?>" <?= $st === 'archived' ? 'data-confirm="¿Archivar este registro? Dejará de mostrarse a los estudiantes, pero se conserva su historial."' : '' ?>>
                                        <?= csrf_field() ?><input type="hidden" name="status" value="<?= $st ?>"><button class="dropdown-item"><i class="bi <?= $ic ?> me-2"></i><?= $lb ?></button></form></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav class="p-3 d-flex justify-content-between align-items-center"><span class="small text-muted">Página <?= $page ?> de <?= $pages ?></span>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?<?= e($qs(['page' => $page - 1])) ?>">Anterior</a></li>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="?<?= e($qs(['page' => $page + 1])) ?>">Siguiente</a></li>
            </ul></nav>
    <?php endif; ?>
</div>
