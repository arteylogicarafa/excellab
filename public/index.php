<?php
// Front controller de ExcelLab.
require dirname(__DIR__) . '/app/Core/bootstrap.php';
require ROOT . '/config/routes.php';
Router::dispatch();
