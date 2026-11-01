<div class="text-center py-5">
    <div class="display-1 fw-bold text-success"><?= (int)$code ?></div>
    <h1 class="h3 mb-2"><?= e($title) ?></h1>
    <p class="text-muted"><?= e($message) ?></p>
    <?php if (!empty($detail)): ?><pre class="text-start small bg-light border rounded p-3 mt-3"><?= e($detail) ?></pre><?php endif; ?>
    <a class="btn btn-primary mt-2" href="<?= url(Auth::check() ? Auth::home() : 'login') ?>"><i class="bi bi-house"></i> Volver al inicio</a>
</div>
