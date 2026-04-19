<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alta de Usuario - Concesionario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 320px; padding: 8px; }
        .error { color: #b00020; }
        .actions { margin-top: 14px; }
        .required { color: #b00020; }
        .help { color: #666; font-size: .9em; }
    </style>
</head>
<body>
    <h1>Alta de Usuario</h1>
    <p class="help"><span class="required">*</span> Campo obligatorio.</p>

    <?php $selectedRole = in_array(($oldRole ?? 'employee'), ['employee', 'admin'], true) ? $oldRole : 'employee'; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="user_process.php">
        <label for="name">Nombre <span class="required">*</span></label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($oldName ?? ''); ?>">

        <label for="email">Email <span class="required">*</span></label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($oldEmail ?? ''); ?>">

        <label for="password">Password <span class="required">*</span></label>
        <input type="password" id="password" name="password" required>

        <label for="role">Rol <span class="required">*</span></label>
        <select id="role" name="role" required>
            <option value="employee" <?php echo $selectedRole === 'employee' ? 'selected' : ''; ?>>Empleado</option>
            <option value="admin" <?php echo $selectedRole === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select>

        <div class="actions">
            <input type="submit" value="Crear usuario">
        </div>
    </form>

    <p><a href="index.php?controller=users&amp;action=index">Volver a gestión de usuarios</a></p>
</body>
</html>
