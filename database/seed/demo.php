<?php
// Evaluaciones, logros y actividad de demostración.
$ach = [
    ['primeros-pasos', 'Primeros pasos', 'Completa tu primera lección.', 'bi-flag', 'lessons', 1],
    ['aprendiz', 'Aprendiz constante', 'Completa 10 lecciones.', 'bi-book', 'lessons', 10],
    ['modulo-completo', 'Módulo completado', 'Termina todas las lecciones de un módulo.', 'bi-patch-check', 'modules', 1],
    ['explorador', 'Explorador', 'Completa 3 módulos.', 'bi-collection', 'modules', 3],
    ['practicante', 'Practicante', 'Resuelve correctamente 5 ejercicios.', 'bi-pencil', 'exercises', 5],
    ['calculista', 'Calculista', 'Resuelve correctamente 20 ejercicios.', 'bi-calculator', 'exercises', 20],
    ['primer-reto', 'Primer reto', 'Supera tu primer Excel Challenge.', 'bi-trophy', 'challenges', 1],
    ['retador', 'Retador', 'Supera 4 retos.', 'bi-lightning', 'challenges', 4],
    ['cien-puntos', '100 puntos', 'Acumula 100 puntos.', 'bi-star', 'points', 100],
    ['quinientos-puntos', '500 puntos', 'Acumula 500 puntos.', 'bi-stars', 'points', 500],
    ['evaluado', 'Evaluado', 'Aprueba una evaluación.', 'bi-clipboard-check', 'assessments', 1],
    ['caso-resuelto', 'Caso resuelto', 'Entrega tu primer caso práctico.', 'bi-briefcase', 'cases', 1],
];
$achId = [];
foreach ($ach as [$code, $name, $desc, $icon, $metric, $th]) {
    $achId[$code] = ins('achievements', ['code' => $code, 'name' => $name, 'description' => $desc, 'icon' => $icon, 'metric' => $metric, 'threshold' => $th, 'status' => 'active']);
}

// ---- Evaluaciones: una por módulo y una integradora ----
$asId = [];
foreach ($modules as $i => [$mTitle]) {
    $m = $i + 1;
    $pool = $EXBYMOD[$m];
    $aid = ins('assessments', [
        'module_id' => $m, 'title' => 'Evaluación del módulo ' . $m . ': ' . $mTitle,
        'description' => 'Preguntas del módulo "' . $mTitle . '". Se sortean al azar en cada intento y se califican automáticamente.',
        'time_limit_min' => 10, 'pass_percent' => 60, 'questions_per_attempt' => min(5, count($pool)), 'randomize' => 1, 'max_attempts' => 0, 'status' => 'active',
    ]);
    $asId[$m] = $aid;
    foreach ($pool as $p => $exId) ins('assessment_questions', ['assessment_id' => $aid, 'exercise_id' => $exId, 'position' => $p + 1]);
}
$final = ins('assessments', [
    'module_id' => null, 'title' => 'Evaluación final integradora',
    'description' => 'Reúne preguntas de todos los módulos. Mide el dominio general de Excel del curso.',
    'time_limit_min' => 30, 'pass_percent' => 70, 'questions_per_attempt' => 12, 'randomize' => 1, 'max_attempts' => 3, 'status' => 'active',
]);
$k = 1;
foreach ($EXBYMOD as $pool) foreach ($pool as $exId) ins('assessment_questions', ['assessment_id' => $final, 'exercise_id' => $exId, 'position' => $k++]);

// ---- Actividad de demostración ----
$ts = fn(int $daysAgo, int $hour = 10, int $min = 0) => date('Y-m-d H:i:s', strtotime("-$daysAgo days $hour:" . sprintf('%02d', $min) . ':00'));
// Puntaje de cada ejercicio de lección según su dificultad (se lee de la fila ya generada)
$lessonPts = [];
foreach ($LESSON_EX as $lid => $exId) {
    $row = $OUT['exercises'][$exId - 1];
    preg_match("/'(basico|intermedio|avanzado)'/", $row, $mm);
    $lessonPts[$lid] = ['basico' => 10, 'intermedio' => 15, 'avanzado' => 20][$mm[1]];
}

