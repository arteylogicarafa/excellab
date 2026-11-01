<?php
class AdminDashboardController
{
    public function index(): void
    {
        $students = (int)DB::val("SELECT COUNT(*) FROM users WHERE role_id=3 AND status<>'archived'");
        $active = (int)DB::val("SELECT COUNT(DISTINCT al.user_id) FROM activity_log al JOIN users u ON u.id=al.user_id WHERE u.role_id=3 AND u.status='active' AND al.created_at >= NOW() - INTERVAL '30 days'");
        $kpi = [
            'students'    => $students,
            'active'      => $active,
            'modules'     => (int)DB::val("SELECT COUNT(*) FROM modules WHERE status='active'"),
            'exercises'   => (int)DB::val("SELECT COUNT(*) FROM exercises WHERE status='active'"),
            'assessments' => (int)DB::val("SELECT COUNT(*) FROM assessment_attempts WHERE status<>'in_progress'"),
            'average'     => round((float)DB::val("SELECT AVG(percent) FROM assessment_attempts WHERE status<>'in_progress'"), 1),
            'pending'     => (int)DB::val("SELECT COUNT(*) FROM case_submissions WHERE status='submitted'"),
            'lessons'     => (int)DB::val("SELECT COUNT(*) FROM lessons WHERE status='active'"),
        ];

        // Actividad diaria (intentos y entregas) en los últimos 14 días
        $days = [];
        for ($i = 13; $i >= 0; $i--) $days[date('Y-m-d', strtotime("-$i days"))] = 0;
        $from = array_key_first($days);
        $sources = [
            'SELECT DATE(created_at) d, COUNT(*) n FROM exercise_attempts WHERE created_at >= ? GROUP BY d',
            'SELECT DATE(created_at) d, COUNT(*) n FROM challenge_attempts WHERE created_at >= ? GROUP BY d',
            'SELECT DATE(created_at) d, COUNT(*) n FROM case_submissions WHERE created_at >= ? GROUP BY d',
            'SELECT DATE(started_at) d, COUNT(*) n FROM assessment_attempts WHERE started_at >= ? GROUP BY d',
            'SELECT DATE(completed_at) d, COUNT(*) n FROM lesson_progress WHERE completed_at >= ? GROUP BY d',
        ];
        foreach ($sources as $sql) foreach (DB::all($sql, [$from]) as $r) if (isset($days[$r['d']])) $days[$r['d']] += (int)$r['n'];

        $moduleStats = DB::all("SELECT m.title, COALESCE(SUM(sp.percent),0) AS total FROM modules m
            LEFT JOIN student_progress sp ON sp.module_id=m.id AND sp.student_id IN (SELECT id FROM users WHERE role_id=3 AND status='active')
            WHERE m.status='active' GROUP BY m.id ORDER BY m.position");
        $activeStudents = max(1, (int)DB::val("SELECT COUNT(*) FROM users WHERE role_id=3 AND status='active'"));
        foreach ($moduleStats as &$m) $m['avg'] = round($m['total'] / $activeStudents, 1);
        unset($m);

        $passed = (int)DB::val("SELECT COUNT(*) FROM assessment_attempts WHERE status<>'in_progress' AND passed=1");
        $failed = $kpi['assessments'] - $passed;

        $map = Stats::pointsMap();
        $top = [];
        foreach (DB::all("SELECT id, name FROM users WHERE role_id=3 AND status='active'") as $s) $top[] = ['id' => $s['id'], 'name' => $s['name'], 'points' => (int)round($map[$s['id']] ?? 0), 'progress' => ProgressService::overall((int)$s['id'])];
        usort($top, fn($a, $b) => $b['points'] <=> $a['points']);

        $pendingSubs = DB::all("SELECT s.id, s.created_at, u.name, c.title FROM case_submissions s JOIN users u ON u.id=s.student_id JOIN practical_cases c ON c.id=s.case_id WHERE s.status='submitted' ORDER BY s.id DESC LIMIT 5");

        render('admin/dashboard', [
            'kpi' => $kpi, 'dayLabels' => array_map(fn($d) => date('d/m', strtotime($d)), array_keys($days)), 'dayValues' => array_values($days),
            'moduleStats' => $moduleStats, 'passed' => $passed, 'failed' => $failed, 'top' => array_slice($top, 0, 5), 'pendingSubs' => $pendingSubs,
        ]);
    }
}
