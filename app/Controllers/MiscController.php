<?php
class MiscController
{
    public function search(): void
    {
        $q = input('q');
        $results = [];
        if (mb_strlen($q) >= 2) {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $results['Lecciones'] = DB::all("SELECT l.id, l.title, l.description AS extra, m.title AS ctx FROM lessons l JOIN modules m ON m.id=l.module_id WHERE l.status='active' AND m.status='active' AND (l.title ILIKE ? OR l.description ILIKE ? OR l.theory ILIKE ?) LIMIT 15", [$like, $like, $like]);
            $results['Ejercicios'] = DB::all("SELECT e.id, e.title, e.topic AS extra, m.title AS ctx FROM exercises e JOIN modules m ON m.id=e.module_id WHERE e.status='active' AND (e.title ILIKE ? OR e.statement ILIKE ? OR e.topic ILIKE ?) LIMIT 15", [$like, $like, $like]);
            $results['Retos'] = DB::all("SELECT id, title, objective AS extra, difficulty AS ctx FROM challenges WHERE status='active' AND (title ILIKE ? OR situation ILIKE ? OR objective ILIKE ?) LIMIT 10", [$like, $like, $like]);
            $results['Casos prácticos'] = DB::all("SELECT id, title, category AS extra, mode AS ctx FROM practical_cases WHERE status='active' AND (title ILIKE ? OR context ILIKE ?) LIMIT 10", [$like, $like]);
            $results['Evaluaciones'] = DB::all("SELECT id, title, description AS extra, '' AS ctx FROM assessments WHERE status='active' AND (title ILIKE ? OR description ILIKE ?) LIMIT 10", [$like, $like]);
            Activity::log('busqueda', 'search', null, $q);
        }
        render('misc/search', ['q' => $q, 'results' => $results]);
    }

    public function notifications(): void
    {
        $rows = DB::all('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 60', [Auth::id()]);
        render('misc/notifications', ['rows' => $rows]);
    }

    public function readAll(): void
    {
        DB::exec('UPDATE notifications SET is_read=1 WHERE user_id=?', [Auth::id()]);
        redirect('notificaciones');
    }

    public function go(int $id): void
    {
        $n = DB::one('SELECT * FROM notifications WHERE id=? AND user_id=?', [$id, Auth::id()]);
        if (!$n) abort(404);
        DB::exec('UPDATE notifications SET is_read=1 WHERE id=?', [$id]);
        redirect($n['link'] ?: 'notificaciones');
    }

    public function achievements(): void
    {
        $rows = DB::all("SELECT a.*, sa.awarded_at FROM achievements a LEFT JOIN student_achievements sa ON sa.achievement_id=a.id AND sa.student_id=? WHERE a.status='active' ORDER BY (sa.awarded_at IS NULL), a.threshold", [Auth::id()]);
        render('misc/achievements', ['rows' => $rows, 'counts' => Stats::counts(Auth::id())]);
    }

    public function ranking(): void
    {
        $enabled = DB::setting('ranking_enabled', '1') === '1';
        $list = [];
        $me = null;
        if ($enabled) {
            $map = Stats::pointsMap();
            $students = DB::all("SELECT u.id, u.name FROM users u JOIN roles r ON r.id=u.role_id WHERE r.name='estudiante' AND u.status='active'");
            foreach ($students as $s) $list[] = ['id' => (int)$s['id'], 'name' => $s['name'], 'points' => (int)round($map[$s['id']] ?? 0)];
            usort($list, fn($a, $b) => $b['points'] <=> $a['points'] ?: strcmp($a['name'], $b['name']));
            foreach ($list as $i => &$row) {
                $row['pos'] = $i + 1;
                $parts = explode(' ', $row['name']);
                $row['display'] = $parts[0] . (isset($parts[1]) ? ' ' . mb_substr($parts[count($parts) - 1], 0, 1) . '.' : '');
                if ($row['id'] === Auth::id()) $me = $row;
            }
            unset($row);
        }
        render('misc/ranking', ['enabled' => $enabled, 'list' => array_slice($list, 0, 10), 'me' => $me, 'total' => count($list)]);
    }

    public function certificate(int $moduleId): void
    {
        $p = DB::one('SELECT sp.*, m.title AS module_title, u.name AS student_name FROM student_progress sp JOIN modules m ON m.id=sp.module_id JOIN users u ON u.id=sp.student_id WHERE sp.student_id=? AND sp.module_id=? AND sp.completed_at IS NOT NULL', [Auth::id(), $moduleId]);
        if (!$p) {
            flash('warning', 'Debes completar todas las lecciones del módulo para obtener el certificado.');
            redirect('aprender/modulo/' . $moduleId);
        }
        Activity::log('certificado_visto', 'modules', $moduleId);
        render('student/certificate', ['p' => $p], 'print');
    }
}
