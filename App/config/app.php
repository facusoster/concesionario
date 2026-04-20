<?php

/**
 * Configuración centralizada por entorno (desarrollo/producción)
 * 
 * Define cómo el sistema maneja errores, logs y debugging
 */

// Detectar entorno (cambiar a 'production' cuando suba a internet)
define('ENV', getenv('APP_ENV') ?: 'development');
define('DEBUG', ENV === 'development');

// Configurar manejo de errores de PHP según entorno
if (DEBUG) {
    // DESARROLLO: Mostrar todos los errores en pantalla para debugging
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
} else {
    // PRODUCCIÓN: Ocultar errores técnicos al usuario, pero registrar en logs
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
}

// Configuración global
define('APP_NAME', 'Concesionario');
define('APP_URL', 'http://localhost/Concesionario/App/public');
define('TIMEZONE', 'America/Argentina/Buenos_Aires');

date_default_timezone_set(TIMEZONE);

// Registrar el manejador global de errores y excepciones (Fase 3)
require_once __DIR__ . '/../core/ErrorHandler.php';
require_once __DIR__ . '/../core/Logger.php';
ErrorHandler::register();
