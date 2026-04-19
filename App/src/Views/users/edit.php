<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Usuario - Concesionario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="email"], select { width: 320px; padding: 8px; }
        .error { color: #b00020; }
        .actions { margin-top: 14px; }
    </style>
</head>
<body>
    <h1>Editar Usuario</h1>

    <?php $currentRole = (string)$user['role']; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="user_process.php">
        <input type="hidden" name="_action" value="update">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$user['id']); ?>">

        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">

        <label for="role">Rol</label>
        <select id="role" name="role" required>
            <option value="employee" <?php echo $currentRole === 'employee' ? 'selected' : ''; ?>>Empleado</option>
            <option value="admin" <?php echo $currentRole === 'admin' ? 'selected' : ''; ?>>Administrador</option>
            <?php if (!in_array($currentRole, ['employee', 'admin'], true)): ?>
                <option value="<?php echo htmlspecialchars($currentRole); ?>" selected><?php echo htmlspecialchars($currentRole); ?></option>
            <?php endif; ?>
        </select>

        <div class="actions">
            <input type="submit" value="Guardar cambios">
        </div>
    </form>

    <p><a href="index.php?controller=users&amp;action=index">Volver a gestión de usuarios</a></p>
</body>
</html>
