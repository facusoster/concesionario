<?php

// Wrapper de compatibilidad para formulario de alta de empleado.
$params = $_GET;
$params['controller'] = 'users';
$params['action'] = 'create';

header('Location: index.php?' . http_build_query($params));
exit;
