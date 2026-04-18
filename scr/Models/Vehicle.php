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
    
    // Miembro estático para conteo (contador)
    private static int $totalInstances = 0;

    public function __construct(array $data = []) {
        $this->type  = $data['type'] ?? null;
        $this->brand = $data['brand'] ?? null;
        $this->model = $data['model'] ?? null;
        $this->year  = isset($data['year']) ? (int)$data['year'] : null;
        $this->price = isset($data['price']) ? (float)$data['price'] : null;
        self::$totalInstances++; 
    }

    // Getters y Setters con validación (Encapsulamiento y validación)
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): void {
        if ($price < 0) throw new InvalidArgumentException('Precio inválido');
        $this->price = $price;
    }

    public static function getTotal(): int { return self::$totalInstances; }

    // Implementación del método abstracto para guardar en la base de datos
    public function save(): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO vehicles (type, brand, model, year, price) VALUES (:t, :b, :m, :y, :p)");
            return $stmt->execute([
                ':t' => $this->type,
                ':b' => $this->brand,
                ':m' => $this->model,
                ':y' => $this->year,
                ':p' => $this->price
            ]);
        } catch (PDOException $e) {
            // Log error; no exposure to user
            error_log($e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM vehicles");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}