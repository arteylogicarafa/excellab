<?php $pageTitle = 'Certificado · ' . $p['module_title']; ?>
<div class="no-print text-center pt-3">
    <a class="btn btn-light" href="<?= url('aprender/modulo/' . (int)$p['module_id']) ?>"><i class="bi bi-arrow-left"></i> Volver</a>
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir / Guardar como PDF</button>
</div>
<div class="certificate">
    <div class="seal"><i class="bi bi-patch-check-fill"></i></div>
    <div class="text-uppercase text-muted fw-semibold" style="letter-spacing:.2em">ExcelLab · Paquetes Informáticos</div>
    <h1>Certificado de finalización</h1>
    <p class="fs-5 mb-1">Se certifica que</p>
    <div class="student"><?= e($p['student_name']) ?></div>
    <p class="fs-5 mt-3">ha completado satisfactoriamente el módulo</p>
    <h2 class="text-success mb-3">“<?= e($p['module_title']) ?>”</h2>
    <p class="text-muted">Completado el <?= e(fmt_date($p['completed_at'], false)) ?> · <?= (int)$p['lessons_total'] ?> lecciones</p>
    <div class="d-flex justify-content-between align-items-end mt-5 pt-3">
        <div class="text-start small text-muted">Código de verificación<br><strong class="text-dark fs-6"><?= e($p['certificate_code']) ?></strong></div>
        <div class="text-center small text-muted"><div style="border-top:1px solid #6b7280;padding-top:.3rem;min-width:220px">Docente de la unidad curricular</div></div>
    </div>
</div>
