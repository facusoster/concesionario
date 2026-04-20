<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="h4 mb-0">Gestion de Usuarios</h2>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="user_form.php">Alta de empleado</a>
        <a class="btn btn-outline-secondary" href="dashboard.php">Volver al dashboard</a>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
    <form method="get" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="controller" value="users">
        <input type="hidden" name="action" value="index">

        <div class="col-12 col-md-6">
            <label class="form-label" for="q">Busqueda rapida</label>
            <input class="form-control" type="text" id="q" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Buscar por nombre o email">
        </div>

        <div class="col-12 col-md-2">
            <label class="form-label" for="perPage">Por página</label>
            <select class="form-select" id="perPage" name="perPage" onchange="this.form.submit()">
                <?php foreach ([5, 10, 20, 50] as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ((int)($perPage ?? 10) === $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort ?? 'id'); ?>">
        <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir ?? 'desc'); ?>">
        <input type="hidden" name="page" value="1">

        <div class="col-12 col-md-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-outline-secondary" href="index.php?controller=users&amp;action=index">Limpiar</a>
        </div>
    </form>
    </div>
</div>

<p class="text-muted mb-2">
    Mostrando <?php echo count($users ?? []); ?> de <?php echo (int)($totalUsers ?? 0); ?> usuarios.
    Página <?php echo (int)($page ?? 1); ?> de <?php echo (int)($totalPages ?? 1); ?>.
</p>

    <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <?php
                $baseParams = [
                    'controller' => 'users',
                    'action' => 'index',
                    'q' => $q ?? '',
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
                <th><a class="text-decoration-none" href="index.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'id', 'dir' => $toggleDir('id')])); ?>"><?php echo htmlspecialchars($sortLabel('id', 'ID')); ?></a></th>
                <th><a class="text-decoration-none" href="index.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'name', 'dir' => $toggleDir('name')])); ?>"><?php echo htmlspecialchars($sortLabel('name', 'Nombre')); ?></a></th>
                <th><a class="text-decoration-none" href="index.php?<?php echo http_build_query(array_merge($baseParams, ['sort' => 'email', 'dir' => $toggleDir('email')])); ?>"><?php echo htmlspecialchars($sortLabel('email', 'Email')); ?></a></th>
                <th>Rol</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5">No hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <?php
                        $roleValue = (string)$u['role'];
                        $roleLabel = $roleValue === 'admin'
                            ? 'Administrador'
                            : ($roleValue === 'employee' ? 'Empleado' : 'Sin definir');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string)$u['id']); ?></td>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($roleLabel); ?></td>
                        <td class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="index.php?controller=users&amp;action=edit&amp;id=<?php echo urlencode((string)$u['id']); ?>">Editar</a>
                            <?php if ((int)$u['id'] !== (int)$currentUserId): ?>
                                <form method="post" action="user_process.php" class="d-inline" onsubmit="return confirm('¿Seguro que desea eliminar este usuario?');">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$u['id']); ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            <?php endif; ?>
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
        'controller' => 'users',
        'action' => 'index',
        'q' => $q ?? '',
        'sort' => $sort ?? 'id',
        'dir' => $dir ?? 'desc',
        'perPage' => $perPage ?? 10,
    ];
    ?>
    <nav aria-label="Paginacion de usuarios" class="mt-3">
        <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => 1])); ?>">Primera</a>
            </li>
            <li class="page-item <?php echo (($page ?? 1) <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => max(1, ($page ?? 1) - 1)])); ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                <li class="page-item <?php echo (($page ?? 1) === $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => min(($totalPages ?? 1), ($page ?? 1) + 1)])); ?>">Siguiente</a>
            </li>
            <li class="page-item <?php echo (($page ?? 1) >= ($totalPages ?? 1)) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo http_build_query(array_merge($paginationBase, ['page' => ($totalPages ?? 1)])); ?>">Última</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
