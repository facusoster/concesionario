<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Logger.php';
require_once __DIR__ . '/../Exceptions/RepositoryException.php';
require_once __DIR__ . '/../Models/user.php';

/**
 * UserRepository
 * Responsable de la persistencia de usuarios usando PDO (prepared statements, try/catch)
 */
class UserRepository {
    private PDO $db;
    private array $allowedSortColumns = [
        'id' => 'id',
        'name' => 'name',
        'email' => 'email',
    ];

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
            Logger::error('Error SQL en findByEmail', [
                'method' => __METHOD__,
                'email' => $email,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo consultar el usuario por email.', 0, $e);
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
    public function countByFilters(string $q = '', string $role = ''): int {
        try {
            $sql = 'SELECT COUNT(*) AS total FROM users WHERE 1=1';
            $params = [];

            if ($q !== '') {
                $sql .= ' AND (name LIKE :q OR email LIKE :q)';
                $params[':q'] = '%' . $q . '%';
            }

            if ($role !== '') {
                $sql .= ' AND role = :role';
                $params[':role'] = $role;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            Logger::error('Error SQL en countByFilters(User)', [
                'method' => __METHOD__,
                'q' => $q,
                'role' => $role,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo contar el listado de usuarios.', 0, $e);
        }
    }

    public function findByFilters(string $q = '', string $sort = 'id', string $dir = 'desc', int $page = 1, int $perPage = 10, string $role = ''): array {
        try {
            $sql = 'SELECT id, name, email, role FROM users WHERE 1=1';
            $params = [];

            if ($q !== '') {
                $sql .= ' AND (name LIKE :q OR email LIKE :q)';
                $params[':q'] = '%' . $q . '%';
            }

            if ($role !== '') {
                $sql .= ' AND role = :role';
                $params[':role'] = $role;
            }

            $sortColumn = $this->allowedSortColumns[$sort] ?? 'id';
            $sortDirection = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
            $offset = max(0, ($page - 1) * $perPage);
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . $sortDirection . ' LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error('Error SQL en findByFilters(User)', [
                'method' => __METHOD__,
                'q' => $q,
                'sort' => $sort,
                'dir' => $dir,
                'page' => $page,
                'perPage' => $perPage,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo consultar el listado de usuarios.', 0, $e);
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
            Logger::error('Error SQL en findById(User)', [
                'method' => __METHOD__,
                'id' => $id,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo consultar el usuario por id.', 0, $e);
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
            Logger::error('Error SQL en updateBasic', [
                'method' => __METHOD__,
                'id' => $id,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo actualizar el usuario en base de datos.', 0, $e);
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
            Logger::error('Error SQL en deleteById', [
                'method' => __METHOD__,
                'id' => $id,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo eliminar el usuario en base de datos.', 0, $e);
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
            Logger::error('Error SQL en save(User)', [
                'method' => __METHOD__,
                'userId' => $user->getId(),
                'userEmail' => $user->getEmail(),
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo guardar el usuario en base de datos.', 0, $e);
        }
    }
}
