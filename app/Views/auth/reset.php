<?php $pageTitle = 'Nueva contraseña'; ?>
<h1 class="h3 mb-1">Crea tu nueva contraseña</h1>
<p class="text-muted mb-4">Mínimo 8 caracteres, combinando letras y números.</p>
<form method="post" action="<?= url('restablecer/' . $token) ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="password">Nueva contraseña</label>
        <input class="form-control" type="password" id="password" name="password" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" autocomplete="new-password">
        <div class="invalid-feedback">Mínimo 8 caracteres con letras y números.</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password2">Confirmar contraseña</label>
        <input class="form-control" type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password">
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit">Guardar contraseña</button>
</form>
