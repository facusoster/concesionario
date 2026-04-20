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
$typeFilter = trim($_GET['type'] ?? '');
$brandFilter = trim($_GET['brand'] ?? '');
$modelFilter = trim($_GET['model'] ?? '');

$vehicles = $repo->getByFilters($typeFilter, $brandFilter, $modelFilter);

$message = Flash::get('success') ?? Flash::get('info') ?? '';
$error = Flash::get('error') ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vehículos - Concesionario</title>
    <style>body{font-family:Arial, sans-serif;margin:30px}table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #ddd}.filtros label{display:block;margin-top:8px}.filtros input,.filtros select{padding:6px;margin-top:4px}.filtros .acciones{margin-top:10px}</style>
</head>
<body>
    <h1>Vehículos</h1>
    <?php if ($message): ?><p style="color:green"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

    <p><a href="vehicle_form.php">Agregar vehículo</a> | <a href="dashboard.php">Volver al panel</a></p>

    <form method="get" action="vehicles.php" class="filtros">
        <label for="type">Tipo</label>
        <select id="type" name="type">
            <option value="">Todos</option>
            <option value="Auto" <?php echo $typeFilter === 'Auto' ? 'selected' : ''; ?>>Auto</option>
            <option value="Camioneta" <?php echo $typeFilter === 'Camioneta' ? 'selected' : ''; ?>>Camioneta</option>
            <option value="Moto" <?php echo $typeFilter === 'Moto' ? 'selected' : ''; ?>>Moto</option>
            <option value="Camion" <?php echo $typeFilter === 'Camion' ? 'selected' : ''; ?>>Camión</option>
        </select>

        <label for="brand">Marca</label>
        <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($brandFilter); ?>" placeholder="Ej: VW">

        <label for="model">Modelo</label>
        <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($modelFilter); ?>" placeholder="Ej: Gol">

        <div class="acciones">
            <button type="submit">Filtrar</button>
            <a href="vehicles.php">Limpiar</a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vehicles)): ?>
                <tr>
                    <td colspan="7">No se encontraron vehículos con esos filtros.</td>
                </tr>
            <?php else: ?>
            <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><?php echo htmlspecialchars($v->getId()); ?></td>
                    <td><?php echo htmlspecialchars($v->getType()); ?></td>
                    <td><?php echo htmlspecialchars($v->getBrand()); ?></td>
                    <td><?php echo htmlspecialchars($v->getModel()); ?></td>
                    <td><?php echo htmlspecialchars($v->getYear()); ?></td>
                    <td><?php echo htmlspecialchars(number_format($v->getPrice(), 2)); ?></td>
                    <td>
                        <a href="vehicle_form.php?id=<?php echo urlencode($v->getId()); ?>">Editar</a>
                        
                        <form method="post" action="vehicle_process.php" style="display:inline" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($v->getId()); ?>">
                            <input type="submit" value="Eliminar">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>