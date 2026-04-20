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
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Concesionario</title>
    <style>body{font-family:Arial, sans-serif;margin:30px}</style>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($name); ?></h1>
    <p>Rol: <strong><?php echo htmlspecialchars($role); ?></strong></p>

    <ul>
        <li><a href="vehicles.php">Gestionar Vehículos</a> <!-- disponible para ambos roles --></li>
        <?php if ($role === 'admin'): ?>
            <li><a href="index.php?controller=users&amp;action=index">Gestionar Usuarios (Admin)</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>

    <p style="color:#666;font-size:0.9em">Nota: los empleados deben ser dirigidos a la sección de gestión de vehículos; sólo los administradores pueden gestionar usuarios.</p>
</body>
</html>