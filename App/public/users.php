<?php

// Wrapper de compatibilidad: redirige al Front Controller.
$params = $_GET;
$params['controller'] = 'users';
$params['action'] = 'index';

header('Location: index.php?' . http_build_query($params));
exit;
