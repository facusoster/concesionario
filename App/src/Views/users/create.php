<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="h4 mb-0">Alta de Usuario</h2>
    <a class="btn btn-outline-secondary" href="index.php?controller=users&amp;action=index">Volver a gestión de usuarios</a>
</div>
<p class="text-secondary small"><span class="required">*</span> Campo obligatorio.</p>

    <?php $selectedRole = in_array(($oldRole ?? 'employee'), ['employee', 'admin'], true) ? $oldRole : 'employee'; ?>

<div class="card shadow-sm">
    <div class="card-body">
<form method="post" action="user_process.php" class="row g-3">
    <div class="col-12 col-md-6">
    <label class="form-label" for="name">Nombre <span class="required">*</span></label>
    <input class="form-control" type="text" id="name" name="name" required value="<?php echo htmlspecialchars($oldName ?? ''); ?>">
    </div>

    <div class="col-12 col-md-6">
    <label class="form-label" for="email">Email <span class="required">*</span></label>
    <input class="form-control" type="email" id="email" name="email" required value="<?php echo htmlspecialchars($oldEmail ?? ''); ?>">
    </div>

    <div class="col-12 col-md-6">
    <label class="form-label" for="password">Password <span class="required">*</span></label>
    <input class="form-control" type="password" id="password" name="password" required>
    </div>

    <div class="col-12 col-md-6">
    <label class="form-label" for="role">Rol <span class="required">*</span></label>
    <select class="form-select" id="role" name="role" required>
        <option value="employee" <?php echo $selectedRole === 'employee' ? 'selected' : ''; ?>>Empleado</option>
        <option value="admin" <?php echo $selectedRole === 'admin' ? 'selected' : ''; ?>>Administrador</option>
    </select>
    </div>

    <div class="col-12">
        <button class="btn btn-primary" type="submit">Crear usuario</button>
    </div>
</form>
    </div>
</div>
