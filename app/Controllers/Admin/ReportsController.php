<?php
class ReportsController
{
    private const TYPES = ['lesson' => 'Lección', 'exercise' => 'Ejercicio', 'challenge' => 'Reto', 'case' => 'Caso práctico', 'assessment' => 'Evaluación'];

    private function union(): string
    {
        return "SELECT lp.completed_at AS occurred_at, lp.student_id, 'lesson' AS kind, l.title AS activity, l.module_id AS module_id, 'Completada' AS result, NULL AS score
                    FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id
                UNION ALL
                SELECT a.created_at, a.student_id, 'exercise', e.title, e.module_id, CASE WHEN a.is_correct=1 THEN 'Correcto' ELSE 'Incorrecto' END, a.points
                    FROM exercise_attempts a JOIN exercises e ON e.id=a.exercise_id
                UNION ALL
                SELECT a.created_at, a.student_id, 'challenge', c.title, NULL, CASE WHEN a.is_correct=1 THEN 'Superado' ELSE 'Fallido' END, a.points
                    FROM challenge_attempts a JOIN challenges c ON c.id=a.challenge_id
                UNION ALL
                SELECT s.created_at, s.student_id, 'case', pc.title, NULL, CASE WHEN s.status='reviewed' THEN 'Revisado' ELSE 'Pendiente' END, s.score
                    FROM case_submissions s JOIN practical_cases pc ON pc.id=s.case_id
                UNION ALL
                SELECT t.finished_at, t.student_id, 'assessment', a.title, a.module_id, CASE WHEN t.passed=1 THEN 'Aprobado' ELSE 'No aprobado' END, t.percent
                    FROM assessment_attempts t JOIN assessments a ON a.id=t.assessment_id WHERE t.status<>'in_progress'";
    }

    public function index(): void
    {
        $f = [
            'student' => input_int('student'), 'module' => input_int('module'), 'type' => input('type'),
            'from' => input('from'), 'to' => input('to'),
        ];
        $where = [];
        $params = [];
        if ($f['student']) { $where[] = 'x.student_id=?'; $params[] = $f['student']; }
        if ($f['module']) { $where[] = 'x.module_id=?'; $params[] = $f['module']; }
        if (isset(self::TYPES[$f['type']])) { $where[] = 'x.kind=?'; $params[] = $f['type']; }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['from'])) { $where[] = 'x.occurred_at >= ?'; $params[] = $f['from'] . ' 00:00:00'; }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['to'])) { $where[] = 'x.occurred_at <= ?'; $params[] = $f['to'] . ' 23:59:59'; }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $base = 'FROM (' . $this->union() . ') x JOIN users u ON u.id=x.student_id LEFT JOIN modules m ON m.id=x.module_id' . $w;

        if (input('export') === '1') {
            $rows = DB::all("SELECT x.occurred_at, u.name AS student, x.kind, x.activity, m.title AS module_title, x.result, x.score $base ORDER BY x.occurred_at DESC NULLS LAST LIMIT 20000", $params);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="reporte_excellab_' . date('Ymd_His') . '.csv"');
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Fecha', 'Estudiante', 'Tipo', 'Actividad', 'Módulo', 'Resultado', 'Puntaje'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [$r['occurred_at'], $r['student'], self::TYPES[$r['kind']], $r['activity'], $r['module_title'], $r['result'], $r['score'] !== null ? str_replace('.', ',', (string)round((float)$r['score'], 2)) : ''], ';');
            }
            fclose($out);
            Activity::log('reporte_exportado', 'reports', null, (string)count($rows) . ' filas');
            exit;
        }

        $rows = DB::all("SELECT x.occurred_at, u.id AS student_id, u.name AS student, x.kind, x.activity, m.title AS module_title, x.result, x.score $base ORDER BY x.occurred_at DESC NULLS LAST LIMIT 300", $params);
        $summary = DB::one("SELECT COUNT(*) AS total, COUNT(DISTINCT x.student_id) AS students,
                SUM(CASE WHEN x.kind='exercise' AND x.result='Correcto' THEN 1 ELSE 0 END) AS ok_ex, SUM(CASE WHEN x.kind='exercise' THEN 1 ELSE 0 END) AS all_ex,
                AVG(CASE WHEN x.kind='assessment' THEN x.score END) AS avg_assess $base", $params);
        render('admin/reports/index', [
            'rows' => $rows, 'f' => $f, 'summary' => $summary, 'types' => self::TYPES,
            'students' => DB::all('SELECT id, name FROM users WHERE role_id=3 ORDER BY name'),
            'modules' => DB::all('SELECT id, title FROM modules ORDER BY position'),
            'query' => http_build_query(array_filter($f)),
        ]);
    }
}
