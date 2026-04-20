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

require_once __DIR__ . '/../src/Repositories/vehicle_repository.php';

$name = $_SESSION['user_name'] ?? 'Usuario';
$role = $_SESSION['role'] ?? 'employee';

$q = trim((string)($_GET['q'] ?? ''));
$sort = trim((string)($_GET['sort'] ?? 'id'));
$dir = strtolower(trim((string)($_GET['dir'] ?? 'desc'))) === 'asc' ? 'asc' : 'desc';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = max(1, min(12, (int)($_GET['perPage'] ?? 6)));

if (!in_array($sort, ['id', 'type', 'brand', 'model', 'year', 'price', 'status'], true)) {
    $sort = 'id';
}

$vehicleRepo = new VehicleRepository();
$availableVehicles = $vehicleRepo->countByFilters('', 'disponible');
$totalFilteredAvailable = $vehicleRepo->countByFilters($q, 'disponible');
$totalPages = max(1, (int)ceil($totalFilteredAvailable / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}

$saleVehicles = $vehicleRepo->getByFilters($q, 'disponible', $sort, $dir, $page, $perPage);

$pageTitle = 'Dashboard - Concesionario';
$showNav = true;
$message = '';
$error = '';
$contentTemplate = __DIR__ . '/../src/Views/public/dashboard_content.php';
$contentData = [
    'name' => $name,
    'role' => $role,
    'availableVehicles' => $availableVehicles,
    'saleVehicles' => $saleVehicles,
    'q' => $q,
    'sort' => $sort,
    'dir' => $dir,
    'page' => $page,
    'perPage' => $perPage,
    'totalPages' => $totalPages,
    'totalFilteredAvailable' => $totalFilteredAvailable,
];

require __DIR__ . '/../src/Views/layout/base.php';