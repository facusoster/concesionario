<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h2 class="h4 mb-2">Bienvenido, <?php echo htmlspecialchars($name); ?></h2>
        <p class="mb-0">Rol: <span class="badge text-bg-secondary"><?php echo htmlspecialchars($role); ?></span></p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="h6 text-uppercase text-secondary mb-3">Acciones rápidas</h3>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="vehicles.php">Gestionar Vehiculos</a>
            <?php if ($role === 'admin'): ?>
                <a class="btn btn-outline-primary" href="index.php?controller=users&amp;action=index">Gestionar Usuarios</a>
            <?php endif; ?>
            <a class="btn btn-outline-danger" href="logout.php">Cerrar sesion</a>
        </div>
    </div>
</div>

<p class="small text-secondary mt-3 mb-0">Nota: los empleados deben usar la seccion de vehiculos; solo los administradores pueden gestionar usuarios.</p>
