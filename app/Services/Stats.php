<?php
/** Cálculos de puntaje, métricas y actividad reciente. */
class Stats
{
    /** Puntos por estudiante: mejor resultado de cada ejercicio, reto y caso. */
    public static function pointsMap(): array
    {
        $map = [];
        $queries = [
            'SELECT student_id, SUM(m) p FROM (SELECT student_id, MAX(points) m FROM exercise_attempts GROUP BY student_id, exercise_id) t GROUP BY student_id',
            'SELECT student_id, SUM(m) p FROM (SELECT student_id, MAX(points) m FROM challenge_attempts GROUP BY student_id, challenge_id) t GROUP BY student_id',
            'SELECT student_id, SUM(m) p FROM (SELECT student_id, MAX(score) m FROM case_submissions WHERE score IS NOT NULL GROUP BY student_id, case_id) t GROUP BY student_id',
        ];
        foreach ($queries as $sql) {
            foreach (DB::all($sql) as $r) {
                $map[$r['student_id']] = ($map[$r['student_id']] ?? 0) + (float)$r['p'];
            }
        }
        return $map;
    }

    public static function points(int $sid): int
    {
        return (int)round(self::pointsMap()[$sid] ?? 0);
    }

    /** Métricas usadas por los logros y el panel del estudiante. */
    public static function counts(int $sid): array
    {
        return [
            'lessons'     => (int)DB::val('SELECT COUNT(*) FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id WHERE lp.student_id=?', [$sid]),
            'exercises'   => (int)DB::val('SELECT COUNT(DISTINCT exercise_id) FROM exercise_attempts WHERE student_id=? AND is_correct=1', [$sid]),
            'attempted'   => (int)DB::val('SELECT COUNT(DISTINCT exercise_id) FROM exercise_attempts WHERE student_id=?', [$sid]),
            'challenges'  => (int)DB::val('SELECT COUNT(DISTINCT challenge_id) FROM challenge_attempts WHERE student_id=? AND is_correct=1', [$sid]),
            'modules'     => (int)DB::val('SELECT COUNT(*) FROM student_progress WHERE student_id=? AND completed_at IS NOT NULL', [$sid]),
            'assessments' => (int)DB::val('SELECT COUNT(DISTINCT assessment_id) FROM assessment_attempts WHERE student_id=? AND passed=1', [$sid]),
            'cases'       => (int)DB::val('SELECT COUNT(DISTINCT case_id) FROM case_submissions WHERE student_id=?', [$sid]),
            'points'      => self::points($sid),
        ];
    }

    /** Puntos ganados por día durante los últimos $days días (primer acierto de cada actividad). */
    public static function pointsByDay(int $sid, int $days = 14): array
    {
        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $rows = [];
        $queries = [
            'SELECT DATE(f) d, SUM(p) s FROM (SELECT MIN(created_at) f, MAX(points) p FROM exercise_attempts WHERE student_id=? AND is_correct=1 GROUP BY exercise_id) t GROUP BY d',
            'SELECT DATE(f) d, SUM(p) s FROM (SELECT MIN(created_at) f, MAX(points) p FROM challenge_attempts WHERE student_id=? AND is_correct=1 GROUP BY challenge_id) t GROUP BY d',
            'SELECT DATE(f) d, SUM(p) s FROM (SELECT MIN(created_at) f, MAX(score) p FROM case_submissions WHERE student_id=? AND score IS NOT NULL GROUP BY case_id) t GROUP BY d',
        ];
        foreach ($queries as $sql) {
            foreach (DB::all($sql, [$sid]) as $r) {
                $rows[$r['d']] = ($rows[$r['d']] ?? 0) + (float)$r['s'];
            }
        }
        $labels = $values = [];
        for ($i = 0; $i < $days; $i++) {
            $d = date('Y-m-d', strtotime("$from +$i days"));
            $labels[] = date('d/m', strtotime($d));
            $values[] = round($rows[$d] ?? 0, 1);
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /** Últimas actividades del estudiante combinando todas las fuentes. */
    public static function recent(int $sid, int $limit = 8): array
    {
        $items = [];
        foreach (DB::all('SELECT l.title, l.id, lp.completed_at t FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id WHERE lp.student_id=? ORDER BY lp.completed_at DESC LIMIT ' . $limit, [$sid]) as $r) {
            $items[] = ['bi-book', 'success', 'Completaste la lección "' . $r['title'] . '"', $r['t'], 'aprender/leccion/' . $r['id']];
        }
        foreach (DB::all('SELECT e.title, e.id, a.is_correct, a.points, a.created_at t FROM exercise_attempts a JOIN exercises e ON e.id=a.exercise_id WHERE a.student_id=? ORDER BY a.id DESC LIMIT ' . $limit, [$sid]) as $r) {
            $items[] = [$r['is_correct'] ? 'bi-check-circle' : 'bi-x-circle', $r['is_correct'] ? 'success' : 'danger',
                ($r['is_correct'] ? 'Acertaste' : 'Fallaste') . ' el ejercicio "' . $r['title'] . '"' . ($r['is_correct'] ? ' (+' . $r['points'] . ' pts)' : ''), $r['t'], 'laboratorio/' . $r['id']];
        }
        foreach (DB::all('SELECT c.title, c.id, a.is_correct, a.points, a.created_at t FROM challenge_attempts a JOIN challenges c ON c.id=a.challenge_id WHERE a.student_id=? ORDER BY a.id DESC LIMIT ' . $limit, [$sid]) as $r) {
            $items[] = ['bi-trophy', $r['is_correct'] ? 'warning' : 'secondary', ($r['is_correct'] ? 'Superaste' : 'Intentaste') . ' el reto "' . $r['title'] . '"', $r['t'], 'retos/' . $r['id']];
        }
        foreach (DB::all('SELECT a.title, a.id, t.percent, t.passed, t.finished_at t FROM assessment_attempts t JOIN assessments a ON a.id=t.assessment_id WHERE t.student_id=? AND t.status<>\'in_progress\' ORDER BY t.id DESC LIMIT ' . $limit, [$sid]) as $r) {
            $items[] = ['bi-clipboard-check', $r['passed'] ? 'success' : 'danger', 'Evaluación "' . $r['title'] . '": ' . round($r['percent']) . '%', $r['t'], 'evaluaciones/' . $r['id']];
        }
        foreach (DB::all('SELECT c.title, c.id, s.created_at t FROM case_submissions s JOIN practical_cases c ON c.id=s.case_id WHERE s.student_id=? ORDER BY s.id DESC LIMIT ' . $limit, [$sid]) as $r) {
            $items[] = ['bi-briefcase', 'primary', 'Entregaste el caso "' . $r['title'] . '"', $r['t'], 'casos/' . $r['id']];
        }
        usort($items, fn($a, $b) => strcmp((string)$b[3], (string)$a[3]));
        return array_slice($items, 0, $limit);
    }
}
