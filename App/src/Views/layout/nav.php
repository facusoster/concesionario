<?php
$showNav = $showNav ?? true;
if (!$showNav) {
    return;
}

$role = $_SESSION['role'] ?? null;
$isAuth = isset($_SESSION['user_id']);
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Concesionario</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($isAuth): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="vehicles.php">Vehiculos</a></li>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?controller=users&amp;action=index">Usuarios</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($isAuth): ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar sesion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
