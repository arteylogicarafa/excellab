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
<div class="auth-wrap">
    <section class="auth-side">
        <a class="d-flex align-items-center gap-2 text-white fs-4 fw-bold" href="<?= url('/') ?>"><span class="brand-mark bg-white text-success" style="background:#fff"><i class="bi bi-grid-3x3-gap-fill" style="color:#217346"></i></span> ExcelLab</a>
        <div>
            <h2 class="mb-4">Aprende Excel practicando,<br>a tu propio ritmo.</h2>
            <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill"></i> Lecciones claras con ejemplos reales</li>
                <li><i class="bi bi-check-circle-fill"></i> Laboratorio de ejercicios con corrección automática</li>
                <li><i class="bi bi-check-circle-fill"></i> Retos, casos prácticos y evaluaciones</li>
                <li><i class="bi bi-check-circle-fill"></i> Seguimiento de tu progreso y certificados</li>
            </ul>
        </div>
        <small class="opacity-75">Unidad curricular: Paquetes Informáticos</small>
    </section>
    <section class="auth-main">
        <div class="auth-card">
            <div class="d-lg-none text-center mb-3 <?= logo_url() ? 'd-none' : '' ?>"><span class="brand-mark d-inline-grid"><i class="bi bi-grid-3x3-gap-fill"></i></span> <span class="fw-bold fs-4 align-middle">ExcelLab</span></div>
            <?php foreach (flashes() as $f): ?>
                <div class="alert alert-<?= e($f['type']) ?>" role="alert"><?= e($f['msg']) ?></div>
            <?php endforeach; ?>
            <?= $content ?>
        </div>
    </section>
</div>
<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
