<?php
/** Consultas de ejercicios y sus opciones. */
class Exercise
{
    public static function find(int $id, bool $onlyActive = true): ?array
    {
        $ex = DB::one('SELECT e.*, m.title AS module_title FROM exercises e JOIN modules m ON m.id=e.module_id WHERE e.id=?' . ($onlyActive ? " AND e.status='active'" : ''), [$id]);
        return $ex ? self::hydrate($ex) : null;
    }

    public static function forLesson(int $lessonId): array
    {
        return array_map([self::class, 'hydrate'], DB::all("SELECT e.*, '' AS module_title FROM exercises e WHERE e.lesson_id=? AND e.status='active' ORDER BY e.id", [$lessonId]));
    }

    /** Agrega opciones y datos de tabla decodificados. */
    public static function hydrate(array $ex): array
    {
        $ex['options'] = DB::all('SELECT id, label, is_correct FROM exercise_options WHERE exercise_id=? ORDER BY position, id', [$ex['id']]);
        $ex['sheet'] = $ex['data_json'] ? json_decode($ex['data_json'], true) : null;
        return $ex;
    }

    /** Resumen del estudiante por ejercicio: intentos y si lo resolvió. */
    public static function studentStatus(int $sid): array
    {
        $out = [];
        foreach (DB::all('SELECT exercise_id, COUNT(*) attempts, MAX(is_correct) solved, MAX(points) best FROM exercise_attempts WHERE student_id=? GROUP BY exercise_id', [$sid]) as $r) {
            $out[$r['exercise_id']] = $r;
        }
        return $out;
    }
}
