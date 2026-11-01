<?php
// Tabla de rutas. Acceso: public | auth | student | staff | admin

// --- Autenticación y cuenta ---
Router::get('/', 'AuthController@root', 'public');
// Estudiantes: entran solo con su correo (sin contraseña ni registro)
Router::get('/login', 'AuthController@loginForm', 'public');
Router::post('/entrar', 'AuthController@enter', 'public');
// Docentes y administradores: correo y contraseña
Router::get('/docentes', 'AuthController@staffForm', 'public');
Router::post('/docentes', 'AuthController@login', 'public');
Router::post('/salir', 'AuthController@logout', 'auth');
Router::get('/recuperar', 'AuthController@forgotForm', 'public');
Router::post('/recuperar', 'AuthController@forgot', 'public');
Router::get('/restablecer/{any}', 'AuthController@resetForm', 'public');
Router::post('/restablecer/{any}', 'AuthController@reset', 'public');
Router::get('/cuenta', 'AuthController@account', 'auth');
Router::get('/cuenta/password', 'AuthController@account', 'auth');
Router::post('/cuenta/perfil', 'AuthController@updateProfile', 'auth');
Router::post('/cuenta/password', 'AuthController@changePassword', 'auth');

// --- Estudiante ---
Router::get('/inicio', 'DashboardController@student', 'student');
Router::get('/logros', 'MiscController@achievements', 'student');
Router::get('/ranking', 'MiscController@ranking', 'student');
Router::get('/certificado/{id}', 'MiscController@certificate', 'student');

Router::get('/aprender', 'LearnController@index', 'auth');
Router::get('/aprender/modulo/{id}', 'LearnController@module', 'auth');
Router::get('/aprender/leccion/{id}', 'LearnController@lesson', 'auth');
Router::post('/aprender/leccion/{id}/completar', 'LearnController@complete', 'student');

Router::get('/laboratorio', 'LabController@index', 'auth');
Router::get('/laboratorio/{id}', 'LabController@show', 'auth');
Router::post('/ejercicios/{id}/comprobar', 'LabController@check', 'auth');

Router::get('/retos', 'ChallengeController@index', 'auth');
Router::get('/retos/{id}', 'ChallengeController@show', 'auth');
Router::post('/retos/{id}/comprobar', 'ChallengeController@check', 'auth');

Router::get('/casos', 'PracticalCaseController@index', 'auth');
Router::get('/casos/{id}', 'PracticalCaseController@show', 'auth');
Router::get('/casos/{id}/plantilla', 'PracticalCaseController@template', 'auth');
Router::post('/casos/{id}/entregar', 'PracticalCaseController@submit', 'student');
Router::get('/archivo/{id}', 'PracticalCaseController@download', 'auth');

Router::get('/evaluaciones', 'AssessmentController@index', 'auth');
Router::get('/evaluaciones/intento/{id}', 'AssessmentController@take', 'student');
Router::post('/evaluaciones/intento/{id}/enviar', 'AssessmentController@finish', 'student');
Router::get('/evaluaciones/resultado/{id}', 'AssessmentController@result', 'auth');
Router::get('/evaluaciones/{id}', 'AssessmentController@show', 'auth');
Router::post('/evaluaciones/{id}/iniciar', 'AssessmentController@start', 'student');

Router::get('/buscar', 'MiscController@search', 'auth');
Router::get('/notificaciones', 'MiscController@notifications', 'auth');
Router::post('/notificaciones/leer', 'MiscController@readAll', 'auth');
Router::get('/notificaciones/ir/{id}', 'MiscController@go', 'auth');

// --- Panel docente ---
Router::get('/admin', 'AdminDashboardController@index', 'staff');
Router::get('/admin/entregas', 'SubmissionsController@index', 'staff');
Router::get('/admin/entregas/{id}', 'SubmissionsController@show', 'staff');
Router::post('/admin/entregas/{id}/calificar', 'SubmissionsController@grade', 'staff');
Router::get('/admin/seguimiento', 'TrackingController@index', 'staff');
Router::get('/admin/seguimiento/{id}', 'TrackingController@student', 'staff');
Router::get('/admin/reportes', 'ReportsController@index', 'staff');
Router::get('/admin/ajustes', 'SettingsController@index', 'admin');
Router::post('/admin/ajustes', 'SettingsController@save', 'admin');

// CRUD genérico: estudiantes, usuarios, modulos, lecciones, ejercicios, retos, casos, evaluaciones, logros
Router::get('/admin/{any}', 'CrudController@index', 'staff');
Router::get('/admin/{any}/nuevo', 'CrudController@form', 'staff');
Router::get('/admin/{any}/{id}/editar', 'CrudController@form', 'staff');
Router::post('/admin/{any}/guardar', 'CrudController@save', 'staff');
Router::post('/admin/{any}/{id}/estado', 'CrudController@status', 'staff');
