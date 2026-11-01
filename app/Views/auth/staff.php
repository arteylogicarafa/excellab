<?php $pageTitle = 'Acceso docentes'; ?>
<h1 class="h3 mb-1">Acceso docentes</h1>
<p class="text-muted mb-4">Para docentes y administradores.</p>
<form method="post" action="<?= url('docentes') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control" type="email" id="email" name="email" value="<?= e(old('email')) ?>" required autofocus autocomplete="username" maxlength="150">
        <div class="invalid-feedback">Escribe un correo válido.</div>
    </div>
    <div class="mb-3">
        <label class="form-label d-flex justify-content-between" for="password">Contraseña <a class="small" href="<?= url('recuperar') ?>">¿Olvidaste tu contraseña?</a></label>
        <div class="input-group">
            <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
            <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
            <div class="invalid-feedback">Escribe tu contraseña.</div>
        </div>
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit">Iniciar sesión</button>
</form>
<?php if (config('show_demo_credentials')): ?>
<div class="card-x mt-4 p-3 small">
    <div class="fw-semibold mb-2"><i class="bi bi-info-circle text-success"></i> Cuentas de demostración</div>
    <table class="table table-sm table-borderless mb-1">
        <tr><td class="text-muted">Administrador</td><td><code>admin@excellab.test</code></td><td><code>Admin#2026</code></td></tr>
        <tr><td class="text-muted">Docente</td><td><code>docente@excellab.test</code></td><td><code>Docente#2026</code></td></tr>
    </table>
    <div class="text-muted">En el primer acceso se pedirá cambiar la contraseña.</div>
</div>
<?php endif; ?>
<p class="text-center mt-4 mb-0"><a href="<?= url('login') ?>"><i class="bi bi-arrow-left"></i> Entrada de estudiantes</a></p>
