<?php

require_once __DIR__ . '/../../config/database.php';

// Interface for authentication (contrato)
interface Authenticable {
    public function login(string $email, string $password): bool;
    public function logout(): void;
}

// Abstract user class (base para Employee/Administrator)
abstract class User implements Authenticable {
    // Protected properties to allow access from subclasses
    protected ?int $id = null;
    protected string $name;
    protected string $email;
    protected ?string $password = null; // hashed password

    public function __construct(string $name, string $email, string $password = '') {
        $this->name = $name;
        $this->email = $email;
        if ($password !== '') {
            // Guardamos el hash
            $this->setPassword($password);
        }
    }

    // Getters / Setters
    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getHashedPassword(): ?string { return $this->password; }

    // Password handling
    public function setPassword(string $plain): void {
        $this->password = password_hash($plain, PASSWORD_DEFAULT);
    }

    protected function verifyPassword(string $plain): bool {
        return $this->password !== null && password_verify($plain, $this->password);
    }

    // Factory to build User from DB row
    public static function fromArray(array $data): User {
        $role = $data['role'] ?? 'employee';
        if ($role === 'admin') {
            $user = new Administrator($data['name'] ?? '', $data['email'] ?? '');
        } else {
            $user = new Employee($data['name'] ?? '', $data['email'] ?? '');
        }
        // asignar hashed password directamente
        $user->password = $data['password'] ?? null;
        $user->id = isset($data['id']) ? (int)$data['id'] : null;
        return $user;
    }

    // Cada subclase debe implementar su rol
    abstract public function getRole(): string;

    // Authentication methods will be implemented in concrete classes
    abstract public function login(string $email, string $password): bool;
    abstract public function logout(): void;
}

// Employee class
class Employee extends User {
    public function __construct(string $name, string $email, string $password = '') {
        parent::__construct($name, $email, $password);
    }

    public function getRole(): string { return 'employee'; }

    public function login(string $email, string $password): bool {
        // Simple in-memory check; persistence should be handled by a repository
        return $this->email === $email && $this->verifyPassword($password);
    }

    public function logout(): void {
        // Terminar la sesión (si se estuviera usando)
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}

// Administrator class
class Administrator extends User {
    public function getRole(): string { return 'admin'; }

    public function login(string $email, string $password): bool {
        return $this->email === $email && $this->verifyPassword($password);
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    // Convenience factory to create employees (demo method)
    public function createEmployee(string $name, string $email, string $plainPwd): Employee {
        return new Employee($name, $email, $plainPwd);
    }
}
