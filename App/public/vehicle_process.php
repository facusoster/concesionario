<?php
// Procesador para crear/editar/eliminar vehículos
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/Repositories/vehicle_repository.php';
require_once __DIR__ . '/../src/Models/Vehicle.php';

$action = $_POST['action'] ?? '';
$repo = new VehicleRepository();

function redirectVehicleFormError(string $message, ?int $id = null): void {
    $url = 'vehicle_form.php?error=' . rawurlencode($message);
    if ($id !== null) {
        $url .= '&id=' . urlencode((string)$id);
    }
    header('Location: ' . $url);
    exit;
}

try {
    if ($action === 'create') {
        $type = trim($_POST['type'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $yearRaw = trim((string)($_POST['year'] ?? ''));
        $year = $yearRaw !== '' ? (int)$yearRaw : null;
        $priceRaw = trim((string)($_POST['price'] ?? ''));
        $price = $priceRaw !== '' ? (float)$priceRaw : null;

        if ($type === '' || $brand === '' || $model === '' || $yearRaw === '' || $priceRaw === '') {
            redirectVehicleFormError('Tipo, marca, modelo, año y precio son obligatorios.');
        }

        if (!ctype_digit($yearRaw) || $year === null || $year < 1886) {
            redirectVehicleFormError('El año debe ser un número entero válido.');
        }

        if (!is_numeric($priceRaw) || $price === null || $price < 0) {
            redirectVehicleFormError('El precio debe ser un número válido mayor o igual a 0.');
        }

        $veh = new Vehicle(['type'=>$type,'brand'=>$brand,'model'=>$model,'year'=>$year,'price'=>$price]);
        $ok = $repo->save($veh);
        if ($ok) {
            header('Location: vehicles.php?message=' . rawurlencode('Vehículo creado correctamente.'));
            exit;
        } else {
            header('Location: vehicle_form.php?error=' . rawurlencode('No se pudo crear el vehículo.'));
            exit;
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $veh = $repo->findById($id);
        if (!$veh) {
            header('Location: vehicles.php?error=' . rawurlencode('Vehículo no encontrado.'));
            exit;
        }

        $type = trim($_POST['type'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $yearRaw = trim((string)($_POST['year'] ?? ''));
        $year = $yearRaw !== '' ? (int)$yearRaw : null;
        $priceRaw = trim((string)($_POST['price'] ?? ''));

        if ($type === '' || $brand === '' || $model === '' || $yearRaw === '' || $priceRaw === '') {
            redirectVehicleFormError('Tipo, marca, modelo, año y precio son obligatorios.', $id);
        }

        if (!ctype_digit($yearRaw) || $year === null || $year < 1886) {
            redirectVehicleFormError('El año debe ser un número entero válido.', $id);
        }

        if (!is_numeric($priceRaw) || (float)$priceRaw < 0) {
            redirectVehicleFormError('El precio debe ser un número válido mayor o igual a 0.', $id);
        }

        $veh->setType($type);
        $veh->setBrand($brand);
        $veh->setModel($model);
        $veh->setYear($year);
        $veh->setPrice((float)$priceRaw);

        $ok = $repo->save($veh);
        if ($ok) {
            header('Location: vehicles.php?message=' . rawurlencode('Vehículo actualizado correctamente.'));
            exit;
        } else {
            header('Location: vehicle_form.php?id=' . urlencode($id) . '&error=' . rawurlencode('No se pudo actualizar el vehículo.'));
            exit;
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $ok = $repo->delete($id);
        if ($ok) {
            header('Location: vehicles.php?message=' . rawurlencode('Vehículo eliminado correctamente.'));
            exit;
        } else {
            header('Location: vehicles.php?error=' . rawurlencode('No se pudo eliminar el vehículo.'));
            exit;
        }
    } else {
        header('Location: vehicles.php');
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: vehicles.php?error=' . rawurlencode('Error interno del sistema.'));
    exit;
}
