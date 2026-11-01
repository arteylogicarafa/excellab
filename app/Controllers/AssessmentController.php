<?php
class AssessmentController
{
    public function index(): void
    {
        $sid = Auth::id();
        $rows = DB::all("SELECT a.*, m.title AS module_title,
                (SELECT COUNT(*) FROM assessment_questions q JOIN exercises e ON e.id=q.exercise_id AND e.status='active' WHERE q.assessment_id=a.id) AS pool,
                (SELECT COUNT(*) FROM assessment_attempts t WHERE t.assessment_id=a.id AND t.student_id=? AND t.status<>'in_progress') AS tries,
                (SELECT MAX(percent) FROM assessment_attempts t WHERE t.assessment_id=a.id AND t.student_id=? AND t.status<>'in_progress') AS best,
                (SELECT MAX(passed) FROM assessment_attempts t WHERE t.assessment_id=a.id AND t.student_id=?) AS passed
            FROM assessments a LEFT JOIN modules m ON m.id=a.module_id WHERE a.status='active' ORDER BY (a.module_id IS NULL), m.position, a.id", [$sid, $sid, $sid]);
        $history = DB::all("SELECT t.*, a.title FROM assessment_attempts t JOIN assessments a ON a.id=t.assessment_id WHERE t.student_id=? AND t.status<>'in_progress' ORDER BY t.id DESC LIMIT 10", [$sid]);
        render('assessments/index', ['rows' => $rows, 'history' => $history]);
    }

    private function assessment(int $id): array
    {
        $a = DB::one("SELECT a.*, m.title AS module_title FROM assessments a LEFT JOIN modules m ON m.id=a.module_id WHERE a.id=? AND a.status='active'", [$id]);
        if (!$a) abort(404);
        return $a;
    }

    public function show(int $id): void
    {
        $a = $this->assessment($id);
        $pool = (int)DB::val("SELECT COUNT(*) FROM assessment_questions q JOIN exercises e ON e.id=q.exercise_id AND e.status='active' WHERE q.assessment_id=?", [$id]);
        $attempts = DB::all("SELECT * FROM assessment_attempts WHERE assessment_id=? AND student_id=? ORDER BY id DESC", [$id, Auth::id()]);
        $open = null;
        foreach ($attempts as $t) {
            if ($t['status'] === 'in_progress' && $this->deadline($a, $t) > time()) $open = $t;
        }
        $finished = count(array_filter($attempts, fn($t) => $t['status'] !== 'in_progress'));
        render('assessments/show', [
            'a' => $a, 'pool' => $pool, 'attempts' => $attempts, 'open' => $open,
            'questions' => $a['questions_per_attempt'] ? min($a['questions_per_attempt'], $pool) : $pool,
            'canStart' => $pool > 0 && (!$a['max_attempts'] || $finished < $a['max_attempts']),
            'finished' => $finished,
        ]);
    }

    private function deadline(array $a, array $attempt): int
    {
        return strtotime($attempt['started_at']) + $a['time_limit_min'] * 60;
    }

    public function start(int $id): void
    {
        $a = $this->assessment($id);
        $sid = Auth::id();
        foreach (DB::all("SELECT * FROM assessment_attempts WHERE assessment_id=? AND student_id=? AND status='in_progress'", [$id, $sid]) as $t) {
            if ($this->deadline($a, $t) > time()) redirect('evaluaciones/intento/' . $t['id']);
            $this->grade($a, $t, [], 'expired');
        }
        $finished = (int)DB::val("SELECT COUNT(*) FROM assessment_attempts WHERE assessment_id=? AND student_id=? AND status<>'in_progress'", [$id, $sid]);
        if ($a['max_attempts'] && $finished >= $a['max_attempts']) {
            flash('warning', 'Ya usaste todos los intentos disponibles de esta evaluación.');
            redirect('evaluaciones/' . $id);
        }
        $ids = array_column(DB::all("SELECT e.id FROM assessment_questions q JOIN exercises e ON e.id=q.exercise_id WHERE q.assessment_id=? AND e.status='active' ORDER BY q.position", [$id]), 'id');
        if (!$ids) {
            flash('warning', 'Esta evaluación aún no tiene preguntas.');
            redirect('evaluaciones/' . $id);
        }
        if ($a['randomize']) shuffle($ids);
        if ($a['questions_per_attempt'] > 0) $ids = array_slice($ids, 0, (int)$a['questions_per_attempt']);
        $aid = DB::insert('assessment_attempts', [
            'assessment_id' => $id, 'student_id' => $sid, 'question_ids' => json_encode(array_map('intval', $ids)), 'started_at' => date('Y-m-d H:i:s'),
        ]);
        Activity::log('evaluacion_iniciada', 'assessments', $id, $a['title']);
        redirect('evaluaciones/intento/' . $aid);
    }

    private function myAttempt(int $id): array
    {
        $t = DB::one('SELECT * FROM assessment_attempts WHERE id=? AND student_id=?', [$id, Auth::id()]);
        if (!$t) abort(404);
        return $t;
    }

    public function take(int $id): void
    {
        $t = $this->myAttempt($id);
        $a = DB::one('SELECT * FROM assessments WHERE id=?', [$t['assessment_id']]);
        if ($t['status'] !== 'in_progress') redirect('evaluaciones/resultado/' . $id);
        $remaining = $this->deadline($a, $t) - time();
        if ($remaining <= 0) {
            $this->grade($a, $t, [], 'expired');
            flash('warning', 'Se agotó el tiempo. Se calificó tu evaluación.');
            redirect('evaluaciones/resultado/' . $id);
        }
        $questions = [];
        foreach (json_decode($t['question_ids'], true) as $exId) {
            if ($ex = Exercise::find((int)$exId, false)) $questions[] = $ex;
        }
        render('assessments/take', ['a' => $a, 't' => $t, 'questions' => $questions, 'remaining' => $remaining]);
    }

    public function finish(int $id): void
    {
        $t = $this->myAttempt($id);
        $a = DB::one('SELECT * FROM assessments WHERE id=?', [$t['assessment_id']]);
        if ($t['status'] !== 'in_progress') redirect('evaluaciones/resultado/' . $id);
        $late = time() > $this->deadline($a, $t) + 30; // 30 s de tolerancia
        $answers = is_array($_POST['answers'] ?? null) ? $_POST['answers'] : [];
        $this->grade($a, $t, $answers, $late ? 'expired' : 'finished');
        $badges = BadgeService::check(Auth::id());
        foreach ($badges as $b) flash('info', 'Nuevo logro: ' . $b['name']);
        redirect('evaluaciones/resultado/' . $id);
    }

    private function grade(array $a, array $t, array $answers, string $status): void
    {
        $score = $max = 0;
        $detail = [];
        foreach (json_decode($t['question_ids'], true) as $exId) {
            $ex = Exercise::find((int)$exId, false);
            if (!$ex) continue;
            $given = isset($answers[$exId]) && !is_array($answers[$exId]) ? trim((string)$answers[$exId]) : '';
            [$ok] = ExerciseEvaluator::check($ex, $given, $ex['options']);
            $max += (int)$ex['points'];
            $score += $ok ? (int)$ex['points'] : 0;
            $label = $given;
            if ($ex['answer_kind'] === 'option') foreach ($ex['options'] as $o) if ((string)$o['id'] === $given) $label = $o['label'];
            $detail[$exId] = ['answer' => mb_substr($label, 0, 300), 'ok' => $ok];
        }
        $percent = $max ? round($score / $max * 100, 2) : 0;
        $passed = $percent >= $a['pass_percent'] ? 1 : 0;
        DB::exec('UPDATE assessment_attempts SET answers_json=?, score=?, max_score=?, percent=?, passed=?, finished_at=NOW(), status=? WHERE id=? AND status=\'in_progress\'',
            [json_encode($detail, JSON_UNESCAPED_UNICODE), $score, $max, $percent, $passed, $status, $t['id']]);
        Activity::log('evaluacion_finalizada', 'assessments', (int)$a['id'], $a['title'] . ': ' . round($percent) . '%', (int)$t['student_id']);
        Notifier::send((int)$t['student_id'], $passed ? 'Evaluación aprobada' : 'Evaluación finalizada', $a['title'] . ': ' . round($percent) . '% (mínimo ' . $a['pass_percent'] . '%)', 'evaluaciones/resultado/' . $t['id']);
    }

    public function result(int $id): void
    {
        $t = DB::one('SELECT t.*, u.name AS student_name FROM assessment_attempts t JOIN users u ON u.id=t.student_id WHERE t.id=?', [$id]);
        if (!$t) abort(404);
        if (!Auth::isStaff() && (int)$t['student_id'] !== Auth::id()) abort(403);
        if ($t['status'] === 'in_progress') redirect('evaluaciones/intento/' . $id);
        $a = DB::one('SELECT a.*, m.title AS module_title FROM assessments a LEFT JOIN modules m ON m.id=a.module_id WHERE a.id=?', [$t['assessment_id']]);
        $detail = json_decode((string)$t['answers_json'], true) ?: [];
        $questions = [];
        foreach (json_decode($t['question_ids'], true) as $exId) {
            if ($ex = Exercise::find((int)$exId, false)) $questions[] = $ex;
        }
        render('assessments/result', ['a' => $a, 't' => $t, 'questions' => $questions, 'detail' => $detail]);
    }
}
