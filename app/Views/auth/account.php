<?php $pageTitle = 'Mi cuenta'; ?>
<div class="page-head"><div><h1>Mi cuenta</h1><p>Administra tu perfil y tu contraseña.</p></div></div>
<?php if ($forced): ?>
    <div class="alert alert-warning"><i class="bi bi-shield-exclamation"></i> Estás usando una contraseña inicial. Debes cambiarla para poder continuar.</div>
<?php endif; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-x"><div class="card-x-head"><h2>Perfil</h2></div>
            <form class="card-x-body needs-validation" method="post" action="<?= url('cuenta/perfil') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="name">Nombre completo</label>
                    <input class="form-control" id="name" name="name" value="<?= e($u['name']) ?>" required minlength="3" maxlength="120"></div>
                <div class="mb-3"><label class="form-label">Correo</label><input class="form-control" value="<?= e($u['email']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">Rol</label><input class="form-control" value="<?= e($u['role_label']) ?>" disabled></div>
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
            </form>
        </div>
    </div>
    <?php if (Auth::isStaff()): ?>
    <div class="col-lg-6">
        <div class="card-x"><div class="card-x-head"><h2>Cambiar contraseña</h2></div>
            <form class="card-x-body needs-validation" method="post" action="<?= url('cuenta/password') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="current_password">Contraseña actual</label>
                    <input class="form-control" type="password" id="current_password" name="current_password" required autocomplete="current-password"></div>
                <div class="mb-3"><label class="form-label" for="password">Nueva contraseña</label>
                    <input class="form-control" type="password" id="password" name="password" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" autocomplete="new-password">
                    <div class="form-text">Mínimo 8 caracteres, con letras y números.</div>
                    <div class="invalid-feedback">Mínimo 8 caracteres con letras y números.</div></div>
                <div class="mb-3"><label class="form-label" for="password2">Confirmar nueva contraseña</label>
                    <input class="form-control" type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password"></div>
                <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
