<?php
/** Autenticación y sesión de usuario. */
class Auth
{
    public static function check(): bool
    {
        if (empty($_SESSION['user'])) {
            return false;
        }
        $idle = config('session_idle_min') * 60;
        if (time() - ($_SESSION['last_activity'] ?? time()) > $idle) {
            self::logout(true);
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): int
    {
        return (int)($_SESSION['user']['id'] ?? 0);
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isStaff(): bool
    {
        return in_array(self::role(), ['admin', 'docente'], true);
    }

    public static function home(): string
    {
        return self::isStaff() ? 'admin' : 'inicio';
    }

    /** @return array{0:bool,1:string} */
    public static function attempt(string $email, string $password): array
    {
        $u = DB::one('SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=?', [strtolower($email)]);
        if (!$u) {
            password_verify($password, '$2y$10$abcdefghijklmnopqrstuuAbCdEfGhIjKlMnOpQrStUvWxYz012345'); // tiempo constante
            return [false, 'Correo o contraseña incorrectos.'];
        }
        if ($u['locked_until'] && strtotime($u['locked_until']) > time()) {
            $min = ceil((strtotime($u['locked_until']) - time()) / 60);
            return [false, "Cuenta bloqueada temporalmente por intentos fallidos. Inténtalo en $min min."];
        }
        if (!password_verify($password, $u['password_hash'])) {
            $fails = (int)$u['failed_attempts'] + 1;
            $lock = null;
            if ($fails >= config('max_failed_logins')) {
                $lock = date('Y-m-d H:i:s', time() + config('lock_minutes') * 60);
                $fails = 0;
            }
            DB::exec('UPDATE users SET failed_attempts=?, locked_until=?, updated_at=NOW() WHERE id=?', [$fails, $lock, $u['id']]);
            Activity::log('login_fallido', 'users', (int)$u['id'], $email, (int)$u['id']);
            return [false, 'Correo o contraseña incorrectos.'];
        }
        if ($u['status'] !== 'active') {
            return [false, 'Tu cuenta está deshabilitada. Contacta con tu docente.'];
        }
        self::login($u);
        return [true, ''];
    }

    public static function login(array $u): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$u['id'], 'name' => $u['name'], 'email' => $u['email'],
            'role' => $u['role'], 'must_change' => (bool)$u['must_change_password'],
        ];
        $_SESSION['last_activity'] = time();
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        DB::exec('UPDATE users SET failed_attempts=0, locked_until=NULL, last_login_at=NOW(), updated_at=NOW() WHERE id=?', [$u['id']]);
        Activity::log('inicio_sesion', 'users', (int)$u['id'], null, (int)$u['id']);
    }

    public static function logout(bool $expired = false): void
    {
        if (!empty($_SESSION['user'])) {
            Activity::log('cierre_sesion', 'users', self::id());
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        if ($expired) {
            flash('warning', 'Tu sesión expiró por inactividad.');
        }
    }

    public static function clearMustChange(): void
    {
        $_SESSION['user']['must_change'] = false;
    }
}
