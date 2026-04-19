<?php
// Script CLI: crea un usuario administrador si no existe.
// Uso: php src/test/seed_admin.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Repositories/user_repository.php';
require_once __DIR__ . '/../Models/user.php';

$email = 'admin@concesionario.test';
$plainPassword = 'secret123';

$repo = new UserRepository();

try {
    $existing = $repo->findByEmail($email);
    if ($existing) {
        echo "Admin already exists: id={$existing->getId()}, role={$existing->getRole()}\n";
        exit(0);
    }

    $admin = new Administrator('Administrator', $email, $plainPassword);
    $saved = $repo->save($admin);

    if ($saved) {
        echo "Admin created successfully. ID={$admin->getId()}\n";
    } else {
        echo "Failed to create admin (see logs).\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
