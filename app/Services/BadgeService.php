<?php
/** Otorga insignias cuando el estudiante alcanza las metas definidas. */
class BadgeService
{
    public static function check(int $sid): array
    {
        $counts = Stats::counts($sid);
        $awarded = [];
        $pending = DB::all('SELECT a.* FROM achievements a WHERE a.status=\'active\' AND a.id NOT IN (SELECT achievement_id FROM student_achievements WHERE student_id=?)', [$sid]);
        foreach ($pending as $a) {
            $metric = $a['metric'];
            if (($counts[$metric] ?? 0) >= (int)$a['threshold']) {
                try {
                    DB::insert('student_achievements', ['student_id' => $sid, 'achievement_id' => $a['id']]);
                } catch (PDOException $e) {
                    continue; // ya otorgado en una petición simultánea
                }
                Notifier::send($sid, '¡Nuevo logro: ' . $a['name'] . '!', $a['description'], 'logros');
                Activity::log('logro', 'achievements', (int)$a['id'], $a['name'], $sid);
                $awarded[] = ['name' => $a['name'], 'icon' => $a['icon']];
            }
        }
        return $awarded;
    }
}
