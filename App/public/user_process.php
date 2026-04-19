<?php

// Wrapper de compatibilidad para procesar alta/edición/eliminación de usuarios.
$_GET['controller'] = 'users';

$allowed = ['store', 'update', 'delete'];
$requestedAction = strtolower(trim($_POST['_action'] ?? 'store'));
$_GET['action'] = in_array($requestedAction, $allowed, true) ? $requestedAction : 'store';

require __DIR__ . '/index.php';
