<?php
class LearnController
{
    public function index(): void
    {
        $sid = Auth::id();
        $mods = Auth::role() === 'estudiante' ? ProgressService::modules($sid)
            : DB::all("SELECT m.*, (SELECT COUNT(*) FROM lessons l WHERE l.module_id=m.id AND l.status='active') lessons_total, 0 lessons_done, NULL certificate_code FROM modules m WHERE m.status='active' ORDER BY m.position");
        render('learn/index', ['mods' => $mods]);
    }

    public function module(int $id): void
    {
        $sid = Auth::id();
        $mod = DB::one("SELECT * FROM modules WHERE id=? AND status='active'", [$id]);
        if (!$mod) abort(404);
        $lessons = DB::all("SELECT l.*, (SELECT COUNT(*) FROM lesson_progress lp WHERE lp.lesson_id=l.id AND lp.student_id=?) AS done
            FROM lessons l WHERE l.module_id=? AND l.status='active' ORDER BY l.position, l.id", [$sid, $id]);
        $done = count(array_filter($lessons, fn($l) => $l['done']));
        $cert = DB::one('SELECT certificate_code FROM student_progress WHERE student_id=? AND module_id=? AND completed_at IS NOT NULL', [$sid, $id]);
        $assessment = DB::one("SELECT id, title FROM assessments WHERE module_id=? AND status='active' ORDER BY id LIMIT 1", [$id]);
        $exCount = (int)DB::val("SELECT COUNT(*) FROM exercises WHERE module_id=? AND status='active'", [$id]);
        render('learn/module', compact('mod', 'lessons', 'done', 'cert', 'assessment', 'exCount'));
    }

    public function lesson(int $id): void
    {
        $sid = Auth::id();
        $lesson = DB::one("SELECT l.*, m.title AS module_title, m.id AS mid FROM lessons l JOIN modules m ON m.id=l.module_id WHERE l.id=? AND l.status='active' AND m.status='active'", [$id]);
        if (!$lesson) abort(404);
        $exercises = Exercise::forLesson($id);
        $siblings = DB::all("SELECT id, title FROM lessons WHERE module_id=? AND status='active' ORDER BY position, id", [$lesson['module_id']]);
        $prev = $next = null;
        foreach ($siblings as $i => $s) {
            if ((int)$s['id'] === $id) {
                $prev = $siblings[$i - 1] ?? null;
                $next = $siblings[$i + 1] ?? null;
            }
        }
        $done = (bool)DB::val('SELECT 1 FROM lesson_progress WHERE student_id=? AND lesson_id=?', [$sid, $id]);
        $position = 1;
        foreach ($siblings as $i => $s) if ((int)$s['id'] === $id) $position = $i + 1;
        render('learn/lesson', [
            'lesson' => $lesson, 'exercises' => $exercises, 'prev' => $prev, 'next' => $next, 'done' => $done,
            'sheet' => json_decode((string)$lesson['example_data'], true), 'position' => $position, 'total' => count($siblings),
        ]);
    }

    public function complete(int $id): void
    {
        $lesson = DB::one("SELECT id, module_id FROM lessons WHERE id=? AND status='active'", [$id]);
        if (!$lesson) abort(404);
        ProgressService::completeLesson(Auth::id(), $id);
        $badges = BadgeService::check(Auth::id());
        flash('success', '¡Lección completada!');
        foreach ($badges as $b) flash('info', 'Nuevo logro: ' . $b['name']);
        $next = DB::one("SELECT id FROM lessons WHERE module_id=? AND status='active' AND (position>(SELECT position FROM lessons WHERE id=?) OR (position=(SELECT position FROM lessons WHERE id=?) AND id>?)) ORDER BY position, id LIMIT 1", [$lesson['module_id'], $id, $id, $id]);
        redirect($next ? 'aprender/leccion/' . $next['id'] : 'aprender/modulo/' . $lesson['module_id']);
    }
}
