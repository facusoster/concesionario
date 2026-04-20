<?php

/**
 * Clase para manejar autenticación y autorización
 * 
 * Proporciona métodos para validar que el usuario tiene los permisos necesarios
 * antes de ejecutar acciones. Si no tiene permisos, lanza AuthException.
 */

class Auth {
    /**
     * Verifica que el usuario está autenticado como administrador
     * Si no, lanza AuthException
     * 
     * @throws AuthException Si el usuario no es admin
     */
    public static function requireAdmin(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            throw new AuthException('No tienes permiso para ejecutar esta acción.');
        }
    }

    /**
     * Verifica que el usuario tiene un rol específico
     * Si no, lanza AuthException
     * 
     * @param string $role El rol requerido (ej: 'admin', 'employee')
     * @throws AuthException Si el usuario no tiene el rol requerido
     */
    public static function requireRole(string $role): void {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            throw new AuthException("Se requiere rol: {$role}");
        }
    }

    /**
     * Verifica que el usuario está autenticado
     * Si no, lanza AuthException
     * 
     * @throws AuthException Si el usuario no está autenticado
     */
    public static function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            throw new AuthException('Debes estar autenticado para ejecutar esta acción.');
        }
    }

    /**
     * Comprueba si el usuario actual es administrador (sin lanzar excepción)
     * 
     * @return bool true si es admin, false en caso contrario
     */
    public static function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Comprueba si el usuario actual está autenticado (sin lanzar excepción)
     * 
     * @return bool true si está autenticado, false en caso contrario
     */
    public static function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Obtiene el ID del usuario actual
     * 
     * @return int|null El ID del usuario o null si no está autenticado
     */
    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el rol del usuario actual
     * 
     * @return string|null El rol del usuario o null si no está autenticado
     */
    public static function role(): ?string {
        return $_SESSION['role'] ?? null;
    }
}
