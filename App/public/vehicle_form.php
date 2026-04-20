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

$oldVehicleForm = $_SESSION['old_vehicle_form'] ?? [];
unset($_SESSION['old_vehicle_form']);

if (!empty($oldVehicleForm)) {
    $type = $oldVehicleForm['type'] ?? $type;
    $brand = $oldVehicleForm['brand'] ?? $brand;
    $model = $oldVehicleForm['model'] ?? $model;
    $year = $oldVehicleForm['year'] ?? $year;
    $price = $oldVehicleForm['price'] ?? $price;
}

$error = Flash::get('error') ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $editing ? 'Editar vehículo' : 'Agregar vehículo'; ?> - Concesionario</title>
    <style>body{font-family:Arial, sans-serif;margin:30px}label{display:block;margin-top:8px}input,select{padding:6px;min-width:220px}input[type="submit"]{min-width:auto}.required{color:#b00020}.help{color:#666;font-size:.9em}</style>
</head>
<body>
    <h1><?php echo $editing ? 'Editar vehículo' : 'Agregar vehículo'; ?></h1>
    <p class="help"><span class="required">*</span> Campo obligatorio.</p>
    <?php if ($error): ?><p style="color:red"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

    <form method="post" action="vehicle_process.php">
        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($vehicle->getId()); ?>">
        <?php endif; ?>

        <label for="type">Tipo <span class="required">*</span></label>
        <select id="type" name="type" required>
            <option value="">Seleccione</option>
            <option value="Auto" <?php echo $type === 'Auto' ? 'selected' : ''; ?>>Auto</option>
            <option value="Camioneta" <?php echo $type === 'Camioneta' ? 'selected' : ''; ?>>Camioneta</option>
            <option value="Moto" <?php echo $type === 'Moto' ? 'selected' : ''; ?>>Moto</option>
            <option value="Camion" <?php echo $type === 'Camion' ? 'selected' : ''; ?>>Camión</option>
        </select>

        <label for="brand">Marca <span class="required">*</span></label>
        <input type="text" id="brand" name="brand" required value="<?php echo htmlspecialchars($brand); ?>">

        <label for="model">Modelo <span class="required">*</span></label>
        <input type="text" id="model" name="model" required value="<?php echo htmlspecialchars($model); ?>">

        <label for="year">Año <span class="required">*</span></label>
        <input type="number" id="year" name="year" required value="<?php echo htmlspecialchars($year); ?>">

        <label for="price">Precio <span class="required">*</span></label>
        <input type="number" step="0.01" id="price" name="price" required value="<?php echo htmlspecialchars($price); ?>">

        <input type="submit" value="<?php echo $editing ? 'Actualizar' : 'Crear'; ?>">
    </form>

    <p><a href="vehicles.php">Volver al listado</a></p>
</body>
</html>