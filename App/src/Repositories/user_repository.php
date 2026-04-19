<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/user.php';

/**
 * UserRepository
 * Responsable de la persistencia de usuarios usando PDO (prepared statements, try/catch)
 */
class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Busca un usuario por email. Retorna instancia de User (Employee/Administrator) o null.
     */
    public function findByEmail(string $email): ?User {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            return User::fromArray($row);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Retorna todos los usuarios para listados administrativos.
     */
    public function findAll(): array {
        try {
            $stmt = $this->db->prepare('SELECT id, name, email, role FROM users ORDER BY id DESC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Retorna usuarios filtrando por nombre, email y rol.
     * Si un filtro viene vacío, no se aplica.
     */
    public function findByFilters(string $name = '', string $email = '', string $role = ''): array {
        try {
            $sql = 'SELECT id, name, email, role FROM users WHERE 1=1';
            $params = [];

            if ($name !== '') {
                $sql .= ' AND name LIKE :name';
                $params[':name'] = '%' . $name . '%';
            }

            if ($email !== '') {
                $sql .= ' AND email LIKE :email';
                $params[':email'] = '%' . $email . '%';
            }

            if ($role !== '') {
                $sql .= ' AND role = :role';
                $params[':role'] = $role;
            }

            $sql .= ' ORDER BY id DESC';

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Busca usuario por id y retorna fila asociativa o null.
     */
    public function findById(int $id): ?array {
        try {
            $stmt = $this->db->prepare('SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Verifica si existe otro usuario con el mismo email.
     */
    public function emailExistsForOtherId(string $email, int $id): bool {
        try {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
            $stmt->execute([
                ':email' => $email,
                ':id' => $id,
            ]);
            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return true;
        }
    }

    /**
     * Actualiza nombre, email y rol de un usuario.
     */
    public function updateBasic(int $id, string $name, string $email, string $role): bool {
        try {
            $stmt = $this->db->prepare('UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id');
            return $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Elimina usuario por id.
     */
    public function deleteById(int $id): bool {
        try {
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Guarda un usuario. Si no tiene id hace INSERT, si tiene id hace UPDATE.
     * Devuelve true en exito, false en error.
     */
    public function save(User $user): bool {
        try {
            if ($user->getId() === null) {
                $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
                $hashed = $user->getHashedPassword();
                if ($hashed === null) {
                    throw new InvalidArgumentException('Password not set for user');
                }
                $stmt->execute([
                    ':name' => $user->getName(),
                    ':email' => $user->getEmail(),
                    ':password' => $hashed,
                    ':role' => $user->getRole(),
                ]);
                $user->setId((int)$this->db->lastInsertId());
                return true;
            } else {
                $stmt = $this->db->prepare('UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id');
                $stmt->execute([
                    ':name' => $user->getName(),
                    ':email' => $user->getEmail(),
                    ':password' => $user->getHashedPassword(),
                    ':role' => $user->getRole(),
                    ':id' => $user->getId(),
                ]);
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
