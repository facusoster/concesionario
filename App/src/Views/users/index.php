<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Usuarios - Concesionario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .ok { color: #0a7a0a; }
        .error { color: #b00020; }
        .actions { margin: 10px 0; }
        .filters label { display: block; margin-top: 8px; }
        .filters input, .filters select { padding: 6px; min-width: 240px; }
        .filters .row-actions { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <?php if (!empty($message)): ?>
        <p class="ok"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="actions">
        <a href="user_form.php">Alta de empleado</a> |
        <a href="dashboard.php">Volver al dashboard</a>
    </div>

    <form method="get" action="index.php" class="filters">
        <input type="hidden" name="controller" value="users">
        <input type="hidden" name="action" value="index">

        <label for="name">Filtrar por nombre</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nameFilter ?? ''); ?>" placeholder="Ej: Facundo">

        <label for="email">Filtrar por email</label>
        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($emailFilter ?? ''); ?>" placeholder="Ej: user@user.com">

        <label for="role">Filtrar por rol</label>
        <select id="role" name="role">
            <option value="" <?php echo ($roleFilter ?? '') === '' ? 'selected' : ''; ?>>Todos</option>
            <option value="employee" <?php echo ($roleFilter ?? '') === 'employee' ? 'selected' : ''; ?>>Empleado</option>
            <option value="admin" <?php echo ($roleFilter ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select>

        <div class="row-actions">
            <button type="submit">Filtrar</button>
            <a href="index.php?controller=users&amp;action=index">Limpiar</a>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acción</th>
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
                        <td>
                            <a href="index.php?controller=users&amp;action=edit&amp;id=<?php echo urlencode((string)$u['id']); ?>">Editar</a>
                            <?php if ((int)$u['id'] !== (int)$currentUserId): ?>
                                |
                                <form method="post" action="user_process.php" style="display:inline" onsubmit="return confirm('¿Seguro que desea eliminar este usuario?');">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$u['id']); ?>">
                                    <input type="submit" value="Eliminar">
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
