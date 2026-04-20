<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Logger.php';
require_once __DIR__ . '/../Exceptions/RepositoryException.php';
require_once __DIR__ . '/../Models/Vehicle.php';

class VehicleRepository {
    private PDO $db;
    private array $allowedSortColumns = [
        'id' => 'id',
        'type' => 'type',
        'brand' => 'brand',
        'model' => 'model',
        'year' => 'year',
        'price' => 'price',
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Get all vehicles as Vehicle instances */
    public function getAll(): array {
        try {
            $stmt = $this->db->prepare('SELECT * FROM vehicles ORDER BY id DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $list = [];
            foreach ($rows as $r) {
                $list[] = Vehicle::fromArray($r);
            }
            return $list;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Retorna vehículos filtrando por type, brand y model.
     * Si un filtro viene vacío, no se aplica.
     */
    public function countByFilters(string $q = ''): int {
        try {
            $sql = 'SELECT COUNT(*) AS total FROM vehicles WHERE 1=1';
            $params = [];

            if ($q !== '') {
                $sql .= ' AND (type LIKE :q OR brand LIKE :q OR model LIKE :q)';
                $params[':q'] = '%' . $q . '%';
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            Logger::error('Error SQL en countByFilters(Vehicle)', [
                'method' => __METHOD__,
                'q' => $q,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo contar el listado de vehículos.', 0, $e);
        }
    }

    public function getByFilters(string $q = '', string $sort = 'id', string $dir = 'desc', int $page = 1, int $perPage = 10): array {
        try {
            $sql = 'SELECT * FROM vehicles WHERE 1=1';
            $params = [];

            if ($q !== '') {
                $sql .= ' AND (type LIKE :q OR brand LIKE :q OR model LIKE :q)';
                $params[':q'] = '%' . $q . '%';
            }

            $sortColumn = $this->allowedSortColumns[$sort] ?? 'id';
            $sortDirection = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
            $offset = max(0, ($page - 1) * $perPage);
            $sql .= ' ORDER BY ' . $sortColumn . ' ' . $sortDirection . ' LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $list = [];
            foreach ($rows as $r) {
                $list[] = Vehicle::fromArray($r);
            }
            return $list;
        } catch (PDOException $e) {
            Logger::error('Error SQL en getByFilters(Vehicle)', [
                'method' => __METHOD__,
                'q' => $q,
                'sort' => $sort,
                'dir' => $dir,
                'page' => $page,
                'perPage' => $perPage,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo consultar el listado de vehículos.', 0, $e);
        }
    }

    /** Find by id */
    public function findById(int $id): ?Vehicle {
        try {
            $stmt = $this->db->prepare('SELECT * FROM vehicles WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            return Vehicle::fromArray($row);
        } catch (PDOException $e) {
            Logger::error('Error SQL en findById(Vehicle)', [
                'method' => __METHOD__,
                'id' => $id,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo consultar el vehículo por id.', 0, $e);
        }
    }

    /** Save (insert or update) */
    public function save(Vehicle $vehicle): bool {
        try {
            if ($vehicle->getId() === null) {
                $stmt = $this->db->prepare('INSERT INTO vehicles (type, brand, model, year, price) VALUES (:type, :brand, :model, :year, :price)');
                $res = $stmt->execute([
                    ':type' => $vehicle->getType(),
                    ':brand' => $vehicle->getBrand(),
                    ':model' => $vehicle->getModel(),
                    ':year' => $vehicle->getYear(),
                    ':price' => $vehicle->getPrice(),
                ]);
                if ($res) {
                    $vehicle->setId((int)$this->db->lastInsertId());
                }
                return $res;
            } else {
                $stmt = $this->db->prepare('UPDATE vehicles SET type = :type, brand = :brand, model = :model, year = :year, price = :price WHERE id = :id');
                return $stmt->execute([
                    ':type' => $vehicle->getType(),
                    ':brand' => $vehicle->getBrand(),
                    ':model' => $vehicle->getModel(),
                    ':year' => $vehicle->getYear(),
                    ':price' => $vehicle->getPrice(),
                    ':id' => $vehicle->getId(),
                ]);
            }
        } catch (PDOException $e) {
            Logger::error('Error SQL en save(Vehicle)', [
                'method' => __METHOD__,
                'vehicleId' => $vehicle->getId(),
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo guardar el vehículo en base de datos.', 0, $e);
        }
    }

    /** Delete by id */
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare('DELETE FROM vehicles WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            Logger::error('Error SQL en delete(Vehicle)', [
                'method' => __METHOD__,
                'id' => $id,
                'exception' => $e->getMessage(),
            ]);
            throw new RepositoryException('No se pudo eliminar el vehículo en base de datos.', 0, $e);
        }
    }
}
