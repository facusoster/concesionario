<?php

/**
 * Clase para manejar mensajes flash (sesión)
 * 
 * Los mensajes flash se almacenan en $_SESSION y se muestran una sola vez.
 * Después de ser mostrados, se eliminan automáticamente.
 * 
 * Uso:
 * - Flash::success('Usuario creado');
 * - Flash::error('No se pudo guardar');
 * - Flash::warning('Cuidado!');
 * - Flash::info('Información importante');
 * - $message = Flash::get('success');
 */

class Flash {
    private const SESSION_KEY = 'flash_messages';

    /**
     * Registra un mensaje de éxito
     * 
     * @param string $message El mensaje a mostrar
     */
    public static function success(string $message): void {
        self::set('success', $message);
    }

    /**
     * Registra un mensaje de error
     * 
     * @param string $message El mensaje a mostrar
     */
    public static function error(string $message): void {
        self::set('error', $message);
    }

    /**
     * Registra un mensaje de advertencia
     * 
     * @param string $message El mensaje a mostrar
     */
    public static function warning(string $message): void {
        self::set('warning', $message);
    }

    /**
     * Registra un mensaje informativo
     * 
     * @param string $message El mensaje a mostrar
     */
    public static function info(string $message): void {
        self::set('info', $message);
    }

    /**
     * Obtiene y elimina un mensaje flash
     * 
     * @param string $type El tipo de mensaje ('success', 'error', 'warning', 'info')
     * @return string|null El mensaje o null si no existe
     */
    public static function get(string $type): ?string {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_KEY][$type])) {
            return null;
        }

        $message = $_SESSION[self::SESSION_KEY][$type];
        unset($_SESSION[self::SESSION_KEY][$type]);

        return $message;
    }

    /**
     * Obtiene todos los mensajes flash disponibles
     * 
     * @return array Array asociativo con tipos como claves y mensajes como valores
     */
    public static function getAll(): array {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            return [];
        }

        $messages = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);

        return $messages;
    }

    /**
     * Verifica si existe un mensaje de un tipo específico
     * 
     * @param string $type El tipo de mensaje
     * @return bool true si existe el mensaje
     */
    public static function has(string $type): bool {
        self::ensureSession();
        return isset($_SESSION[self::SESSION_KEY][$type]);
    }

    /**
     * Limpia todos los mensajes flash
     */
    public static function clear(): void {
        self::ensureSession();
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Registra un mensaje en la sesión
     * 
     * @param string $type El tipo de mensaje
     * @param string $message El contenido del mensaje
     */
    private static function set(string $type, string $message): void {
        self::ensureSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $_SESSION[self::SESSION_KEY][$type] = $message;
    }

    /**
     * Asegura que la sesión está activa
     */
    private static function ensureSession(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
