<?php

/**
 * Clase centralizada para registrar errores en archivos de log
 * 
 * Soporta múltiples tipos de logs para mejor análisis:
 * - app.log: Errores generales de la aplicación
 * - sql.log: Errores de base de datos
 * - auth.log: Intentos de autenticación/autorización
 * 
 * Usa error_log() de PHP con modo 3 (escritura en archivo)
 */

class Logger {
    const LOG_DIR = __DIR__ . '/../storage/logs';
    const LOG_FILE_APP = self::LOG_DIR . '/app.log';
    const LOG_FILE_SQL = self::LOG_DIR . '/sql.log';
    const LOG_FILE_AUTH = self::LOG_DIR . '/auth.log';

    /**
     * Registra un error general de la aplicación
     * 
     * @param string $message Mensaje del error
     * @param array $context Información adicional (JSON)
     */
    public static function error(string $message, array $context = []): void {
        self::write(self::LOG_FILE_APP, 'ERROR', $message, $context);
    }

    /**
     * Registra un error de SQL/base de datos
     * 
     * @param string $message Mensaje del error
     * @param array $context Información adicional (JSON)
     */
    public static function sql(string $message, array $context = []): void {
        self::write(self::LOG_FILE_SQL, 'SQL_ERROR', $message, $context);
    }

    /**
     * Registra un intento de acceso o autenticación
     * 
     * @param string $message Mensaje del evento
     * @param array $context Información adicional (JSON)
     */
    public static function auth(string $message, array $context = []): void {
        self::write(self::LOG_FILE_AUTH, 'AUTH', $message, $context);
    }

    /**
     * Registra un evento informativo
     * 
     * @param string $message Mensaje del evento
     * @param array $context Información adicional (JSON)
     */
    public static function info(string $message, array $context = []): void {
        self::write(self::LOG_FILE_APP, 'INFO', $message, $context);
    }

    /**
     * Escribe en un archivo de log con formato consistente
     * 
     * Formato: [Y-m-d H:i:s] NIVEL: Mensaje context_json
     * 
     * @param string $file Ruta del archivo de log
     * @param string $level Nivel del log (ERROR, INFO, SQL_ERROR, AUTH)
     * @param string $message Mensaje principal
     * @param array $context Información adicional
     */
    private static function write(string $file, string $level, string $message, array $context = []): void {
        self::ensureLogDir();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Formato: [timestamp] LEVEL: message context
        $logEntry = "[{$timestamp}] {$level}: {$message} {$contextJson}\n";
        
        // error_log parámetro 3 = escribir en archivo
        error_log($logEntry, 3, $file);
    }

    /**
     * Asegura que el directorio de logs existe
     * Crea la carpeta si es necesario
     */
    private static function ensureLogDir(): void {
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
    }
}
