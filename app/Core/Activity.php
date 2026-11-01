<?php
/** Registro de actividades relevantes (auditoría e historial). */
class Activity
{
    public static function log(string $action, string $entity = '', ?int $entityId = null, ?string $detail = null, ?int $userId = null): void
    {
        try {
            DB::insert('activity_log', [
                'user_id'   => $userId ?? (Auth::id() ?: null),
                'action'    => $action,
                'entity'    => $entity,
                'entity_id' => $entityId,
                'detail'    => $detail !== null ? mb_substr($detail, 0, 500) : null,
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Throwable $e) {
            error_log('Activity::log ' . $e->getMessage());
        }
    }
}
