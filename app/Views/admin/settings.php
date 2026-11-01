<?php $pageTitle = 'Ajustes'; ?>
<div class="page-head"><div><h1><i class="bi bi-gear text-success"></i> Ajustes</h1><p>Opciones generales de la plataforma.</p></div></div>
<div class="card-x" style="max-width:640px"><form class="card-x-body" method="post" action="<?= url('admin/ajustes') ?>">
    <?= csrf_field() ?>
    <div class="form-check form-switch mb-1"><input class="form-check-input" type="checkbox" role="switch" id="rk" name="ranking_enabled" value="1" <?= $ranking ? 'checked' : '' ?>><label class="form-check-label fw-semibold" for="rk">Mostrar ranking de estudiantes</label></div>
    <div class="form-text mb-4">El ranking es complementario: solo muestra puntos y nunca forma parte de la evaluación.</div>
    <button class="btn btn-primary"><i class="bi bi-check2"></i> Guardar</button>
</form></div>
