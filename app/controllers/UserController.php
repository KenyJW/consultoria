<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\User;

final class UserController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        Middleware::roles(['admin']);
        $this->view('users/index', ['title' => 'Usuarios', 'users' => $this->users->all()]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('users/form', ['title' => 'Nuevo usuario', 'user' => null, 'action' => '/users/store']);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData(true);
        if ($this->users->emailExists($data['email'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un usuario con ese correo.');
            $this->redirect('/users/create');
        }

        $this->users->create($data);
        Flash::success('Usuario creado correctamente.');
        $this->redirect('/users');
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->users->find($id);
        if (! $user) {
            Flash::error('Usuario no encontrado.');
            $this->redirect('/users');
        }
        $this->view('users/form', ['title' => 'Editar usuario', 'user' => $user, 'action' => '/users/update']);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData(false, $id);
        if ($this->users->emailExists($data['email'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un usuario con ese correo.');
            $this->redirect('/users/edit?id=' . $id);
        }

        $this->users->update($id, $data);
        Flash::success('Usuario actualizado correctamente.');
        $this->redirect('/users');
    }

    public function destroy(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/users');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id !== (int) (Auth::user()['id'] ?? 0)) {
            $this->users->delete($id);
            Flash::success('Usuario desactivado correctamente.');
        }
        $this->redirect('/users');
    }

    private function validatedData(bool $requirePassword, ?int $id = null): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/users');
        }

        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'role' => (string) ($_POST['role'] ?? 'auditor'),
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre')
            ->email('email', $data['email'], 'Correo')
            ->in('role', $data['role'], ['admin', 'auditor', 'viewer'], 'Rol')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($requirePassword) {
            $validator->minLength('password', $data['password'], 8, 'Contrasena');
        }

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($id ? '/users/edit?id=' . $id : '/users/create');
        }

        return $data;
    }
}
