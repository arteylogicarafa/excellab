<?php
/** Avance por módulo, avance general y certificados. */
class ProgressService
{
    public static function completeLesson(int $sid, int $lessonId): void
    {
        $lesson = DB::one('SELECT id, module_id, title FROM lessons WHERE id=?', [$lessonId]);
        if (!$lesson) {
            return;
        }
        DB::exec('INSERT INTO lesson_progress (student_id, lesson_id) VALUES (?, ?) ON CONFLICT DO NOTHING', [$sid, $lessonId]);
        Activity::log('leccion_completada', 'lessons', $lessonId, $lesson['title']);
        self::recompute($sid, (int)$lesson['module_id']);
    }

    public static function recompute(int $sid, int $moduleId): void
    {
        $total = (int)DB::val("SELECT COUNT(*) FROM lessons WHERE module_id=? AND status='active'", [$moduleId]);
        $done = (int)DB::val("SELECT COUNT(*) FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id WHERE lp.student_id=? AND l.module_id=? AND l.status='active'", [$sid, $moduleId]);
        $percent = $total ? round($done / $total * 100, 2) : 0;
        $row = DB::one('SELECT * FROM student_progress WHERE student_id=? AND module_id=?', [$sid, $moduleId]);
        $completed = $total > 0 && $done >= $total;
        $completedAt = $row['completed_at'] ?? null;
        $code = $row['certificate_code'] ?? null;
        if ($completed && !$completedAt) {
            $completedAt = date('Y-m-d H:i:s');
            $code = sprintf('XL-%02d-%03d-%s', $moduleId, $sid, strtoupper(substr(bin2hex(random_bytes(4)), 0, 5)));
            $mod = DB::val('SELECT title FROM modules WHERE id=?', [$moduleId]);
            Notifier::send($sid, '¡Módulo completado!', 'Terminaste "' . $mod . '". Ya puedes descargar tu certificado.', 'certificado/' . $moduleId);
            Activity::log('modulo_completado', 'modules', $moduleId, $mod);
        } elseif (!$completed) {
            $completedAt = null; // se agregaron lecciones nuevas: el módulo vuelve a estar en curso
        }
        DB::q('INSERT INTO student_progress (student_id, module_id, lessons_total, lessons_done, percent, completed_at, certificate_code)
               VALUES (?,?,?,?,?,?,?) ON CONFLICT (student_id, module_id) DO UPDATE
               SET lessons_total=EXCLUDED.lessons_total, lessons_done=EXCLUDED.lessons_done,
               percent=EXCLUDED.percent, completed_at=EXCLUDED.completed_at,
               certificate_code=COALESCE(student_progress.certificate_code, EXCLUDED.certificate_code),
               updated_at=NOW()',
            [$sid, $moduleId, $total, $done, $percent, $completedAt, $code]);
    }

    /** Módulos activos con el avance del estudiante. */
    public static function modules(int $sid): array
    {
        return DB::all("SELECT m.*,
                (SELECT COUNT(*) FROM lessons l WHERE l.module_id=m.id AND l.status='active') AS lessons_total,
                (SELECT COUNT(*) FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id WHERE lp.student_id=? AND l.module_id=m.id AND l.status='active') AS lessons_done,
                (SELECT certificate_code FROM student_progress sp WHERE sp.student_id=? AND sp.module_id=m.id AND sp.completed_at IS NOT NULL) AS certificate_code
            FROM modules m WHERE m.status='active' ORDER BY m.position, m.id", [$sid, $sid]);
    }

    public static function overall(int $sid): float
    {
        $total = (int)DB::val("SELECT COUNT(*) FROM lessons l JOIN modules m ON m.id=l.module_id WHERE l.status='active' AND m.status='active'");
        if (!$total) {
            return 0;
        }
        $done = (int)DB::val("SELECT COUNT(*) FROM lesson_progress lp JOIN lessons l ON l.id=lp.lesson_id JOIN modules m ON m.id=l.module_id WHERE lp.student_id=? AND l.status='active' AND m.status='active'", [$sid]);
        return round($done / $total * 100, 1);
    }

    /** Primera lección pendiente (para el botón "Continuar aprendiendo"). */
    public static function nextLesson(int $sid): ?array
    {
        return DB::one("SELECT l.id, l.title, m.title AS module_title FROM lessons l JOIN modules m ON m.id=l.module_id
            WHERE l.status='active' AND m.status='active' AND l.id NOT IN (SELECT lesson_id FROM lesson_progress WHERE student_id=?)
            ORDER BY m.position, m.id, l.position, l.id LIMIT 1", [$sid]);
    }
}
