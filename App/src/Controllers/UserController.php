<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Logger.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Flash.php';
require_once __DIR__ . '/../Repositories/user_repository.php';
require_once __DIR__ . '/../Exceptions/ValidationException.php';
require_once __DIR__ . '/../Exceptions/RepositoryException.php';
require_once __DIR__ . '/../Exceptions/AuthException.php';

// Require User models
require_once __DIR__ . '/../Models/User.php';

class UserController extends Controller {
    private UserRepository $repo;

    public function __construct() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->repo = new UserRepository();
    }

    public function index(): void {
        try {
            Auth::requireAdmin();

            $nameFilter = trim($_GET['name'] ?? '');
            $emailFilter = trim($_GET['email'] ?? '');
            $roleFilter = trim($_GET['role'] ?? '');

            if (!in_array($roleFilter, ['', 'admin', 'employee'], true)) {
                $roleFilter = '';
            }

            $users = $this->repo->findByFilters($nameFilter, $emailFilter, $roleFilter);
            $this->render('users/index', [
                'users' => $users,
                'message' => Flash::get('success') ?? Flash::get('info') ?? '',
                'error' => Flash::get('error') ?? '',
                'currentUserId' => (int)($_SESSION['user_id'] ?? 0),
                'nameFilter' => $nameFilter,
                'emailFilter' => $emailFilter,
                'roleFilter' => $roleFilter,
            ]);
        } catch (AuthException $e) {
            Logger::error('Intento de acceso no autorizado a usuarios', ['user_id' => Auth::userId()]);
            http_response_code(403);
            echo 'Acceso denegado: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function create(): void {
        try {
            Auth::requireAdmin();

            $oldForm = $_SESSION['old_user_form'] ?? [];
            unset($_SESSION['old_user_form']);

            $this->render('users/create', [
                'error' => Flash::get('error') ?? '',
                'oldName' => $oldForm['name'] ?? '',
                'oldEmail' => $oldForm['email'] ?? '',
                'oldRole' => $oldForm['role'] ?? 'employee',
            ]);
        } catch (AuthException $e) {
            Logger::error('Intento de acceso no autorizado para crear usuario', ['user_id' => Auth::userId()]);
            http_response_code(403);
            echo 'Acceso denegado: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function store(): void {
        $name = '';
        $email = '';
        $role = 'employee';

        try {
            Auth::requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('index.php?controller=users&action=create');
            }

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = trim($_POST['role'] ?? 'employee');

            if ($name === '' || $email === '' || $password === '') {
                throw new ValidationException('Nombre, email y password son obligatorios.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('El email no es válido.');
            }

            if (!in_array($role, ['admin', 'employee'], true)) {
                throw new ValidationException('Rol inválido.');
            }

            $existing = $this->repo->findByEmail($email);
            if ($existing !== null) {
                throw new ValidationException('Ya existe un usuario con ese email.');
            }

            $newUser = $role === 'admin'
                ? new Administrator($name, $email, $password)
                : new Employee($name, $email, $password);
            $this->repo->save($newUser);

            Flash::success('Usuario creado correctamente.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (AuthException $e) {
            Logger::error('Intento de crear usuario sin permisos de admin', ['user_id' => Auth::userId()]);
            Flash::error('No tienes permiso para crear usuarios.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (ValidationException $e) {
            $_SESSION['old_user_form'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];
            Flash::error($e->getMessage());
            $this->redirect('index.php?controller=users&action=create');
        } catch (RepositoryException $e) {
            $_SESSION['old_user_form'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];
            Flash::error('No se pudo crear el usuario. Intente nuevamente.');
            $this->redirect('index.php?controller=users&action=create');
        } catch (Throwable $e) {
            Logger::error('Error no controlado en UserController::store', [
                'exception' => $e->getMessage(),
            ]);
            Flash::error('Error interno del sistema.');
            $this->redirect('index.php?controller=users&action=create');
        } finally {
            // Punto de extensión para trazabilidad adicional si se requiere.
        }
    }

    public function edit(): void {
        try {
            Auth::requireAdmin();

            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                Flash::error('Usuario inválido.');
                $this->redirect('index.php?controller=users&action=index');
            }

            $user = $this->repo->findById($id);
            if ($user === null) {
                Flash::error('Usuario no encontrado.');
                $this->redirect('index.php?controller=users&action=index');
            }

            $this->render('users/edit', [
                'user' => $user,
                'error' => Flash::get('error') ?? '',
            ]);
        } catch (AuthException $e) {
            Logger::error('Intento de acceso no autorizado para editar usuario', ['user_id' => Auth::userId()]);
            http_response_code(403);
            echo 'Acceso denegado: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function update(): void {
        $id = 0;

        try {
            Auth::requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('index.php?controller=users&action=index');
            }

            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? '');

            if ($id <= 0 || $name === '' || $email === '' || $role === '') {
                throw new ValidationException('Todos los campos son obligatorios.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('El email no es válido.');
            }

            if (!in_array($role, ['admin', 'employee'], true)) {
                throw new ValidationException('Rol inválido.');
            }

            if ($this->repo->emailExistsForOtherId($email, $id)) {
                throw new ValidationException('Ya existe otro usuario con ese email.');
            }

            $this->repo->updateBasic($id, $name, $email, $role);

            Flash::success('Usuario actualizado correctamente.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (AuthException $e) {
            Logger::error('Intento de actualizar usuario sin permisos de admin', ['user_id' => Auth::userId()]);
            Flash::error('No tienes permiso para actualizar usuarios.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (ValidationException $e) {
            Flash::error($e->getMessage());
            $this->redirect('index.php?controller=users&action=edit&id=' . $id);
        } catch (RepositoryException $e) {
            Flash::error('No se pudo actualizar el usuario. Intente nuevamente.');
            $this->redirect('index.php?controller=users&action=edit&id=' . $id);
        } catch (Throwable $e) {
            Logger::error('Error no controlado en UserController::update', [
                'exception' => $e->getMessage(),
                'id' => $id,
            ]);
            Flash::error('Error interno del sistema.');
            $this->redirect('index.php?controller=users&action=edit&id=' . $id);
        } finally {
            // Punto de extensión para trazabilidad adicional si se requiere.
        }
    }

    public function delete(): void {
        $id = 0;

        try {
            Auth::requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('index.php?controller=users&action=index');
            }

            $id = (int)($_POST['id'] ?? 0);
            $currentUserId = (int)($_SESSION['user_id'] ?? 0);

            if ($id <= 0) {
                throw new ValidationException('Usuario inválido.');
            }

            if ($id === $currentUserId) {
                throw new ValidationException('No podés eliminar tu propio usuario en sesión.');
            }

            $this->repo->deleteById($id);

            Flash::success('Usuario eliminado correctamente.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (AuthException $e) {
            Logger::error('Intento de eliminar usuario sin permisos de admin', ['user_id' => Auth::userId()]);
            Flash::error('No tienes permiso para eliminar usuarios.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (ValidationException $e) {
            Flash::error($e->getMessage());
            $this->redirect('index.php?controller=users&action=index');
        } catch (RepositoryException $e) {
            Flash::error('No se pudo eliminar el usuario. Intente nuevamente.');
            $this->redirect('index.php?controller=users&action=index');
        } catch (Throwable $e) {
            Logger::error('Error no controlado en UserController::delete', [
                'exception' => $e->getMessage(),
                'id' => $id,
            ]);
            Flash::error('Error interno del sistema.');
            $this->redirect('index.php?controller=users&action=index');
        } finally {
            // Punto de extensión para trazabilidad adicional si se requiere.
        }
    }
}
