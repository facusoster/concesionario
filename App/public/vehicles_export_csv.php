<?php
// Exportación CSV de vehículos (solo administrador)

require_once __DIR__ . '/../config/app.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Flash.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../src/Repositories/vehicle_repository.php';
require_once __DIR__ . '/../src/Exceptions/RepositoryException.php';

$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$sort = trim((string)($_GET['sort'] ?? 'id'));
$dir = strtolower(trim((string)($_GET['dir'] ?? 'desc'))) === 'asc' ? 'asc' : 'desc';

if (!in_array($status, ['', 'disponible', 'vendido'], true)) {
    $status = '';
}

if (!in_array($sort, ['id', 'type', 'brand', 'model', 'year', 'price', 'status'], true)) {
    $sort = 'id';
}

if (!Auth::isAdmin()) {
    Flash::error('No tienes permiso para exportar CSV. Por favor, contacta a un administrador para realizar esta acción.');
    $redirectParams = ['q' => $q, 'status' => $status, 'sort' => $sort, 'dir' => $dir];
    header('Location: vehicles.php?' . http_build_query($redirectParams));
    exit;
}

$repo = new VehicleRepository();

try {
    $vehicles = $repo->getAllByFilters($q, $status, $sort, $dir);

    $filename = 'vehiculos_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'wb');
    if ($output === false) {
        throw new RuntimeException('No se pudo abrir la salida CSV.');
    }

    // BOM UTF-8 para compatibilidad con Excel.
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, ['Id', 'Tipo', 'Marca', 'Modelo', 'Año', 'Precio', 'Estado']);

    foreach ($vehicles as $v) {
        fputcsv($output, [
            $v->getId(),
            $v->getType(),
            $v->getBrand(),
            $v->getModel(),
            $v->getYear(),
            number_format((float)$v->getPrice(), 2, '.', ''),
            $v->getStatus(),
        ]);
    }

    fclose($output);
    exit;
} catch (RepositoryException $e) {
    Logger::error('Error de repositorio al exportar CSV de vehículos', [
        'q' => $q,
        'status' => $status,
        'sort' => $sort,
        'dir' => $dir,
        'exception' => $e->getMessage(),
    ]);
    Flash::error('No se pudo generar el archivo CSV.');
    $redirectParams = ['q' => $q, 'status' => $status, 'sort' => $sort, 'dir' => $dir];
    header('Location: vehicles.php?' . http_build_query($redirectParams));
    exit;
} catch (Throwable $e) {
    Logger::error('Error interno al exportar CSV de vehículos', [
        'q' => $q,
        'status' => $status,
        'sort' => $sort,
        'dir' => $dir,
        'exception' => $e->getMessage(),
    ]);
    Flash::error('Error interno al exportar CSV.');
    $redirectParams = ['q' => $q, 'status' => $status, 'sort' => $sort, 'dir' => $dir];
    header('Location: vehicles.php?' . http_build_query($redirectParams));
    exit;
}
