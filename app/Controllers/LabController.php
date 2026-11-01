<?php
class LabController
{
    public function index(): void
    {
        $sid = Auth::id();
        $where = ["e.status='active'", "m.status='active'"];
        $params = [];
        $f = [
            'module'     => input_int('module'),
            'difficulty' => input('difficulty'),
            'type'       => input('type'),
            'topic'      => input('topic'),
            'state'      => input('state'),
            'q'          => input('q'),
        ];
        if ($f['module']) { $where[] = 'e.module_id=?'; $params[] = $f['module']; }
        if (in_array($f['difficulty'], ['basico', 'intermedio', 'avanzado'], true)) { $where[] = 'e.difficulty=?'; $params[] = $f['difficulty']; }
        if (array_key_exists($f['type'], ['multiple_choice' => 1, 'true_false' => 1, 'complete_formula' => 1, 'identify_function' => 1, 'solve_operation' => 1, 'analyze_table' => 1, 'practical' => 1])) { $where[] = 'e.type=?'; $params[] = $f['type']; }
        if ($f['topic'] !== '') { $where[] = 'e.topic=?'; $params[] = $f['topic']; }
        if ($f['q'] !== '') { $where[] = '(e.title ILIKE ? OR e.statement ILIKE ?)'; $like = '%' . addcslashes($f['q'], '%_') . '%'; array_push($params, $like, $like); }
        $rows = DB::all('SELECT e.id, e.title, e.topic, e.difficulty, e.type, e.points, m.title AS module_title FROM exercises e JOIN modules m ON m.id=e.module_id WHERE ' . implode(' AND ', $where) . " ORDER BY m.position, CASE e.difficulty WHEN 'basico' THEN 1 WHEN 'intermedio' THEN 2 WHEN 'avanzado' THEN 3 END, e.id", $params);
        $status = Auth::role() === 'estudiante' ? Exercise::studentStatus($sid) : [];
        if ($f['state'] === 'solved') $rows = array_filter($rows, fn($r) => !empty($status[$r['id']]['solved']));
        if ($f['state'] === 'pending') $rows = array_filter($rows, fn($r) => empty($status[$r['id']]['solved']));
        render('lab/index', [
            'rows' => array_values($rows), 'status' => $status, 'f' => $f,
            'modules' => DB::all("SELECT id, title FROM modules WHERE status='active' ORDER BY position"),
            'topics' => array_column(DB::all("SELECT DISTINCT topic FROM exercises WHERE status='active' ORDER BY topic"), 'topic'),
        ]);
    }

    public function show(int $id): void
    {
        $ex = Exercise::find($id);
        if (!$ex) abort(404);
        $attempts = Auth::role() === 'estudiante'
            ? DB::all('SELECT * FROM exercise_attempts WHERE exercise_id=? AND student_id=? ORDER BY id DESC LIMIT 8', [$id, Auth::id()]) : [];
        $siblings = DB::all("SELECT id FROM exercises WHERE status='active' ORDER BY module_id, id");
        $ids = array_column($siblings, 'id');
        $pos = array_search($id, $ids);
        render('lab/show', [
            'ex' => $ex, 'attempts' => $attempts,
            'prevId' => $pos > 0 ? $ids[$pos - 1] : null, 'nextId' => $ids[$pos + 1] ?? null,
        ]);
    }

    /** Comprueba una respuesta (AJAX) y registra el intento del estudiante. */
    public function check(int $id): void
    {
        $ex = Exercise::find($id);
        if (!$ex) json_out(['ok' => false, 'message' => 'Ejercicio no encontrado.'], 404);
        $answer = input('answer');
        if ($answer === '') json_out(['ok' => false, 'message' => 'Escribe o selecciona una respuesta antes de comprobar.'], 422);
        if (mb_strlen($answer) > 500) json_out(['ok' => false, 'message' => 'La respuesta es demasiado larga.'], 422);
        $context = input('context') === 'lesson' ? 'lesson' : 'lab';

        [$correct, $correctText] = ExerciseEvaluator::check($ex, $answer, $ex['options']);
        $attempts = 0;
        $awarded = [];
        $shownAnswer = $answer;
        if ($ex['answer_kind'] === 'option') {
            foreach ($ex['options'] as $o) if ((string)$o['id'] === $answer) $shownAnswer = $o['label'];
        }
        $points = $correct ? (int)$ex['points'] : 0;
        if (Auth::role() === 'estudiante') {
            DB::insert('exercise_attempts', [
                'exercise_id' => $id, 'student_id' => Auth::id(), 'context' => $context,
                'answer' => mb_substr($shownAnswer, 0, 500), 'is_correct' => (int)$correct, 'points' => $points,
            ]);
            $attempts = (int)DB::val('SELECT COUNT(*) FROM exercise_attempts WHERE exercise_id=? AND student_id=? AND is_correct=0', [$id, Auth::id()]);
            Activity::log($correct ? 'ejercicio_correcto' : 'ejercicio_incorrecto', 'exercises', $id, $ex['title']);
            if ($correct) $awarded = BadgeService::check(Auth::id());
        }
        json_out([
            'ok' => true, 'correct' => $correct, 'points' => $points,
            'message' => $correct ? '¡Correcto! Excelente trabajo.' : 'Respuesta incorrecta. Revisa la explicación e inténtalo de nuevo.',
            'explanation' => $ex['explanation'],
            'correct_answer' => ($correct || $attempts >= 2 || Auth::role() !== 'estudiante') ? $correctText : null,
            'badges' => $awarded,
            'preview' => Auth::role() !== 'estudiante',
        ]);
    }
}
