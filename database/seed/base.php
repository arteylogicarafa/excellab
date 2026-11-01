<?php
// Roles, usuarios de demostración, módulos y ajustes.
ins('roles', ['name' => 'admin', 'label' => 'Administrador']);
ins('roles', ['name' => 'docente', 'label' => 'Docente']);
ins('roles', ['name' => 'estudiante', 'label' => 'Estudiante']);

// Todos arrancan con must_change_password = 1 (se exige cambiar la clave en el primer acceso)
$users = [
    [1, 'Administrador ExcelLab', 'admin@excellab.test', 'admin'],
    [2, 'Prof. Mariana Rojas', 'docente@excellab.test', 'docente'],
    [3, 'Ana Pérez', 'ana.perez@excellab.test', 'estudiante'],
    [3, 'Luis Martínez', 'luis.martinez@excellab.test', 'estudiante'],
    [3, 'Marta Gómez', 'marta.gomez@excellab.test', 'estudiante'],
    [3, 'Pedro Sánchez', 'pedro.sanchez@excellab.test', 'estudiante'],
    [3, 'Sofía Ramírez', 'sofia.ramirez@excellab.test', 'estudiante'],
    [3, 'Juan Torres', 'juan.torres@excellab.test', 'estudiante'],
];
foreach ($users as $i => [$role, $name, $email, $key]) {
    ins('users', [
        'role_id' => $role, 'name' => $name, 'email' => $email, 'password_hash' => $HASHES[$key],
        'status' => 'active', 'must_change_password' => $key === 'estudiante' ? 0 : 1, 'failed_attempts' => 0, 'locked_until' => null,
        'last_login_at' => null,
    ]);
}

$modules = [
    ['Introducción a Excel', 'Conoce qué es Excel, su entorno de trabajo y los conceptos básicos: libros, hojas, celdas, tipos de datos y fórmulas.', 'bi-lightbulb'],
    ['Formato y organización', 'Aprende a presentar la información con formato de celdas, números, bordes, colores y tablas.', 'bi-palette'],
    ['Operaciones y fórmulas básicas', 'Domina las cuatro operaciones, las referencias relativas y absolutas y la jerarquía de operadores.', 'bi-plus-slash-minus'],
    ['Funciones básicas', 'Usa las funciones fundamentales: SUMA, PROMEDIO, MAX, MIN, CONTAR y CONTARA.', 'bi-calculator'],
    ['Funciones condicionales', 'Toma decisiones con SI y calcula con criterios usando CONTAR.SI, SUMAR.SI y PROMEDIO.SI.', 'bi-signpost-split'],
    ['Organización de datos', 'Ordena, filtra y busca información; controla lo que se escribe con validación y resalta con formato condicional.', 'bi-funnel'],
    ['Gráficos', 'Representa los datos con gráficos de barras, circulares y de líneas, e interpreta los resultados.', 'bi-bar-chart-line'],
    ['Aplicaciones prácticas', 'Aplica todo lo aprendido en situaciones reales: ventas, inventario, calificaciones, gastos y trabajadores.', 'bi-briefcase'],
];
foreach ($modules as $i => [$title, $desc, $icon]) {
    ins('modules', ['title' => $title, 'description' => $desc, 'icon' => $icon, 'position' => $i + 1, 'status' => 'active']);
}

ins('settings', ['skey' => 'ranking_enabled', 'svalue' => '1']);
