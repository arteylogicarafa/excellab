<?php
// Funciones auxiliares globales.

function config(string $key, $default = null)
{
    $v = $GLOBALS['config'];
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) {
            return $default;
        }
        $v = $v[$k];
    }
    return $v;
}

/** Escapa HTML (protección XSS). */
function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function script_dir(): string
{
    $d = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    return rtrim($d, '/');
}

function base_url(): string
{
    return preg_replace('#/public$#', '', script_dir());
}

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return script_dir() . '/assets/' . ltrim($path, '/');
}

/** Logo de la marca: coloca el archivo en public/assets/img/logo.png (también .webp, .jpg o .svg). */
function logo_url(): ?string
{
    foreach (['png', 'webp', 'jpg', 'jpeg', 'svg'] as $ext) {
        if (is_file(ROOT . '/public/assets/img/logo.' . $ext)) {
            return asset('img/logo.' . $ext);
        }
    }
    return null;
}

function redirect(string $path): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

function is_ajax(): bool
{
    return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function json_out($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// --- CSRF ---
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $t = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($t) && hash_equals(csrf_token(), $t);
}

// --- Mensajes flash y datos antiguos de formulario ---
function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function remember_input(array $data): void
{
    unset($data['_csrf'], $data['password'], $data['password2'], $data['current_password']);
    $_SESSION['old'] = $data;
}

function old(string $key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

// --- Entrada ---
function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_array($v) ? $default : trim((string)$v);
}

function input_int(string $key, int $default = 0): int
{
    $v = input($key, (string)$default);
    return is_numeric($v) ? (int)$v : $default;
}

// --- Vistas ---
// Los parámetros llevan prefijo "__" para no chocar con variables de las vistas (p. ej. $data en sheet.php).
function render(string $__view, array $__vars = [], ?string $__layout = 'app'): void
{
    extract($__vars);
    ob_start();
    require APP . "/Views/$__view.php";
    $content = ob_get_clean();
    if ($__layout) {
        require APP . "/Views/layouts/$__layout.php";
    } else {
        echo $content;
    }
}

function partial(string $__name, array $__vars = []): void
{
    extract($__vars);
    require APP . "/Views/components/$__name.php";
}

function abort(int $code, string $message = ''): void
{
    // Apache no reconoce el código 419: se responde 403 conservando el mensaje de formulario expirado
    $status = $code === 419 ? 403 : $code;
    http_response_code($status);
    $titles = [403 => 'Acceso restringido', 404 => 'Página no encontrada', 419 => 'Sesión expirada'];
    $defaults = [
        403 => 'No tienes permisos para ver esta sección.',
        404 => 'La página que buscas no existe o fue movida.',
        419 => 'El formulario expiró. Vuelve atrás, recarga la página e inténtalo de nuevo.',
    ];
    if (is_ajax()) {
        json_out(['ok' => false, 'message' => $message ?: ($defaults[$code] ?? 'Error')], $status);
    }
    render('errors/error', [
        'code' => $code, 'title' => $titles[$code] ?? 'Error',
        'message' => $message ?: ($defaults[$code] ?? 'Error'), 'detail' => '',
    ], Auth::check() ? 'app' : 'auth');
    exit;
}

// --- Formato ---
function fmt_date(?string $d, bool $time = true): string
{
    if (!$d) {
        return '—';
    }
    $t = strtotime($d);
    return date($time ? 'd/m/Y H:i' : 'd/m/Y', $t);
}

function time_ago(?string $d): string
{
    if (!$d) {
        return '';
    }
    $diff = time() - strtotime($d);
    if ($diff < 60) return 'hace un momento';
    if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'hace ' . floor($diff / 3600) . ' h';
    if ($diff < 86400 * 7) return 'hace ' . floor($diff / 86400) . ' d';
    return fmt_date($d, false);
}

function diff_label(string $d): string
{
    return ['basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado'][$d] ?? $d;
}

function diff_class(string $d): string
{
    return ['basico' => 'success', 'intermedio' => 'warning', 'avanzado' => 'danger'][$d] ?? 'secondary';
}

function type_label(string $t): string
{
    return [
        'multiple_choice' => 'Selección múltiple',
        'true_false'      => 'Verdadero o falso',
        'complete_formula' => 'Completar fórmula',
        'identify_function' => 'Identificar función',
        'solve_operation' => 'Resolver operación',
        'analyze_table'   => 'Analizar tabla',
        'practical'       => 'Situación práctica',
    ][$t] ?? $t;
}

function status_badge(string $s): string
{
    $map = ['active' => ['success', 'Activo'], 'inactive' => ['secondary', 'Inactivo'], 'archived' => ['dark', 'Archivado'],
            'submitted' => ['warning', 'Pendiente de revisión'], 'reviewed' => ['success', 'Revisado']];
    [$c, $l] = $map[$s] ?? ['secondary', $s];
    return '<span class="badge text-bg-' . $c . '">' . e($l) . '</span>';
}

function col_letter(int $i): string
{
    $s = '';
    $i++;
    while ($i > 0) {
        $m = ($i - 1) % 26;
        $s = chr(65 + $m) . $s;
        $i = intdiv($i - $m - 1, 26);
    }
    return $s;
}

/** Limpia HTML de contenido docente: solo etiquetas seguras y sin atributos de eventos. */
function clean_html(string $html): string
{
    $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><h4><h5><h6><code><pre><kbd><table><thead><tbody><tr><th><td><span><div><img><a><blockquote>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*(javascript|data|vbscript):[^"\']*\2/i', '$1=$2#$2', $html);
    return $html;
}
