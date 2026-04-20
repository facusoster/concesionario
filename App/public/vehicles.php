<?php
// Listado de vehículos

// Cargar configuración centralizada
require_once __DIR__ . '/../config/app.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/Repositories/vehicle_repository.php';
require_once __DIR__ . '/../core/Flash.php';

$repo = new VehicleRepository();
$q = trim($_GET['q'] ?? '');
$sort = trim($_GET['sort'] ?? 'id');
$dir = strtolower(trim($_GET['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = max(1, min(25, (int)($_GET['perPage'] ?? 10)));

if (!in_array($sort, ['id', 'type', 'brand', 'model', 'year', 'price'], true)) {
    $sort = 'id';
}

$totalVehicles = $repo->countByFilters($q);
$totalPages = max(1, (int)ceil($totalVehicles / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}

$vehicles = $repo->getByFilters($q, $sort, $dir, $page, $perPage);

$message = Flash::get('success') ?? Flash::get('info') ?? '';
$error = Flash::get('error') ?? '';

$pageTitle = 'Vehiculos - Concesionario';
$showNav = true;
$contentTemplate = __DIR__ . '/../src/Views/public/vehicles_content.php';
$contentData = [
    'vehicles' => $vehicles,
    'q' => $q,
    'sort' => $sort,
    'dir' => $dir,
    'page' => $page,
    'perPage' => $perPage,
    'totalPages' => $totalPages,
    'totalVehicles' => $totalVehicles,
];

require __DIR__ . '/../src/Views/layout/base.php';