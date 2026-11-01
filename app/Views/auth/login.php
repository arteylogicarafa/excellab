<?php $pageTitle = 'Entrar'; ?>
<?php if ($logo = logo_url()): ?><img class="d-block mx-auto mb-2" src="<?= e($logo) ?>" alt="ExcelLab by Rafa Guerrero" style="max-width:300px;width:100%"><?php endif; ?>
<h1 class="h3 mb-1">Bienvenido a ExcelLab</h1>
<p class="text-muted mb-4">Escribe tu correo para entrar. Lo usamos solo para guardar tu avance.</p>
<form method="post" action="<?= url('entrar') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control form-control-lg" type="email" id="email" name="email" value="<?= e(old('email')) ?>" required autofocus autocomplete="email" maxlength="150" placeholder="tucorreo@ejemplo.com">
        <div class="invalid-feedback">Escribe un correo válido.</div>
    </div>
    <button class="btn btn-primary btn-lg w-100" type="submit">Entrar <i class="bi bi-arrow-right"></i></button>
</form>
<p class="text-center small text-muted mt-4 mb-0">Usa siempre el mismo correo para conservar tu progreso.<br><a href="<?= url('docentes') ?>">Acceso docentes</a></p>
