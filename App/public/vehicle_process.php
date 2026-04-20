<?php
// Procesador para crear/editar/eliminar vehículos

// Cargar configuración centralizada
require_once __DIR__ . '/../config/app.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/Repositories/vehicle_repository.php';
require_once __DIR__ . '/../src/Models/Vehicle.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Flash.php';
require_once __DIR__ . '/../src/Exceptions/ValidationException.php';
require_once __DIR__ . '/../src/Exceptions/RepositoryException.php';
require_once __DIR__ . '/../src/Exceptions/AuthException.php';

$action = $_POST['action'] ?? '';
$repo = new VehicleRepository();

function redirectVehicleFormError(string $message, ?int $id = null): void {
    Flash::error($message);
    $url = 'vehicle_form.php';
    if ($id !== null) {
        $url .= '?id=' . urlencode((string)$id);
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
            throw new ValidationException('Tipo, marca, modelo, año y precio son obligatorios.');
        }

        if (!ctype_digit($yearRaw) || $year === null || $year < 1886) {
            throw new ValidationException('El año debe ser un número entero válido.');
        }

        if (!is_numeric($priceRaw) || $price === null || $price < 0) {
            throw new ValidationException('El precio debe ser un número válido mayor o igual a 0.');
        }

        $veh = new Vehicle(['type'=>$type,'brand'=>$brand,'model'=>$model,'year'=>$year,'price'=>$price]);
        $repo->save($veh);
        Flash::success('Vehículo creado correctamente.');
        header('Location: vehicles.php');
        exit;
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $veh = $repo->findById($id);
        if (!$veh) {
            throw new ValidationException('Vehículo no encontrado.');
        }

        $type = trim($_POST['type'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $yearRaw = trim((string)($_POST['year'] ?? ''));
        $year = $yearRaw !== '' ? (int)$yearRaw : null;
        $priceRaw = trim((string)($_POST['price'] ?? ''));

        if ($type === '' || $brand === '' || $model === '' || $yearRaw === '' || $priceRaw === '') {
            throw new ValidationException('Tipo, marca, modelo, año y precio son obligatorios.');
        }

        if (!ctype_digit($yearRaw) || $year === null || $year < 1886) {
            throw new ValidationException('El año debe ser un número entero válido.');
        }

        if (!is_numeric($priceRaw) || (float)$priceRaw < 0) {
            throw new ValidationException('El precio debe ser un número válido mayor o igual a 0.');
        }

        $veh->setType($type);
        $veh->setBrand($brand);
        $veh->setModel($model);
        $veh->setYear($year);
        $veh->setPrice((float)$priceRaw);

        $repo->save($veh);
        Flash::success('Vehículo actualizado correctamente.');
        header('Location: vehicles.php');
        exit;
    } elseif ($action === 'delete') {
        Auth::requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $repo->delete($id);
        Flash::success('Vehículo eliminado correctamente.');
        header('Location: vehicles.php');
        exit;
    } else {
        header('Location: vehicles.php');
        exit;
    }
} catch (AuthException $e) {
    Logger::error('Intento de eliminar vehículo sin permisos de admin', [
        'user_id' => Auth::userId(),
        'action' => $action,
    ]);
    if ($action === 'delete') {
        Flash::error('No tienes permiso para eliminar vehículos.');
        header('Location: vehicles.php');
        exit;
    }
    Flash::error('Acceso denegado.');
    header('Location: vehicles.php');
    exit;
} catch (ValidationException $e) {
    if ($action === 'create' || $action === 'update') {
        $_SESSION['old_vehicle_form'] = [
            'type' => trim($_POST['type'] ?? ''),
            'brand' => trim($_POST['brand'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'year' => trim((string)($_POST['year'] ?? '')),
            'price' => trim((string)($_POST['price'] ?? '')),
        ];
    }
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        redirectVehicleFormError($e->getMessage(), $id > 0 ? $id : null);
    }
    if ($action === 'create') {
        redirectVehicleFormError($e->getMessage());
    }
    Flash::error($e->getMessage());
    header('Location: vehicles.php');
    exit;
} catch (RepositoryException $e) {
    if ($action === 'create' || $action === 'update') {
        $_SESSION['old_vehicle_form'] = [
            'type' => trim($_POST['type'] ?? ''),
            'brand' => trim($_POST['brand'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'year' => trim((string)($_POST['year'] ?? '')),
            'price' => trim((string)($_POST['price'] ?? '')),
        ];
    }
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        redirectVehicleFormError('No se pudo procesar la operación en base de datos.', $id > 0 ? $id : null);
    }
    if ($action === 'create') {
        redirectVehicleFormError('No se pudo procesar la operación en base de datos.');
    }
    Flash::error('No se pudo procesar la operación en base de datos.');
    header('Location: vehicles.php');
    exit;
} catch (Exception $e) {
    Logger::error('Error no controlado en vehicle_process.php', [
        'exception' => $e->getMessage(),
        'action' => $action,
    ]);
    Flash::error('Error interno del sistema.');
    header('Location: vehicles.php');
    exit;
} finally {
    // Punto de extensión para trazabilidad adicional si se requiere.
}
