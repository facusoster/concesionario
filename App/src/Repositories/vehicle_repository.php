<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Logger.php';
require_once __DIR__ . '/../Exceptions/RepositoryException.php';
require_once __DIR__ . '/../Models/Vehicle.php';

class VehicleRepository {
    private PDO $db;

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
    public function getByFilters(string $type = '', string $brand = '', string $model = ''): array {
        try {
            $sql = 'SELECT * FROM vehicles WHERE 1=1';
            $params = [];

            if ($type !== '') {
                $sql .= ' AND type = :type';
                $params[':type'] = $type;
            }

            if ($brand !== '') {
                $sql .= ' AND brand LIKE :brand';
                $params[':brand'] = '%' . $brand . '%';
            }

            if ($model !== '') {
                $sql .= ' AND model LIKE :model';
                $params[':model'] = '%' . $model . '%';
            }

            $sql .= ' ORDER BY id DESC';

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
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
