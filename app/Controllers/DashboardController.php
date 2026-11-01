<?php
class DashboardController
{
    public function student(): void
    {
        $sid = Auth::id();
        $mods = ProgressService::modules($sid);
        $counts = Stats::counts($sid);
        $pendingChallenges = DB::all("SELECT c.id, c.title, c.difficulty, c.points, c.est_minutes FROM challenges c
            WHERE c.status='active' AND c.id NOT IN (SELECT challenge_id FROM challenge_attempts WHERE student_id=? AND is_correct=1)
            ORDER BY CASE c.difficulty WHEN 'basico' THEN 1 WHEN 'intermedio' THEN 2 WHEN 'avanzado' THEN 3 END, c.position LIMIT 4", [$sid]);
        $pendingTotal = (int)DB::val("SELECT COUNT(*) FROM challenges c WHERE c.status='active' AND c.id NOT IN (SELECT challenge_id FROM challenge_attempts WHERE student_id=? AND is_correct=1)", [$sid]);
        $achievements = DB::all('SELECT a.*, sa.awarded_at FROM student_achievements sa JOIN achievements a ON a.id=sa.achievement_id WHERE sa.student_id=? ORDER BY sa.awarded_at DESC LIMIT 6', [$sid]);
        render('student/dashboard', [
            'mods'        => $mods,
            'overall'     => ProgressService::overall($sid),
            'modsDone'    => count(array_filter($mods, fn($m) => $m['lessons_total'] > 0 && $m['lessons_done'] >= $m['lessons_total'])),
            'counts'      => $counts,
            'pending'     => $pendingChallenges,
            'pendingTotal' => $pendingTotal,
            'recent'      => Stats::recent($sid, 7),
            'achievements' => $achievements,
            'next'        => ProgressService::nextLesson($sid),
            'chartPoints' => Stats::pointsByDay($sid, 14),
            'pendingSubs' => (int)DB::val("SELECT COUNT(*) FROM case_submissions WHERE student_id=? AND status='submitted'", [$sid]),
        ]);
    }
}
