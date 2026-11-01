<?php
class TrackingController
{
    public function index(): void
    {
        $q = input('q');
        $params = [];
        $w = '';
        if ($q !== '') { $w = ' AND (u.name ILIKE ? OR u.email ILIKE ?)'; $like = '%' . addcslashes($q, '%_\\') . '%'; $params = [$like, $like]; }
        $students = DB::all("SELECT u.id, u.name, u.email, u.status,
                (SELECT MAX(created_at) FROM activity_log al WHERE al.user_id=u.id) AS last_activity
            FROM users u WHERE u.role_id=3 AND u.status<>'archived' $w ORDER BY u.name", $params);
        $map = Stats::pointsMap();
        foreach ($students as &$s) {
            $s['progress'] = ProgressService::overall((int)$s['id']);
            $s['points'] = (int)round($map[$s['id']] ?? 0);
        }
        unset($s);
        render('admin/tracking/index', ['students' => $students, 'q' => $q]);
    }

    public function student(int $id): void
    {
        $u = DB::one('SELECT id, name, email, status, last_login_at, created_at FROM users WHERE id=? AND role_id=3', [$id]);
        if (!$u) abort(404);
        render('admin/tracking/student', [
            'u' => $u,
            'overall' => ProgressService::overall($id),
            'mods' => ProgressService::modules($id),
            'counts' => Stats::counts($id),
            'exAttempts' => DB::all('SELECT a.*, e.title FROM exercise_attempts a JOIN exercises e ON e.id=a.exercise_id WHERE a.student_id=? ORDER BY a.id DESC LIMIT 25', [$id]),
            'chAttempts' => DB::all('SELECT a.*, c.title FROM challenge_attempts a JOIN challenges c ON c.id=a.challenge_id WHERE a.student_id=? ORDER BY a.id DESC LIMIT 15', [$id]),
            'asAttempts' => DB::all("SELECT t.*, a.title FROM assessment_attempts t JOIN assessments a ON a.id=t.assessment_id WHERE t.student_id=? AND t.status<>'in_progress' ORDER BY t.id DESC LIMIT 15", [$id]),
            'cases' => DB::all('SELECT s.*, c.title, c.points AS max_points, c.mode FROM case_submissions s JOIN practical_cases c ON c.id=s.case_id WHERE s.student_id=? ORDER BY s.id DESC LIMIT 15', [$id]),
            'achievements' => DB::all('SELECT a.*, sa.awarded_at FROM student_achievements sa JOIN achievements a ON a.id=sa.achievement_id WHERE sa.student_id=? ORDER BY sa.awarded_at DESC', [$id]),
            'log' => DB::all('SELECT * FROM activity_log WHERE user_id=? ORDER BY id DESC LIMIT 20', [$id]),
            'chart' => Stats::pointsByDay($id, 14),
        ]);
    }
}
