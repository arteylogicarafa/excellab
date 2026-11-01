<?php
/**
 * Ejercicio interactivo.
 * $ex: ejercicio hidratado. $context: 'lab' | 'lesson'. $exam (bool): modo evaluación (sin comprobación inmediata).
 * $num: número de pregunta (modo evaluación).
 */
$exam = $exam ?? false;
$context = $context ?? 'lab';
$hasOptions = $ex['answer_kind'] === 'option';
$placeholder = ['number' => 'Escribe el resultado numérico', 'formula' => '=FÓRMULA(…)', 'text' => 'Escribe tu respuesta'][$ex['answer_kind']] ?? '';
?>
<div class="exercise-card" id="ex-<?= (int)$ex['id'] ?>" <?php if (!$exam): ?>data-check-url="<?= url('ejercicios/' . (int)$ex['id'] . '/comprobar') ?>" data-context="<?= e($context) ?>"<?php endif; ?>>
    <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
        <?php if ($exam): ?><span class="chip green">Pregunta <?= (int)$num ?></span><?php endif; ?>
        <span class="chip"><?= e(type_label($ex['type'])) ?></span>
        <span class="chip <?= diff_class($ex['difficulty']) ?>"><?= e(diff_label($ex['difficulty'])) ?></span>
        <span class="chip green"><i class="bi bi-star-fill"></i> <?= (int)$ex['points'] ?> pts</span>
    </div>
    <?php if (!$exam || true): ?><div class="text-muted small mb-1"><?= e($ex['topic']) ?></div><?php endif; ?>
    <div class="exercise-statement mb-2"><?= nl2br(e($ex['statement'])) ?></div>
    <?php if (!empty($ex['instructions'])): ?><div class="small text-muted mb-2"><i class="bi bi-info-circle"></i> <?= e($ex['instructions']) ?></div><?php endif; ?>
    <?php if (!empty($ex['sheet'])): partial('sheet', ['data' => $ex['sheet'], 'caption' => 'Datos del ejercicio']); endif; ?>

    <div class="mt-3">
        <?php if ($hasOptions): ?>
            <?php foreach ($ex['options'] as $o): ?>
                <label class="opt">
                    <input class="form-check-input" type="radio" name="<?= $exam ? 'answers[' . (int)$ex['id'] . ']' : 'opt-' . (int)$ex['id'] ?>" value="<?= (int)$o['id'] ?>">
                    <span><?= e($o['label']) ?></span>
                </label>
            <?php endforeach; ?>
        <?php else: ?>
            <input class="form-control answer-input <?= $ex['answer_kind'] === 'formula' ? 'formula-input' : '' ?>" type="text" maxlength="500" autocomplete="off" spellcheck="false"
                   <?= $exam ? 'name="answers[' . (int)$ex['id'] . ']"' : '' ?> placeholder="<?= e($placeholder) ?>" aria-label="Respuesta">
        <?php endif; ?>
    </div>

    <?php if (!$exam): ?>
        <button type="button" class="btn btn-primary mt-3 btn-check-ex"><i class="bi bi-check2-circle"></i> Comprobar</button>
        <div class="feedback" role="status" aria-live="polite"></div>
    <?php endif; ?>
</div>
