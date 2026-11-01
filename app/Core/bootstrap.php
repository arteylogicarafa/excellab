<?php
// Arranque: constantes, autocarga, configuración, sesión segura y manejo de errores.
define('ROOT', dirname(__DIR__, 2));
define('APP', ROOT . '/app');

$GLOBALS['config'] = require ROOT . '/config/config.php';
date_default_timezone_set($GLOBALS['config']['timezone']);

error_reporting(E_ALL);
ini_set('display_errors', $GLOBALS['config']['debug'] ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/logs/php-error.log');

spl_autoload_register(function ($class) {
    foreach (['Core', 'Models', 'Services', 'Controllers', 'Controllers/Admin'] as $dir) {
        $file = APP . "/$dir/$class.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require APP . '/Core/helpers.php';

// Sesión segura
session_name('excellab_sid');
ini_set('session.gc_maxlifetime', '2592000');
session_set_cookie_params([
    'lifetime' => 2592000, // 30 días: el alumno no tiene que volver a escribir su correo
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();

set_exception_handler(function (Throwable $e) {
    error_log($e);
    http_response_code(500);
    if (is_ajax()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => config('debug') ? $e->getMessage() : 'Error interno del servidor.']);
        return;
    }
    $detail = config('debug') ? $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')' : '';
    render('errors/error', ['code' => 500, 'title' => 'Algo salió mal', 'message' => 'Ocurrió un error inesperado. Inténtalo de nuevo.', 'detail' => $detail], 'auth');
});
