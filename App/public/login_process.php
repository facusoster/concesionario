<?php
// Procesador de login
// public/login_process.php

session_start();

require_once __DIR__ . '/../src/Repositories/user_repository.php';
require_once __DIR__ . '/../src/Models/user.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $msg = 'Email y password son requeridos.';
    header('Location: login.php?error=' . rawurlencode($msg));
    exit;
}

$repo = new UserRepository();
try {
    $user = $repo->findByEmail($email);
    if ($user === null) {
        $msg = 'Usuario no encontrado.';
        header('Location: login.php?error=' . rawurlencode($msg));
        exit;
    }

    $hashed = $user->getHashedPassword();
    if ($hashed === null || !password_verify($password, $hashed)) {
        $msg = 'Credenciales incorrectas.';
        header('Location: login.php?error=' . rawurlencode($msg));
        exit;
    }

    // Autenticación exitosa: almacenar datos en la sesión
    $_SESSION['user_id'] = $user->getId();
    $_SESSION['user_name'] = $user->getName();
    $_SESSION['role'] = $user->getRole();

    // Redirigir a dashboard (crear este archivo luego)
    header('Location: dashboard.php');
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    $msg = 'Error interno, vea logs.';
    header('Location: login.php?error=' . rawurlencode($msg));
    exit;
}
