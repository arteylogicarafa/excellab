<?php
$pageTitle = ($id ? 'Editar ' : 'Nuevo ') . $e['singular'];
$key = $e['key'];
$val = function (string $name, $default = '') use ($row, $old) {
    if (array_key_exists($name, $old)) return $old[$name];
    return $row[$name] ?? $default;
};
?>
<nav class="small mb-2"><a href="<?= url('admin/' . $key) ?>"><?= e($e['title']) ?></a> <span class="text-muted">›</span> <?= $id ? 'Editar' : 'Nuevo' ?></nav>
<div class="page-head"><div><h1><?= e(ucfirst($pageTitle)) ?></h1></div></div>
<form class="card-x needs-validation" method="post" action="<?= url('admin/' . $key . '/guardar') ?>" novalidate>
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="card-x-body"><div class="row g-3">
        <?php foreach ($e['fields'] as $f):
            $n = $f['name']; $t = $f['type']; $col = $f['col'] ?? 12; $req = !empty($f['required']);
            $v = $val($n, $f['default'] ?? '');
            ?>
            <div class="col-md-<?= $col ?>">
                <label class="form-label fw-semibold" for="f-<?= e($n) ?>"><?= e($f['label']) ?><?= $req ? ' <span class="text-danger">*</span>' : '' ?></label>
                <?php if (in_array($t, ['text', 'email'], true)): ?>
                    <input class="form-control" type="<?= $t ?>" id="f-<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>" <?= $req ? 'required' : '' ?> <?= isset($f['max']) ? 'maxlength="' . (int)$f['max'] . '"' : '' ?> <?= isset($f['min']) ? 'minlength="' . (int)$f['min'] . '"' : '' ?> <?= isset($f['pattern']) ? 'pattern="' . e($f['pattern']) . '"' : '' ?>>
                <?php elseif ($t === 'password'): ?>
                    <input class="form-control" type="<?= $id ? 'password' : 'text' ?>" id="f-<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($id ? '' : $suggest) ?>" autocomplete="new-password" minlength="8" <?= $id ? '' : 'required' ?> placeholder="<?= $id ? 'Dejar vacío para no cambiarla' : '' ?>">
                <?php elseif ($t === 'number'): ?>
                    <input class="form-control" type="number" id="f-<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>" <?= isset($f['min']) ? 'min="' . $f['min'] . '"' : '' ?> <?= isset($f['max']) ? 'max="' . $f['max'] . '"' : '' ?> step="<?= e($f['step'] ?? '1') ?>" <?= $req ? 'required' : '' ?>>
                <?php elseif ($t === 'select'): $o = $opts[$n] ?? []; ?>
                    <select class="form-select" id="f-<?= e($n) ?>" name="<?= e($n) ?>" <?= $req ? 'required' : '' ?>>
                        <?php if (!$req || !empty($f['nullable'])): ?><option value="">— Ninguno —</option><?php endif; ?>
                        <?php foreach ($o as $k => $label): ?><option value="<?= e($k) ?>" <?= (string)$v === (string)$k ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
                    </select>
                <?php elseif (in_array($t, ['textarea', 'html', 'json'], true)): ?>
                    <textarea class="form-control <?= $t !== 'textarea' ? 'font-monospace small' : '' ?>" id="f-<?= e($n) ?>" name="<?= e($n) ?>" rows="<?= (int)($f['rows'] ?? 3) ?>" <?= $req ? 'required' : '' ?>><?= e($t === 'json' && $v !== '' && $v !== null && !array_key_exists($n, $old) ? json_encode(json_decode((string)$v, true), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $v) ?></textarea>
                <?php elseif ($t === 'checks'): $sel = array_map('strval', (array)$val($n, [])); $grp = null; ?>
                    <div class="border rounded p-2" style="max-height: 340px; overflow:auto">
                        <?php foreach ($checks[$n] ?? [] as $c): if ($grp !== $c['module_title']): $grp = $c['module_title']; ?><div class="small fw-bold text-success mt-2"><?= e($grp) ?></div><?php endif; ?>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="q<?= (int)$c['id'] ?>" name="<?= e($n) ?>[]" value="<?= (int)$c['id'] ?>" <?= in_array((string)$c['id'], $sel, true) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="q<?= (int)$c['id'] ?>"><?= e($c['title']) ?> <span class="text-muted">· <?= e(type_label($c['type'])) ?> · <?= e(diff_label($c['difficulty'])) ?></span></label></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($f['help'])): ?><div class="form-text"><?= e($f['help']) ?></div><?php endif; ?>
                <div class="invalid-feedback">Revisa este campo.</div>
            </div>
        <?php endforeach; ?>
    </div></div>
    <div class="card-x-body border-top d-flex gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i> Guardar</button><a class="btn btn-light" href="<?= url('admin/' . $key) ?>">Cancelar</a></div>
</form>
