<?php

$controller = strtolower($_GET['controller'] ?? '');
$action = strtolower($_GET['action'] ?? 'index');

if ($controller === 'users') {
    require_once __DIR__ . '/../src/Controllers/UserController.php';
    $instance = new UserController();

    if (!method_exists($instance, $action)) {
        http_response_code(404);
        echo 'Accion no encontrada.';
        exit;
    }

    $instance->{$action}();
    exit;
}

header('Location: login.php');
exit;
