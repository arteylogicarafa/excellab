<?php
// Configuración general de ExcelLab.
$dbUrl = getenv('DATABASE_URL');
if ($dbUrl === false || $dbUrl === '') {
    $dbUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? '';
}
return [
    'app_name' => 'ExcelLab',
    'debug'    => true,              // En producción: false
    'timezone' => 'America/Caracas',
    // Conexión a PostgreSQL (Neon) leída de la variable de entorno DATABASE_URL.
    // No se guardan claves en este archivo.
    'db' => [
        'url' => $dbUrl,
    ],
    'upload' => [
        'max_mb'     => 5,
        'extensions' => ['xlsx', 'xls', 'csv'],
        'mimes'      => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/zip',
            'application/octet-stream',
            'application/x-ole-storage',
            'application/CDFV2',
            'text/csv',
            'text/plain',
            'application/csv',
        ],
    ],
    // Sin servidor de correo (XAMPP): el enlace de recuperación se muestra en pantalla
    // y se guarda en storage/logs/mail.log. Poner en false al configurar un SMTP real.
    'mail_dev'         => true,
    // Muestra las credenciales de demostración en la pantalla de acceso. Desactivar en producción.
    'show_demo_credentials' => true,
    'session_idle_min' => 720,
    'max_failed_logins' => 5,
    'lock_minutes'      => 10,
];
