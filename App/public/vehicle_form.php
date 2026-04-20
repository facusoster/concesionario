<?php
// Formulario para crear/editar vehículos

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
$vehicle = null;
$editing = false;

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $vehicle = $repo->findById($id);
    if ($vehicle) $editing = true;
}

// valores por defecto
$type = $vehicle ? $vehicle->getType() : '';
$brand = $vehicle ? $vehicle->getBrand() : '';
$model = $vehicle ? $vehicle->getModel() : '';
$year = $vehicle ? $vehicle->getYear() : '';
$price = $vehicle ? $vehicle->getPrice() : '';
$status = $vehicle ? $vehicle->getStatus() : 'disponible';

$oldVehicleForm = $_SESSION['old_vehicle_form'] ?? [];
unset($_SESSION['old_vehicle_form']);

if (!empty($oldVehicleForm)) {
    $type = $oldVehicleForm['type'] ?? $type;
    $brand = $oldVehicleForm['brand'] ?? $brand;
    $model = $oldVehicleForm['model'] ?? $model;
    $year = $oldVehicleForm['year'] ?? $year;
    $price = $oldVehicleForm['price'] ?? $price;
    $status = $oldVehicleForm['status'] ?? $status;
}

$error = Flash::get('error') ?? '';

$pageTitle = ($editing ? 'Editar vehiculo' : 'Agregar vehiculo') . ' - Concesionario';
$showNav = true;
$contentTemplate = __DIR__ . '/../src/Views/public/vehicle_form_content.php';
$contentData = [
    'editing' => $editing,
    'vehicle' => $vehicle,
    'type' => $type,
    'brand' => $brand,
    'model' => $model,
    'year' => $year,
    'price' => $price,
    'status' => $status,
];

require __DIR__ . '/../src/Views/layout/base.php';