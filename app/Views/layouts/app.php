<?php
$user = Auth::user();
$isStaff = Auth::isStaff();
$path = Router::path();
$unread = Notifier::unread($user['id']);
$latest = Notifier::latest($user['id'], 5);
$rankingOn = DB::setting('ranking_enabled', '1') === '1';

if ($isStaff) {
    $nav = [
        ['Principal', [['admin', 'bi-speedometer2', 'Panel', '/admin']]],
        ['Gestión académica', array_values(array_filter([
            ['admin/estudiantes', 'bi-people', 'Estudiantes', '/admin/estudiantes'],
            $user['role'] === 'admin' ? ['admin/usuarios', 'bi-person-gear', 'Docentes y admins', '/admin/usuarios'] : null,
            ['admin/modulos', 'bi-collection', 'Módulos', '/admin/modulos'],
            ['admin/lecciones', 'bi-journal-text', 'Lecciones', '/admin/lecciones'],
            ['admin/ejercicios', 'bi-pencil-square', 'Ejercicios', '/admin/ejercicios'],
            ['admin/retos', 'bi-trophy', 'Retos', '/admin/retos'],
            ['admin/casos', 'bi-briefcase', 'Casos prácticos', '/admin/casos'],
            ['admin/evaluaciones', 'bi-clipboard-check', 'Evaluaciones', '/admin/evaluaciones'],
            ['admin/logros', 'bi-award', 'Insignias', '/admin/logros'],
        ]))],
        ['Seguimiento', [
            ['admin/seguimiento', 'bi-graph-up-arrow', 'Progreso individual', '/admin/seguimiento'],
            ['admin/entregas', 'bi-inbox', 'Entregas de archivos', '/admin/entregas'],
            ['admin/reportes', 'bi-file-earmark-bar-graph', 'Reportes', '/admin/reportes'],
        ]],
        ['Vista de estudiante', [
            ['aprender', 'bi-eye', 'Previsualizar contenidos', '/aprender'],
            ...($user['role'] === 'admin' ? [['admin/ajustes', 'bi-gear', 'Ajustes', '/admin/ajustes']] : []),
        ]],
    ];
} else {
    $nav = [
        ['Principal', [['inicio', 'bi-house-door', 'Inicio', '/inicio']]],
        ['Aprendizaje', [
            ['aprender', 'bi-book', 'Aprender Excel', '/aprender'],
            ['laboratorio', 'bi-journal-check', 'Laboratorio', '/laboratorio'],
            ['retos', 'bi-trophy', 'Excel Challenge', '/retos'],
            ['casos', 'bi-briefcase', 'Casos prácticos', '/casos'],
            ['evaluaciones', 'bi-clipboard-check', 'Evaluaciones', '/evaluaciones'],
        ]],
        ['Mi progreso', array_values(array_filter([
            ['logros', 'bi-award', 'Mis logros', '/logros'],
            $rankingOn ? ['ranking', 'bi-bar-chart', 'Ranking', '/ranking'] : null,
        ]))],
    ];
}
$isActive = function (string $key) use ($path): bool {
    $p = '/' . $key;
    if ($key === 'admin') return $path === '/admin';
    return $path === $p || str_starts_with($path, $p . '/');
};
$initials = mb_strtoupper(mb_substr($user['name'], 0, 1));
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e(($pageTitle ?? '') ? $pageTitle . ' · ExcelLab' : 'ExcelLab') ?></title>
<link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
<link rel="stylesheet" href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('vendor/icons/bootstrap-icons.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<?php partial('loader'); ?>
<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= url(Auth::home()) ?>"><span class="brand-mark"><i class="bi bi-grid-3x3-gap-fill"></i></span> ExcelLab</a>
    <nav>
        <?php foreach ($nav as [$label, $items]): ?>
            <div class="nav-label"><?= e($label) ?></div>
            <?php foreach ($items as [$key, $icon, $text, $href]): ?>
                <a class="nav-link-x <?= $isActive($key) ? 'active' : '' ?>" href="<?= url($href) ?>"><i class="bi <?= e($icon) ?>"></i><?= e($text) ?></a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">Paquetes Informáticos<br>Plataforma educativa de Excel</div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="main">
    <header class="topbar">
        <button class="btn btn-light d-lg-none" data-toggle-sidebar aria-label="Abrir menú"><i class="bi bi-list fs-5"></i></button>
        <form class="search d-none d-sm-block" action="<?= url('buscar') ?>" method="get" role="search">
            <i class="bi bi-search"></i>
            <input class="form-control" type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Buscar lecciones, ejercicios, retos…" minlength="2" aria-label="Buscar">
        </form>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a class="btn btn-light d-sm-none" href="<?= url('buscar') ?>" aria-label="Buscar"><i class="bi bi-search"></i></a>
            <div class="dropdown">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-label="Notificaciones" data-bs-auto-close="outside">
                    <i class="bi bi-bell"></i>
                    <?php if ($unread): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unread > 9 ? '9+' : $unread ?></span><?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width: 340px; max-width: 92vw;">
                    <div class="px-3 py-2 border-bottom fw-semibold d-flex justify-content-between">Notificaciones <a class="small fw-normal" href="<?= url('notificaciones') ?>">Ver todas</a></div>
                    <?php if (!$latest): ?><div class="empty py-4">Sin notificaciones</div><?php endif; ?>
                    <?php foreach ($latest as $n): ?>
                        <a class="dropdown-item py-2 <?= $n['is_read'] ? '' : 'bg-light' ?>" href="<?= url('notificaciones/ir/' . $n['id']) ?>" style="white-space: normal;">
                            <div class="fw-semibold small"><?= e($n['title']) ?></div>
                            <div class="small text-muted"><?= e($n['message']) ?></div>
                            <div class="small text-muted"><?= e(time_ago($n['created_at'])) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <span class="avatar"><?= e($initials) ?></span>
                    <span class="d-none d-md-inline text-start lh-sm"><span class="d-block small fw-semibold"><?= e($user['name']) ?></span><span class="d-block text-muted" style="font-size:.72rem"><?= e(['admin' => 'Administrador', 'docente' => 'Docente', 'estudiante' => 'Estudiante'][$user['role']]) ?></span></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('cuenta') ?>"><i class="bi bi-person me-2"></i>Mi cuenta</a></li>
                    <li><a class="dropdown-item" href="<?= url('cuenta/password') ?>"><i class="bi bi-key me-2"></i>Cambiar contraseña</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="post" action="<?= url('salir') ?>"><?= csrf_field() ?><button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button></form></li>
                </ul>
            </div>
        </div>
    </header>
    <main class="content">
        <?php foreach (flashes() as $f): ?>
            <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($f['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endforeach; ?>
        <?= $content ?>
    </main>
</div>
<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('vendor/chartjs/chart.umd.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<?= $scripts ?? '' ?>
</body>
</html>
