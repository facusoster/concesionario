<?php
// Logout script: destruye la sesión y redirige al login
// public/logout.php

// Cargar configuración centralizada
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/Flash.php';

session_start();

// Limpiar y destruir la sesión si existe
if (session_status() === PHP_SESSION_ACTIVE) {
    // Borra todas las variables de sesión
    $_SESSION = [];

    // Si se usan cookies de sesión, eliminar la cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }

    // Destruir la sesión en el servidor
    session_unset();
    session_destroy();
}

// Redirigir al formulario de login con mensaje
session_start();
Flash::info('Sesión cerrada correctamente.');
header('Location: login.php');
exit;
