<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Repositories/user_repository.php';

class UserController extends Controller {
    private UserRepository $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    private function ensureAdmin(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login.php');
        }

        if (($_SESSION['role'] ?? 'employee') !== 'admin') {
            $this->redirect('dashboard.php?error=' . rawurlencode('Acceso denegado: solo administradores.'));
        }
    }

    public function index(): void {
        $this->ensureAdmin();

        $nameFilter = trim($_GET['name'] ?? '');
        $emailFilter = trim($_GET['email'] ?? '');
        $roleFilter = trim($_GET['role'] ?? '');

        if (!in_array($roleFilter, ['', 'admin', 'employee'], true)) {
            $roleFilter = '';
        }

        $users = $this->repo->findByFilters($nameFilter, $emailFilter, $roleFilter);
        $this->render('users/index', [
            'users' => $users,
            'message' => $_GET['message'] ?? '',
            'error' => $_GET['error'] ?? '',
            'currentUserId' => (int)($_SESSION['user_id'] ?? 0),
            'nameFilter' => $nameFilter,
            'emailFilter' => $emailFilter,
            'roleFilter' => $roleFilter,
        ]);
    }

    public function create(): void {
        $this->ensureAdmin();

        $this->render('users/create', [
            'error' => $_GET['error'] ?? '',
            'oldName' => $_GET['name'] ?? '',
            'oldEmail' => $_GET['email'] ?? '',
            'oldRole' => $_GET['role'] ?? 'employee',
        ]);
    }

    public function store(): void {
        $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=users&action=create');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'employee');

        if ($name === '' || $email === '' || $password === '') {
            $this->redirect('index.php?controller=users&action=create&error=' . rawurlencode('Nombre, email y password son obligatorios.') . '&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&role=' . rawurlencode($role));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('index.php?controller=users&action=create&error=' . rawurlencode('El email no es valido.') . '&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&role=' . rawurlencode($role));
        }

        if (!in_array($role, ['admin', 'employee'], true)) {
            $this->redirect('index.php?controller=users&action=create&error=' . rawurlencode('Rol inválido.') . '&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&role=employee');
        }

        $existing = $this->repo->findByEmail($email);
        if ($existing !== null) {
            $this->redirect('index.php?controller=users&action=create&error=' . rawurlencode('Ya existe un usuario con ese email.') . '&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&role=' . rawurlencode($role));
        }

        $newUser = $role === 'admin'
            ? new Administrator($name, $email, $password)
            : new Employee($name, $email, $password);
        $saved = $this->repo->save($newUser);

        if (!$saved) {
            $this->redirect('index.php?controller=users&action=create&error=' . rawurlencode('No se pudo crear el usuario.') . '&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&role=' . rawurlencode($role));
        }

        $this->redirect('index.php?controller=users&action=index&message=' . rawurlencode('Usuario creado correctamente.'));
    }

    public function edit(): void {
        $this->ensureAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('index.php?controller=users&action=index&error=' . rawurlencode('Usuario inválido.'));
        }

        $user = $this->repo->findById($id);
        if ($user === null) {
            $this->redirect('index.php?controller=users&action=index&error=' . rawurlencode('Usuario no encontrado.'));
        }

        $this->render('users/edit', [
            'user' => $user,
            'error' => $_GET['error'] ?? '',
        ]);
    }

    public function update(): void {
        $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=users&action=index');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? '');

        if ($id <= 0 || $name === '' || $email === '' || $role === '') {
            $this->redirect('index.php?controller=users&action=edit&id=' . $id . '&error=' . rawurlencode('Todos los campos son obligatorios.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('index.php?controller=users&action=edit&id=' . $id . '&error=' . rawurlencode('El email no es válido.'));
        }

        if (!in_array($role, ['admin', 'employee'], true)) {
            $this->redirect('index.php?controller=users&action=edit&id=' . $id . '&error=' . rawurlencode('Rol inválido.'));
        }

        if ($this->repo->emailExistsForOtherId($email, $id)) {
            $this->redirect('index.php?controller=users&action=edit&id=' . $id . '&error=' . rawurlencode('Ya existe otro usuario con ese email.'));
        }

        $updated = $this->repo->updateBasic($id, $name, $email, $role);
        if (!$updated) {
            $this->redirect('index.php?controller=users&action=edit&id=' . $id . '&error=' . rawurlencode('No se pudo actualizar el usuario.'));
        }

        $this->redirect('index.php?controller=users&action=index&message=' . rawurlencode('Usuario actualizado correctamente.'));
    }

    public function delete(): void {
        $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=users&action=index');
        }

        $id = (int)($_POST['id'] ?? 0);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('index.php?controller=users&action=index&error=' . rawurlencode('Usuario inválido.'));
        }

        if ($id === $currentUserId) {
            $this->redirect('index.php?controller=users&action=index&error=' . rawurlencode('No podés eliminar tu propio usuario en sesión.'));
        }

        $deleted = $this->repo->deleteById($id);
        if (!$deleted) {
            $this->redirect('index.php?controller=users&action=index&error=' . rawurlencode('No se pudo eliminar el usuario.'));
        }

        $this->redirect('index.php?controller=users&action=index&message=' . rawurlencode('Usuario eliminado correctamente.'));
    }
}
