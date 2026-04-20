<?php
require_once __DIR__ . '/../../config/database.php';

// Implementamos una clase abstracta como sugiere el repaso
abstract class Manageable {
    abstract public function save(): bool;
}

class Vehicle extends Manageable {
    // Atributos privados (Encapsulamiento)
    private ?int $id = null;
    private ?string $type = null;
    private ?string $brand = null;
    private ?string $model = null;
    private ?int $year = null;
    private ?float $price = null;
    private ?string $imageName = null;
    private string $status = 'disponible';
    
    // Miembro estático para conteo (contador)
    private static int $totalInstances = 0;

    public function __construct(array $data = []) {
        $this->type  = $data['type'] ?? null;
        $this->brand = $data['brand'] ?? null;
        $this->model = $data['model'] ?? null;
        $this->year  = isset($data['year']) ? (int)$data['year'] : null;
        $this->price = isset($data['price']) ? (float)$data['price'] : null;
        $this->imageName = $data['image_name'] ?? null;
        $this->status = in_array(($data['status'] ?? 'disponible'), ['disponible', 'vendido'], true)
            ? (string)$data['status']
            : 'disponible';
        if (isset($data['id'])) {
            $this->id = (int)$data['id'];
        }
        self::$totalInstances++; 
    }

    // Factory from DB row
    public static function fromArray(array $data): Vehicle {
        return new self($data);
    }

    // Getters y Setters
    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    public function getBrand(): ?string { return $this->brand; }
    public function setBrand(string $brand): void { $this->brand = $brand; }

    public function getModel(): ?string { return $this->model; }
    public function setModel(string $model): void { $this->model = $model; }

    public function getYear(): ?int { return $this->year; }
    public function setYear(int $year): void { $this->year = $year; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): void {
        if ($price < 0) throw new InvalidArgumentException('Precio inválido');
        $this->price = $price;
    }

    public function getImageName(): ?string { return $this->imageName; }
    public function setImageName(?string $imageName): void { $this->imageName = $imageName; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void {
        if (!in_array($status, ['disponible', 'vendido'], true)) {
            throw new InvalidArgumentException('Estado inválido');
        }
        $this->status = $status;
    }

    public static function getTotal(): int { return self::$totalInstances; }

    // Implementation of abstract method (Guardar -> save)
    // Nota: persistencia preferida por VehicleRepository; este método es una conveniencia.
    public function save(): bool {
        try {
            $db = Database::getConnection();
            if ($this->id === null) {
                $stmt = $db->prepare("INSERT INTO vehicles (type, brand, model, year, price, image_name, status) VALUES (:t, :b, :m, :y, :p, :image_name, :status)");
                $res = $stmt->execute([
                    ':t' => $this->type,
                    ':b' => $this->brand,
                    ':m' => $this->model,
                    ':y' => $this->year,
                    ':p' => $this->price,
                    ':image_name' => $this->imageName,
                    ':status' => $this->status,
                ]);
                if ($res) {
                    $this->id = (int)$db->lastInsertId();
                }
                return $res;
            } else {
                $stmt = $db->prepare("UPDATE vehicles SET type = :t, brand = :b, model = :m, year = :y, price = :p, image_name = :image_name, status = :status WHERE id = :id");
                return $stmt->execute([
                    ':t' => $this->type,
                    ':b' => $this->brand,
                    ':m' => $this->model,
                    ':y' => $this->year,
                    ':p' => $this->price,
                    ':image_name' => $this->imageName,
                    ':status' => $this->status,
                    ':id' => $this->id
                ]);
            }
        } catch (PDOException $e) {
            // Log error; no exposure to user
            error_log($e->getMessage());
            return false;
        }
    }

    // Convenience static method returning raw rows (backwards compat)
    public static function getAllRaw(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM vehicles ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
