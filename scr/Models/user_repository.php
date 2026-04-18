<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/user.php';

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
