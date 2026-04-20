<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h2 class="h4 mb-2">Bienvenido, <?php echo htmlspecialchars($name); ?></h2>
        <p class="mb-0">Rol: <span class="badge text-bg-secondary"><?php echo htmlspecialchars($role); ?></span></p>
        <p class="mb-0 mt-2">Vehículos disponibles: <span class="badge text-bg-success"><?php echo (int)($availableVehicles ?? 0); ?></span></p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="h6 text-uppercase text-secondary mb-3">Acciones rápidas</h3>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="vehicles.php">Gestionar Vehiculos</a>
            <a class="btn btn-outline-success" href="vehicles.php?status=disponible">Ver disponibles</a>
            <?php if ($role === 'admin'): ?>
                <a class="btn btn-outline-primary" href="index.php?controller=users&amp;action=index">Gestionar Usuarios</a>
            <?php endif; ?>
            <a class="btn btn-outline-danger" href="logout.php">Cerrar sesion</a>
        </div>
    </div>
</div>

<p class="small text-secondary mt-3 mb-0">Nota: los empleados deben usar la seccion de vehiculos; solo los administradores pueden gestionar usuarios.</p>

<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="h5 mb-0">Vehículos en venta</h3>
            <small class="text-secondary">
                Mostrando <?php echo count($saleVehicles ?? []); ?> de <?php echo (int)($totalFilteredAvailable ?? 0); ?> disponibles.
            </small>
        </div>

        <form method="get" action="dashboard.php" class="row g-3 align-items-end mb-3">
            <div class="col-12 col-lg-5">
                <label class="form-label" for="q">Búsqueda rápida</label>
                <input
                    class="form-control"
                    type="text"
                    id="q"
                    name="q"
                    value="<?php echo htmlspecialchars($q ?? ''); ?>"
                    placeholder="Buscar por tipo, marca o modelo"
                >
            </div>

            <div class="col-6 col-lg-2">
                <label class="form-label" for="sort">Ordenar por</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="id" <?php echo ($sort ?? 'id') === 'id' ? 'selected' : ''; ?>>ID</option>
                    <option value="brand" <?php echo ($sort ?? 'id') === 'brand' ? 'selected' : ''; ?>>Marca</option>
                    <option value="model" <?php echo ($sort ?? 'id') === 'model' ? 'selected' : ''; ?>>Modelo</option>
                    <option value="year" <?php echo ($sort ?? 'id') === 'year' ? 'selected' : ''; ?>>Año</option>
                    <option value="price" <?php echo ($sort ?? 'id') === 'price' ? 'selected' : ''; ?>>Precio</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label class="form-label" for="dir">Dirección</label>
                <select class="form-select" id="dir" name="dir">
                    <option value="desc" <?php echo ($dir ?? 'desc') === 'desc' ? 'selected' : ''; ?>>Descendente</option>
                    <option value="asc" <?php echo ($dir ?? 'desc') === 'asc' ? 'selected' : ''; ?>>Ascendente</option>
                </select>
            </div>

            <div class="col-6 col-lg-1">
                <label class="form-label" for="perPage">Tarjetas</label>
                <select class="form-select" id="perPage" name="perPage">
                    <?php foreach ([3, 6, 9, 12] as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo ((int)($perPage ?? 6) === $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="page" value="1">

            <div class="col-6 col-lg-2 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a class="btn btn-outline-secondary" href="dashboard.php">Limpiar</a>
            </div>
        </form>

        <?php if (empty($saleVehicles)): ?>
            <div class="alert alert-light border mb-0" role="alert">
                No hay vehículos disponibles con esos filtros.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($saleVehicles as $vehicle): ?>
                    <?php
                    $imageName = $vehicle->getImageName();
                    $cardImageUrl = $imageName
                        ? 'uploads/vehicles/' . rawurlencode($imageName)
                        : 'uploads/vehicles/default-vehicle.svg';
                    ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card h-100 border-0 shadow-sm">
                            <img
                                src="<?php echo htmlspecialchars($cardImageUrl); ?>"
                                class="card-img-top"
                                alt="Imagen de <?php echo htmlspecialchars(($vehicle->getBrand() ?? '') . ' ' . ($vehicle->getModel() ?? '')); ?>"
                                style="height: 180px; object-fit: cover;"
                            >
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h4 class="h6 mb-0"><?php echo htmlspecialchars(($vehicle->getBrand() ?? '') . ' ' . ($vehicle->getModel() ?? '')); ?></h4>
                                    <span class="badge text-bg-success">Disponible</span>
                                </div>

                                <p class="text-secondary small mb-2">
                                    <?php echo htmlspecialchars((string)($vehicle->getType() ?? 'Vehículo')); ?> del año <?php echo htmlspecialchars((string)($vehicle->getYear() ?? 'N/D')); ?>.
                                </p>

                                <p class="fw-semibold fs-5 mb-3">$<?php echo htmlspecialchars(number_format((float)$vehicle->getPrice(), 2)); ?></p>

                                <div class="mt-auto d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-primary" href="vehicle_form.php?id=<?php echo urlencode((string)$vehicle->getId()); ?>">Editar</a>
                                    <a class="btn btn-sm btn-outline-secondary" href="vehicles.php?q=<?php echo urlencode((string)($vehicle->getModel() ?? '')); ?>&status=disponible">Ver en listado</a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (($totalPages ?? 1) > 1): ?>
                <?php
                $paginationBase = [
                    'q' => $q ?? '',
                    'sort' => $sort ?? 'id',
                    'dir' => $dir ?? 'desc',
                    'perPage' => $perPage ?? 6,
                ];
                ?>
                <nav aria-label="Paginación de tarjetas" class="mt-3">
                    <ul class="pagination justify-content-center flex-wrap mb-0">
                        <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="dashboard.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => 1])); ?>">Primera</a>
                        </li>
                        <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="dashboard.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => max(1, ($page ?? 1) - 1)])); ?>">Anterior</a>
                        </li>
                        <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?php echo (($page ?? 1) === $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="dashboard.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="dashboard.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => min(($totalPages ?? 1), ($page ?? 1) + 1)])); ?>">Siguiente</a>
                        </li>
                        <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="dashboard.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => ($totalPages ?? 1)])); ?>">Última</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
