<?php
/** Hoja tipo Excel. Variables: $data = ['headers'=>[], 'rows'=>[[]]], $caption (opcional) */
if (empty($data) || empty($data['headers'])) return;
$headers = $data['headers'];
$rows = $data['rows'] ?? [];
$cols = count($headers);
?>
<div class="xl-sheet my-3">
    <table>
        <?php if (!empty($caption)): ?><caption><?= e($caption) ?></caption><?php endif; ?>
        <thead>
            <tr>
                <th class="corner"></th>
                <?php for ($c = 0; $c < $cols; $c++): ?><th class="colh"><?= col_letter($c) ?></th><?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="rowh">1</th>
                <?php foreach ($headers as $h): ?><td class="hd"><?= e($h) ?></td><?php endforeach; ?>
            </tr>
            <?php foreach ($rows as $i => $row): ?>
                <tr>
                    <th class="rowh"><?= $i + 2 ?></th>
                    <?php for ($c = 0; $c < $cols; $c++):
                        $v = $row[$c] ?? '';
                        $isNum = is_int($v) || is_float($v);
                        ?>
                        <td class="<?= $isNum ? 'num' : '' ?><?= $v === '' ? ' empty-cell' : '' ?>"><?= e(is_float($v) ? rtrim(rtrim(number_format($v, 4, ',', '.'), '0'), ',') : $v) ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
