<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="h4 mb-0">Vehiculos</h2>
    <?php
    $csvParams = [
        'q' => $q ?? '',
        'status' => $status ?? '',
        'sort' => $sort ?? 'id',
        'dir' => $dir ?? 'desc',
    ];
    ?>
    <div class="d-flex flex-wrap gap-2">
        <?php if (!empty($isAdmin)): ?>
            <a class="btn btn-outline-success" href="vehicles_export_csv.php?<?php echo http_build_query($csvParams); ?>">Exportar CSV</a>
        <?php endif; ?>
        <a class="btn btn-primary" href="vehicle_form.php">Agregar vehiculo</a>
        <a class="btn btn-outline-secondary" href="dashboard.php">Volver al panel</a>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="vehicles.php" class="row g-3 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label" for="q">Busqueda rapida</label>
                <input class="form-control" type="text" id="q" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Buscar por tipo, marca o modelo">
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label" for="perPage">Por página</label>
                <select class="form-select" id="perPage" name="perPage" onchange="this.form.submit()">
                    <?php foreach ([5, 10, 20, 50] as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo ((int)($perPage ?? 10) === $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label" for="status">Estado</label>
                <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                    <option value="" <?php echo ($status ?? '') === '' ? 'selected' : ''; ?>>Todos</option>
                    <option value="disponible" <?php echo ($status ?? '') === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                    <option value="vendido" <?php echo ($status ?? '') === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                </select>
            </div>

            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort ?? 'id'); ?>">
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir ?? 'desc'); ?>">
            <input type="hidden" name="page" value="1">

            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a class="btn btn-outline-secondary" href="vehicles.php">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<p class="text-muted mb-2">
    Mostrando <?php echo count($vehicles ?? []); ?> de <?php echo (int)($totalVehicles ?? 0); ?> vehículos.
    Página <?php echo (int)($page ?? 1); ?> de <?php echo (int)($totalPages ?? 1); ?>.
</p>

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <?php
            $baseParams = [
                'q' => $q ?? '',
                'status' => $status ?? '',
                'perPage' => $perPage ?? 10,
            ];
            $sort = $sort ?? 'id';
            $dir = $dir ?? 'desc';
            $toggleDir = function (string $column) use ($sort, $dir): string {
                return ($sort === $column && $dir === 'asc') ? 'desc' : 'asc';
            };
            $sortLabel = function (string $column, string $label) use ($sort, $dir): string {
                if ($sort !== $column) {
                    return $label;
                }
                return $label . ($dir === 'asc' ? ' ▲' : ' ▼');
            };
            ?>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'id', 'dir' => $toggleDir('id')])); ?>"><?php echo htmlspecialchars($sortLabel('id', 'ID')); ?></a></th>
            <th>Imagen</th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'type', 'dir' => $toggleDir('type')])); ?>"><?php echo htmlspecialchars($sortLabel('type', 'Tipo')); ?></a></th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'brand', 'dir' => $toggleDir('brand')])); ?>"><?php echo htmlspecialchars($sortLabel('brand', 'Marca')); ?></a></th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'model', 'dir' => $toggleDir('model')])); ?>"><?php echo htmlspecialchars($sortLabel('model', 'Modelo')); ?></a></th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'year', 'dir' => $toggleDir('year')])); ?>"><?php echo htmlspecialchars($sortLabel('year', 'Año')); ?></a></th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'price', 'dir' => $toggleDir('price')])); ?>"><?php echo htmlspecialchars($sortLabel('price', 'Precio')); ?></a></th>
            <th><a class="text-decoration-none" href="vehicles.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'status', 'dir' => $toggleDir('status')])); ?>"><?php echo htmlspecialchars($sortLabel('status', 'Estado')); ?></a></th>
            <th>Accion</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($vehicles)): ?>
            <tr>
                <td colspan="9">No se encontraron vehiculos con esos filtros.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($vehicles as $v): ?>
                <?php
                $imageName = $v->getImageName();
                $thumbUrl = $imageName
                    ? 'uploads/vehicles/' . rawurlencode($imageName)
                    : 'uploads/vehicles/default-vehicle.svg';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($v->getId()); ?></td>
                    <td>
                        <img
                            src="<?php echo htmlspecialchars($thumbUrl); ?>"
                            alt="Imagen del vehículo <?php echo htmlspecialchars((string)$v->getId()); ?>"
                            style="width: 72px; height: 54px; object-fit: cover; border: 1px solid #dee2e6; border-radius: 0.375rem;"
                        >
                    </td>
                    <td><?php echo htmlspecialchars($v->getType()); ?></td>
                    <td><?php echo htmlspecialchars($v->getBrand()); ?></td>
                    <td><?php echo htmlspecialchars($v->getModel()); ?></td>
                    <td><?php echo htmlspecialchars($v->getYear()); ?></td>
                    <td><?php echo htmlspecialchars(number_format($v->getPrice(), 2)); ?></td>
                    <td>
                        <?php if ($v->getStatus() === 'vendido'): ?>
                            <span class="badge text-bg-danger">Vendido</span>
                        <?php else: ?>
                            <span class="badge text-bg-success">Disponible</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-inline-flex align-items-center gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="vehicle_form.php?id=<?php echo urlencode($v->getId()); ?>">Editar</a>
                            <?php if ($v->getStatus() === 'vendido'): ?>
                                <span class="badge text-bg-secondary">Historial</span>
                            <?php else: ?>
                                <form method="post" action="vehicle_process.php" class="m-0" onsubmit="return confirm('¿Seguro que desea eliminar?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($v->getId()); ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
    <?php
    $paginationBase = [
        'q' => $q ?? '',
        'status' => $status ?? '',
        'sort' => $sort ?? 'id',
        'dir' => $dir ?? 'desc',
        'perPage' => $perPage ?? 10,
    ];
    ?>
    <nav aria-label="Paginacion de vehiculos" class="mt-3">
        <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="vehicles.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => 1])); ?>">Primera</a>
            </li>
            <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="vehicles.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => max(1, ($page ?? 1) - 1)])); ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                <li class="page-item <?php echo (($page ?? 1) === $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="vehicles.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                <a class="page-link" href="vehicles.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => min(($totalPages ?? 1), ($page ?? 1) + 1)])); ?>">Siguiente</a>
            </li>
            <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                <a class="page-link" href="vehicles.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => ($totalPages ?? 1)])); ?>">Última</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
