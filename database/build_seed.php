<?php
/**
 * Generador de database/seed.sql (datos de demostración de ExcelLab).
 * Uso:  php database/build_seed.php
 * Los contenidos están en database/seed/*.php. No es necesario ejecutarlo para instalar:
 * el archivo seed.sql ya viene generado.
 */
$OUT = [];     // tabla => filas SQL
$COLS = [];    // tabla => columnas
$ID = [];      // tabla => último id
$EXBYMOD = []; // módulo => ids de ejercicios
$LESSONS = []; // módulo => ids de lecciones
$LESSON_EX = []; // lección => id del ejercicio de comprobación

function esc($v): string
{
    if ($v === null) return 'NULL';
    if (is_int($v) || is_float($v)) return (string)$v;
    if (is_bool($v)) return $v ? '1' : '0';
    // Cadenas con escape tipo E'' de PostgreSQL (equivalente a los escapes de MySQL).
    return "E'" . str_replace(["\\", "'", "\r", "\n"], ["\\\\", "''", "\\r", "\\n"], (string)$v) . "'";
}

function ins(string $t, array $row): int
{
    global $OUT, $COLS, $ID;
    $id = $row['id'] ?? (($ID[$t] ?? 0) + 1);
    $ID[$t] = max($ID[$t] ?? 0, $id);
    if ($t !== 'settings') {
        $row = ['id' => $id] + $row;
    }
    $COLS[$t] = array_keys($row);
    $OUT[$t][] = '(' . implode(',', array_map('esc', $row)) . ')';
    return $id;
}

function S(array $headers, array $rows): array
{
    return ['headers' => $headers, 'rows' => $rows];
}

function js($v): ?string
{
    return $v === null ? null : json_encode($v, JSON_UNESCAPED_UNICODE);
}

function L(int $m, string $title, string $desc, string $theory, string $example, ?array $sheet = null, ?string $img = null): int
{
    global $LESSONS;
    $pos = count($LESSONS[$m] ?? []) + 1;
    $id = ins('lessons', [
        'module_id' => $m, 'title' => $title, 'description' => $desc, 'theory' => $theory,
        'example' => $example, 'example_data' => js($sheet), 'image_url' => $img, 'position' => $pos, 'status' => 'active',
    ]);
    $LESSONS[$m][] = $id;
    return $id;
}

/**
 * Ejercicio. $answer: lista de opciones (prefijo * = correcta) o respuesta textual.
 * $o: data (hoja), instr, kind (number|formula|text), tol.
 */
function X(int $m, ?int $lesson, string $title, string $topic, string $diff, string $type, string $statement, $answer, string $explanation, ?int $pts = null, array $o = []): int
{
    global $EXBYMOD, $LESSON_EX;
    $pts = $pts ?? ['basico' => 10, 'intermedio' => 15, 'avanzado' => 20][$diff];
    $isOpts = is_array($answer);
    $id = ins('exercises', [
        'module_id' => $m, 'lesson_id' => $lesson, 'title' => $title, 'topic' => $topic, 'difficulty' => $diff, 'type' => $type,
        'statement' => $statement, 'instructions' => $o['instr'] ?? null, 'data_json' => js($o['data'] ?? null),
        'answer_kind' => $isOpts ? 'option' : ($o['kind'] ?? 'text'),
        'correct_answer' => $isOpts ? null : $answer, 'tolerance' => $o['tol'] ?? 0.01,
        'explanation' => $explanation, 'points' => $pts, 'status' => 'active',
    ]);
    if ($isOpts) {
        foreach ($answer as $i => $label) {
            $ok = str_starts_with($label, '*');
            ins('exercise_options', ['exercise_id' => $id, 'label' => $ok ? substr($label, 1) : $label, 'is_correct' => $ok ? 1 : 0, 'position' => $i + 1]);
        }
    }
    $EXBYMOD[$m][] = $id;
    if ($lesson) $LESSON_EX[$lesson] = $id;
    return $id;
}

// ---------------------------------------------------------------------------
$HASHES = [
    'admin'      => password_hash('Admin#2026', PASSWORD_DEFAULT),
    'docente'    => password_hash('Docente#2026', PASSWORD_DEFAULT),
    'estudiante' => password_hash('Estudiante#2026', PASSWORD_DEFAULT),
];

require __DIR__ . '/seed/base.php';      // roles, usuarios, módulos, ajustes
require __DIR__ . '/seed/content1.php';  // módulos 1-4
require __DIR__ . '/seed/content2.php';  // módulos 5-8
require __DIR__ . '/seed/extras.php';    // ejercicios extra, retos, casos
require __DIR__ . '/seed/demo.php';      // evaluaciones, logros y actividad de demostración

$order = ['roles', 'users', 'modules', 'lessons', 'exercises', 'exercise_options', 'challenges', 'practical_cases',
    'assessments', 'assessment_questions', 'achievements', 'lesson_progress', 'student_progress', 'exercise_attempts',
    'challenge_attempts', 'case_submissions', 'assessment_attempts', 'student_achievements', 'notifications', 'activity_log', 'settings'];

$sql = "-- ExcelLab · Datos de demostración (generado con database/build_seed.php)\n-- PostgreSQL / Neon\nSET client_encoding = 'UTF8';\n\n";
foreach ($order as $t) {
    if (empty($OUT[$t])) continue;
    foreach (array_chunk($OUT[$t], 50) as $chunk) {
        $sql .= 'INSERT INTO ' . $t . ' (' . implode(',', $COLS[$t]) . ") VALUES\n" . implode(",\n", $chunk) . ";\n";
    }
    $sql .= "\n";
}
// Reiniciar las secuencias de los id para que sigan después de los ids explícitos.
foreach ($order as $t) {
    if (empty($OUT[$t]) || $t === 'settings' || !in_array('id', $COLS[$t] ?? [], true)) continue;
    $sql .= 'ALTER TABLE ' . $t . ' ALTER COLUMN id RESTART WITH ' . (($ID[$t] ?? 0) + 1) . ";\n";
}
$sql .= "\n";
file_put_contents(__DIR__ . '/seed.sql', $sql);

echo "seed.sql generado: " . strlen($sql) . " bytes\n";
foreach ($order as $t) {
    if (!empty($OUT[$t])) echo str_pad($t, 24) . count($OUT[$t]) . "\n";
}
