<?php
// Simple login form (formulario de login)
// Archivo público: public/login.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/Flash.php';

$message = Flash::get('success') ?? Flash::get('info') ?? '';
$error = Flash::get('error') ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Concesionario</title>
    <style>
        body{font-family: Arial, sans-serif; margin:40px}
        .container{max-width:400px;margin:auto}
        label{display:block;margin-top:10px}
        input[type="submit"]{margin-top:15px}
        .error{color:#b00020}
        .info{color:#006400}
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (!empty($message)): ?>
            <p class="info"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="login_process.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <input type="submit" value="Ingresar">
        </form>
    </div>
</body>
</html>