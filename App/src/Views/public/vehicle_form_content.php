<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="h4 mb-0"><?php echo $editing ? 'Editar vehiculo' : 'Agregar vehiculo'; ?></h2>
    <a class="btn btn-outline-secondary" href="vehicles.php">Volver al listado</a>
</div>

<p class="text-secondary small"><span class="required">*</span> Campo obligatorio.</p>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="vehicle_process.php" class="row g-3" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($vehicle->getId()); ?>">
            <?php endif; ?>

            <div class="col-12 col-md-6">
                <label class="form-label" for="type">Tipo <span class="required">*</span></label>
                <select class="form-select" id="type" name="type" required>
                    <option value="">Seleccione</option>
                    <option value="Auto" <?php echo $type === 'Auto' ? 'selected' : ''; ?>>Auto</option>
                    <option value="Camioneta" <?php echo $type === 'Camioneta' ? 'selected' : ''; ?>>Camioneta</option>
                    <option value="Moto" <?php echo $type === 'Moto' ? 'selected' : ''; ?>>Moto</option>
                    <option value="Camion" <?php echo $type === 'Camion' ? 'selected' : ''; ?>>Camion</option>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="brand">Marca <span class="required">*</span></label>
                <input class="form-control" type="text" id="brand" name="brand" required value="<?php echo htmlspecialchars($brand); ?>">
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="model">Modelo <span class="required">*</span></label>
                <input class="form-control" type="text" id="model" name="model" required value="<?php echo htmlspecialchars($model); ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label" for="year">Año <span class="required">*</span></label>
                <input class="form-control" type="number" id="year" name="year" required value="<?php echo htmlspecialchars($year); ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label" for="price">Precio <span class="required">*</span></label>
                <input class="form-control" type="number" step="0.01" id="price" name="price" required value="<?php echo htmlspecialchars($price); ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label" for="status">Estado <span class="required">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="disponible" <?php echo ($status ?? 'disponible') === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                    <option value="vendido" <?php echo ($status ?? 'disponible') === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                </select>
            </div>

            <?php
            $currentImageName = ($editing && $vehicle) ? $vehicle->getImageName() : null;
            $currentImageUrl = $currentImageName
                ? 'uploads/vehicles/' . rawurlencode($currentImageName)
                : 'uploads/vehicles/default-vehicle.svg';
            ?>

            <div class="col-12 col-md-6">
                <label class="form-label" for="image">Imagen del vehículo</label>
                <input
                    class="form-control"
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                >
                <div class="form-text">Opcional. Máximo 2MB. Formatos permitidos: JPG, PNG.</div>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Vista previa</label>
                <div>
                    <img
                        src="<?php echo htmlspecialchars($currentImageUrl); ?>"
                        alt="Imagen actual del vehículo"
                        style="max-width: 180px; width: 100%; height: auto; border: 1px solid #dee2e6; border-radius: 0.5rem;"
                    >
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit"><?php echo $editing ? 'Actualizar' : 'Crear'; ?></button>
            </div>
        </form>
    </div>
</div>
