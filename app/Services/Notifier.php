<?php
/** Notificaciones internas. */
class Notifier
{
    public static function send(int $userId, string $title, ?string $message = null, ?string $link = null): void
    {
        DB::insert('notifications', ['user_id' => $userId, 'title' => mb_substr($title, 0, 150), 'message' => $message ? mb_substr($message, 0, 400) : null, 'link' => $link]);
    }

    public static function unread(int $userId): int
    {
        return (int)DB::val('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0', [$userId]);
    }

    public static function latest(int $userId, int $limit = 6): array
    {
        return DB::all('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT ' . (int)$limit, [$userId]);
    }

    /** Avisa a todos los docentes y administradores activos. */
    public static function staff(string $title, ?string $message = null, ?string $link = null): void
    {
        foreach (DB::all("SELECT u.id FROM users u JOIN roles r ON r.id=u.role_id WHERE r.name IN ('admin','docente') AND u.status='active'") as $u) {
            self::send((int)$u['id'], $title, $message, $link);
        }
    }
}
