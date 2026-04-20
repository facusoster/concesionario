<?php

/**
 * Manejador global de errores y excepciones
 * 
 * Centraliza el manejo de:
 * - Excepciones no capturadas (set_exception_handler)
 * - Errores de PHP (set_error_handler)
 * 
 * Proporciona respuestas consistentes al usuario sin exponer detalles técnicos
 * Registra todos los problemas en logs
 */

class ErrorHandler {
    /**
     * Registra los handlers globales
     * Debe ser llamado lo antes posible en la aplicación
     */
    public static function register(): void {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    /**
     * Manejador global de excepciones no capturadas
     * 
     * @param Throwable $exception La excepción capturada
     */
    public static function handleException(Throwable $exception): void {
        // Cargar Logger
        if (!class_exists('Logger')) {
            require_once __DIR__ . '/Logger.php';
        }

        // Determinar tipo de excepción
        $className = get_class($exception);

        // Registrar el error en logs
        Logger::error('Excepción no capturada: ' . $className, [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Si está en desarrollo, mostrar detalles
        if (defined('DEBUG') && DEBUG) {
            http_response_code(500);
            echo '<h1>⚠️ Error en desarrollo</h1>';
            echo '<p><strong>Excepción:</strong> ' . htmlspecialchars($className) . '</p>';
            echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>';
            echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</p>';
            echo '<details><summary>Stack Trace</summary><pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre></details>';
            return;
        }

        // En producción, mostrar página amigable
        http_response_code(500);
        self::showErrorPage('Error interno del servidor. Por favor, intente más tarde.');
    }

    /**
     * Manejador global de errores de PHP
     * 
     * @param int $errno Tipo de error
     * @param string $errstr Mensaje de error
     * @param string $errfile Archivo donde ocurrió
     * @param int $errline Línea donde ocurrió
     * @return bool true para detener propagación, false para continuar
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        // Cargar Logger
        if (!class_exists('Logger')) {
            require_once __DIR__ . '/Logger.php';
        }

        // Ignorar errores silenciados (@)
        if (error_reporting() === 0) {
            return true;
        }

        // Convertir a string el tipo de error
        $errorType = self::getErrorType($errno);

        // Registrar el error
        Logger::error('Error de PHP: ' . $errorType, [
            'errno' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ]);

        // No lanzar excepción por warnings o notices normales
        // Solo por errores críticos
        if ($errno === E_ERROR || $errno === E_PARSE || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        return false; // Dejar que PHP maneje el error normalmente
    }

    /**
     * Convierte un código de error PHP a su nombre
     * 
     * @param int $errno El código de error
     * @return string El nombre del tipo de error
     */
    private static function getErrorType(int $errno): string {
        $errorTypes = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return $errorTypes[$errno] ?? 'UNKNOWN_ERROR';
    }

    /**
     * Muestra una página de error amigable
     * 
     * @param string $message Mensaje a mostrar al usuario
     */
    private static function showErrorPage(string $message): void {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    padding: 40px;
                    max-width: 500px;
                    text-align: center;
                }
                h1 { color: #e74c3c; margin-bottom: 20px; font-size: 2em; }
                p { color: #555; margin-bottom: 30px; font-size: 1.1em; line-height: 1.6; }
                .emoji { font-size: 3em; margin-bottom: 20px; }
                a { 
                    display: inline-block;
                    background: #667eea;
                    color: white;
                    padding: 12px 30px;
                    border-radius: 5px;
                    text-decoration: none;
                    transition: background 0.3s;
                }
                a:hover { background: #764ba2; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="emoji">⚠️</div>
                <h1>¡Oops! Algo salió mal</h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="javascript:history.back()">← Volver atrás</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
