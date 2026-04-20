<?php
// Dashboard sencillo con control de acceso por rol
// public/dashboard.php

// Cargar configuración centralizada
require_once __DIR__ . '/../config/app.php';

session_start();

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$name = $_SESSION['user_name'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'employee';

$pageTitle = 'Dashboard - Concesionario';
$showNav = true;
$message = '';
$error = '';
$contentTemplate = __DIR__ . '/../src/Views/public/dashboard_content.php';
$contentData = [
    'name' => $name,
    'role' => $role,
];

require __DIR__ . '/../src/Views/layout/base.php';