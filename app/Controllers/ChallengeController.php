<?php
class ChallengeController
{
    public function index(): void
    {
        $sid = Auth::id();
        $rows = DB::all("SELECT c.*, (SELECT MAX(is_correct) FROM challenge_attempts a WHERE a.challenge_id=c.id AND a.student_id=?) AS solved,
                (SELECT COUNT(*) FROM challenge_attempts a WHERE a.challenge_id=c.id AND a.student_id=?) AS attempts
            FROM challenges c WHERE c.status='active' ORDER BY CASE c.difficulty WHEN 'basico' THEN 1 WHEN 'intermedio' THEN 2 WHEN 'avanzado' THEN 3 END, c.position, c.id", [$sid, $sid]);
        $solved = count(array_filter($rows, fn($r) => $r['solved']));
        $earned = (int)array_sum(array_map(fn($r) => $r['solved'] ? $r['points'] : 0, $rows));
        render('challenges/index', ['rows' => $rows, 'solved' => $solved, 'earned' => $earned]);
    }

    public function show(int $id): void
    {
        $c = DB::one("SELECT * FROM challenges WHERE id=? AND status='active'", [$id]);
        if (!$c) abort(404);
        $attempts = Auth::role() === 'estudiante' ? DB::all('SELECT * FROM challenge_attempts WHERE challenge_id=? AND student_id=? ORDER BY id DESC LIMIT 8', [$id, Auth::id()]) : [];
        $solved = (bool)array_filter($attempts, fn($a) => $a['is_correct']);
        render('challenges/show', ['c' => $c, 'sheet' => json_decode((string)$c['data_json'], true), 'attempts' => $attempts, 'solved' => $solved]);
    }

    public function check(int $id): void
    {
        $c = DB::one("SELECT * FROM challenges WHERE id=? AND status='active'", [$id]);
        if (!$c) json_out(['ok' => false, 'message' => 'Reto no encontrado.'], 404);
        $answer = input('answer');
        if ($answer === '') json_out(['ok' => false, 'message' => 'Escribe tu resultado antes de validar.'], 422);
        $correct = ExerciseEvaluator::matches($c['answer_kind'], $answer, $c['correct_answer'], (float)$c['tolerance']);
        $points = 0;
        $badges = [];
        if (Auth::role() === 'estudiante') {
            $already = (bool)DB::val('SELECT 1 FROM challenge_attempts WHERE challenge_id=? AND student_id=? AND is_correct=1 LIMIT 1', [$id, Auth::id()]);
            $points = ($correct && !$already) ? (int)$c['points'] : 0;
            DB::insert('challenge_attempts', [
                'challenge_id' => $id, 'student_id' => Auth::id(), 'answer' => mb_substr($answer, 0, 500),
                'is_correct' => (int)$correct, 'points' => $points, 'seconds_spent' => min(86400, max(0, input_int('seconds'))),
            ]);
            Activity::log($correct ? 'reto_superado' : 'reto_fallido', 'challenges', $id, $c['title']);
            if ($correct) $badges = BadgeService::check(Auth::id());
        }
        json_out([
            'ok' => true, 'correct' => $correct, 'points' => $points, 'already' => isset($already) && $already,
            'message' => $correct ? ($c['feedback_ok'] ?: '¡Reto superado!') : ($c['feedback_fail'] ?: 'Resultado incorrecto. Revisa los datos e inténtalo de nuevo.'),
            'badges' => $badges, 'preview' => Auth::role() !== 'estudiante',
        ]);
    }
}
