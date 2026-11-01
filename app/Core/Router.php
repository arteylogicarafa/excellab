<?php
/**
 * Router mínimo. Niveles de acceso: public | auth | student | staff | admin.
 * Placeholders: {id} (dígitos) y {any} (texto sin barras).
 */
class Router
{
    private static array $routes = [];

    public static function get(string $p, string $h, string $access = 'auth'): void
    {
        self::$routes[] = ['GET', $p, $h, $access];
    }

    public static function post(string $p, string $h, string $access = 'auth'): void
    {
        self::$routes[] = ['POST', $p, $h, $access];
    }

    public static function path(): string
    {
        $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $dir = script_dir();
        $base = base_url();
        if ($dir !== '' && str_starts_with($path, $dir)) {
            $path = substr($path, strlen($dir));
        } elseif ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');
        return $path === '/index.php' ? '/' : $path;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
        $path = self::path();

        foreach (self::$routes as [$m, $pattern, $handler, $access]) {
            if ($m !== $method) {
                continue;
            }
            $regex = '#^' . str_replace(['{id}', '{any}'], ['(\d+)', '([^/]+)'], $pattern) . '$#';
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            array_shift($matches);
            self::guard($access, $path);
            if ($method === 'POST' && !csrf_valid()) {
                abort(419);
            }
            [$class, $action] = explode('@', $handler);
            (new $class())->$action(...$matches);
            return;
        }
        abort(404);
    }

    private static function guard(string $access, string $path): void
    {
        if ($access === 'public') {
            return;
        }
        if (!Auth::check()) {
            if (is_ajax()) {
                json_out(['ok' => false, 'message' => 'Tu sesión expiró. Inicia sesión de nuevo.'], 401);
            }
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '';
            redirect('login');
        }
        // Cambio de contraseña obligatorio
        $u = Auth::user();
        if (!empty($u['must_change']) && !in_array($path, ['/cuenta/password', '/salir'], true)) {
            flash('warning', 'Por seguridad debes cambiar tu contraseña inicial antes de continuar.');
            redirect('cuenta/password');
        }
        $role = $u['role'];
        $ok = match ($access) {
            'auth'    => true,
            'student' => $role === 'estudiante',
            'staff'   => in_array($role, ['admin', 'docente'], true),
            'admin'   => $role === 'admin',
            default   => false,
        };
        if (!$ok) {
            abort(403);
        }
    }
}
