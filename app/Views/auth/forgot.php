<?php $pageTitle = 'Recuperar contraseña'; ?>
<h1 class="h3 mb-1">Recuperar contraseña</h1>
<p class="text-muted mb-4">Escribe tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>
<form method="post" action="<?= url('recuperar') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <input class="form-control" type="email" id="email" name="email" required autofocus maxlength="150">
        <div class="invalid-feedback">Escribe un correo válido.</div>
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit">Enviar enlace</button>
</form>
<?php if (!empty($_SESSION['dev_reset_link'])): ?>
    <div class="alert alert-warning mt-3 small">
        <strong>Modo desarrollo:</strong> no hay servidor de correo configurado, así que el enlace se muestra aquí (y se guarda en <code>storage/logs/mail.log</code>):
        <div class="mt-2 text-break"><a href="<?= e($_SESSION['dev_reset_link']) ?>"><?= e($_SESSION['dev_reset_link']) ?></a></div>
    </div>
    <?php unset($_SESSION['dev_reset_link']); ?>
<?php endif; ?>
<p class="text-center mt-4 mb-0"><a href="<?= url('docentes') ?>"><i class="bi bi-arrow-left"></i> Volver al acceso docentes</a></p>