// student_id => [módulo => cantidad de lecciones completadas (o 'all')]
$plan = [3 => [1 => 'all', 2 => 'all', 3 => 3], 4 => [1 => 'all', 2 => 2], 5 => [1 => 4], 6 => [1 => 2], 7 => [1 => 1]];
$stats = [];
$day = 14;
foreach ($plan as $sid => $mods) {
    $stats[$sid] = ['lessons' => 0, 'exercises' => 0, 'challenges' => 0, 'points' => 0, 'modules' => 0, 'assessments' => 0, 'cases' => 0];
    $d = $day;
    foreach ($mods as $m => $n) {
        $ls = $LESSONS[$m];
        $take = $n === 'all' ? count($ls) : $n;
        foreach (array_slice($ls, 0, $take) as $idx => $lid) {
            $when = $ts(max(0, $d), 9 + ($idx % 8), ($idx * 7) % 60);
            if ($idx % 3 === 2) { // un intento fallido previo
                ins('exercise_attempts', ['exercise_id' => $LESSON_EX[$lid], 'student_id' => $sid, 'context' => 'lesson', 'answer' => '—', 'is_correct' => 0, 'points' => 0, 'created_at' => $when]);
            }
            ins('exercise_attempts', ['exercise_id' => $LESSON_EX[$lid], 'student_id' => $sid, 'context' => 'lesson', 'answer' => 'respuesta correcta', 'is_correct' => 1, 'points' => $lessonPts[$lid], 'created_at' => $when]);
            ins('lesson_progress', ['student_id' => $sid, 'lesson_id' => $lid, 'completed_at' => $when]);
            $stats[$sid]['lessons']++;
            $stats[$sid]['exercises']++;
            $stats[$sid]['points'] += $lessonPts[$lid];
            if ($idx % 2 === 1) $d = max(0, $d - 1);
        }
        $pct = round($take / count($ls) * 100, 2);
        $done = $take === count($ls);
        if ($done) $stats[$sid]['modules']++;
        ins('student_progress', ['student_id' => $sid, 'module_id' => $m, 'lessons_total' => count($ls), 'lessons_done' => $take, 'percent' => $pct,
            'completed_at' => $done ? $ts(max(0, $d)) : null, 'certificate_code' => $done ? sprintf('XL-%02d-%03d-%s', $m, $sid, strtoupper(substr(md5("cert$m$sid"), 0, 5))) : null]);
    }
}

// Retos superados
$chPts = [1 => 20, 2 => 20, 3 => 25];
$chPlan = [3 => [[1, 1, 2], [2, 1, 3], [3, 1, 4]], 4 => [[1, 0, 6], [1, 1, 5], [2, 1, 4]], 5 => [[1, 1, 3]]];
foreach ($chPlan as $sid => $rows) {
    foreach ($rows as [$cid, $ok, $dAgo]) {
        ins('challenge_attempts', ['challenge_id' => $cid, 'student_id' => $sid, 'answer' => $ok ? 'correcta' : '0', 'is_correct' => $ok, 'points' => $ok ? $chPts[$cid] : 0, 'seconds_spent' => 120 + $cid * 45, 'created_at' => $ts($dAgo, 16)]);
        if ($ok) { $stats[$sid]['challenges']++; $stats[$sid]['points'] += $chPts[$cid]; }
    }
}

// Casos prácticos (auto-calificados)
foreach ([[3, 1, 50, 2, 4], [3, 3, 37.5, 1, 3], [4, 1, 37.5, 3, 3]] as [$sid, $cid, $score, $dAgo, $okCount]) {
    $results = [];
    for ($i = 0; $i < 4; $i++) $results[] = ['answer' => '', 'ok' => $i < $okCount];
    ins('case_submissions', ['case_id' => $cid, 'student_id' => $sid, 'answers_json' => js($results), 'score' => $score, 'status' => 'reviewed', 'feedback' => "Calificación automática: $okCount de 4 respuestas correctas.", 'created_at' => $ts($dAgo, 11)]);
    $stats[$sid]['cases']++;
    $stats[$sid]['points'] += $score;
}

// Evaluaciones aprobadas
foreach ([[3, 1, 100, 5, 5, 3], [4, 1, 80, 4, 5, 2], [5, 1, 40, 2, 5, 1]] as [$sid, $m, $pct, $sc, $max, $dAgo]) {
    $qids = array_slice($EXBYMOD[$m], 0, 5);
    ins('assessment_attempts', ['assessment_id' => $asId[$m], 'student_id' => $sid, 'question_ids' => json_encode($qids), 'answers_json' => null,
        'score' => $sc * 10, 'max_score' => $max * 10, 'percent' => $pct, 'passed' => $pct >= 60 ? 1 : 0, 'started_at' => $ts($dAgo, 14), 'finished_at' => $ts($dAgo, 14, 8), 'status' => 'finished']);
    if ($pct >= 60) $stats[$sid]['assessments']++;
}

// Logros otorgados
foreach ($stats as $sid => $s) {
    foreach ($ach as [$code, $name, , , $metric, $th]) {
        if ($s[$metric] >= $th) {
            ins('student_achievements', ['student_id' => $sid, 'achievement_id' => $achId[$code], 'awarded_at' => $ts(1, 12)]);
            ins('notifications', ['user_id' => $sid, 'title' => '¡Nuevo logro: ' . $name . '!', 'message' => 'Has desbloqueado la insignia "' . $name . '".', 'link' => 'logros', 'is_read' => 0, 'created_at' => $ts(1, 12)]);
        }
    }
}
// Bienvenida e historial
for ($u = 3; $u <= 8; $u++) {
    ins('notifications', ['user_id' => $u, 'title' => 'Bienvenido a ExcelLab', 'message' => 'Empieza por el módulo 1 "Introducción a Excel". ¡Mucho éxito!', 'link' => 'aprender', 'is_read' => 0, 'created_at' => $ts(15, 8)]);
}
foreach ([3, 4, 5, 6, 7] as $sid) {
    ins('activity_log', ['user_id' => $sid, 'action' => 'inicio_sesion', 'entity' => 'users', 'entity_id' => $sid, 'detail' => null, 'ip' => '127.0.0.1', 'created_at' => $ts(2, 8)]);
}
