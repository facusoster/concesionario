<?php
// Script CLI: prueba findByEmail y verificación de contraseña.
// Uso: php src/test/test_user_repository.php [email] [password]
// Ejemplo: php src/test/test_user_repository.php admin@concesionario.test secret123

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Repositories/user_repository.php';
require_once __DIR__ . '/../Models/user.php';

$email = $argv[1] ?? 'admin@concesionario.test';
$plainPassword = $argv[2] ?? 'secret123';

$repo = new UserRepository();

try {
    $user = $repo->findByEmail($email);
    if (!$user) {
        echo "User not found for email: {$email}\n";
        exit(1);
    }

    echo "Found user: id={$user->getId()}, name={$user->getName()}, role={$user->getRole()}\n";

    $hashed = $user->getHashedPassword();
    if ($hashed === null) {
        echo "User has no hashed password stored.\n";
        exit(1);
    }

    if (password_verify($plainPassword, $hashed)) {
        echo "Password verification: OK\n";
    } else {
        echo "Password verification: INVALID\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
