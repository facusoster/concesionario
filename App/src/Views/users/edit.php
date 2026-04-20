<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="h4 mb-0">Editar Usuario</h2>
    <a class="btn btn-outline-secondary" href="index.php?controller=users&amp;action=index">Volver a gestión de usuarios</a>
</div>

    <?php $currentRole = (string)$user['role']; ?>

<div class="card shadow-sm">
    <div class="card-body">
<form method="post" action="user_process.php" class="row g-3">
    <input type="hidden" name="_action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$user['id']); ?>">

    <div class="col-12 col-md-6">
    <label class="form-label" for="name">Nombre</label>
    <input class="form-control" type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
    </div>

    <div class="col-12 col-md-6">
    <label class="form-label" for="email">Email</label>
    <input class="form-control" type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
    </div>

    <div class="col-12 col-md-6">
    <label class="form-label" for="role">Rol</label>
    <select class="form-select" id="role" name="role" required>
        <option value="employee" <?php echo $currentRole === 'employee' ? 'selected' : ''; ?>>Empleado</option>
        <option value="admin" <?php echo $currentRole === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        <?php if (!in_array($currentRole, ['employee', 'admin'], true)): ?>
            <option value="<?php echo htmlspecialchars($currentRole); ?>" selected><?php echo htmlspecialchars($currentRole); ?></option>
        <?php endif; ?>
    </select>
    </div>

    <div class="col-12">
        <button class="btn btn-primary" type="submit">Guardar cambios</button>
    </div>
</form>
    </div>
</div>
