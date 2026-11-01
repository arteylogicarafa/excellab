<?php
class SubmissionsController
{
    public function index(): void
    {
        $where = [];
        $params = [];
        $f = ['status' => input('status'), 'case' => input_int('case'), 'student' => input_int('student'), 'mode' => input('mode', 'file')];
        if (in_array($f['status'], ['submitted', 'reviewed'], true)) { $where[] = 's.status=?'; $params[] = $f['status']; }
        if ($f['case']) { $where[] = 's.case_id=?'; $params[] = $f['case']; }
        if ($f['student']) { $where[] = 's.student_id=?'; $params[] = $f['student']; }
        if (in_array($f['mode'], ['file', 'platform'], true)) { $where[] = 'c.mode=?'; $params[] = $f['mode']; }
        $w = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $rows = DB::all("SELECT s.*, u.name AS student, c.title AS case_title, c.mode, c.points AS max_points FROM case_submissions s
            JOIN users u ON u.id=s.student_id JOIN practical_cases c ON c.id=s.case_id $w ORDER BY (s.status='submitted') DESC, s.id DESC LIMIT 200", $params);
        render('admin/submissions/index', [
            'rows' => $rows, 'f' => $f,
            'cases' => DB::all('SELECT id, title FROM practical_cases ORDER BY title'),
            'students' => DB::all('SELECT id, name FROM users WHERE role_id=3 ORDER BY name'),
        ]);
    }

    public function show(int $id): void
    {
        $s = DB::one('SELECT s.*, u.name AS student, u.email, c.title AS case_title, c.mode, c.points AS max_points, c.criteria, c.id AS cid, c.questions_json
            FROM case_submissions s JOIN users u ON u.id=s.student_id JOIN practical_cases c ON c.id=s.case_id WHERE s.id=?', [$id]);
        if (!$s) abort(404);
        render('admin/submissions/show', ['s' => $s, 'questions' => json_decode((string)$s['questions_json'], true) ?: [], 'results' => array_values(array_filter((array)json_decode((string)$s['answers_json'], true), 'is_array'))]);
    }

    public function grade(int $id): void
    {
        $s = DB::one('SELECT s.*, c.points AS max_points, c.title FROM case_submissions s JOIN practical_cases c ON c.id=s.case_id WHERE s.id=?', [$id]);
        if (!$s) abort(404);
        $score = input('score');
        if (!is_numeric($score) || $score < 0 || $score > $s['max_points']) {
            flash('danger', 'La calificación debe ser un número entre 0 y ' . $s['max_points'] . '.');
            redirect('admin/entregas/' . $id);
        }
        $feedback = mb_substr(input('feedback'), 0, 2000);
        DB::exec("UPDATE case_submissions SET score=?, feedback=?, status='reviewed', reviewed_by=?, reviewed_at=NOW(), updated_at=NOW() WHERE id=?", [round((float)$score, 2), $feedback ?: null, Auth::id(), $id]);
        Notifier::send((int)$s['student_id'], 'Tu entrega fue calificada', $s['title'] . ': ' . round((float)$score, 1) . ' / ' . $s['max_points'] . ' pts', 'casos/' . $s['case_id']);
        BadgeService::check((int)$s['student_id']);
        Activity::log('entrega_calificada', 'case_submissions', $id, $s['title'] . ': ' . $score);
        flash('success', 'Calificación guardada y estudiante notificado.');
        redirect('admin/entregas');
    }
}
