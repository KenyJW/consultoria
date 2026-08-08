<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Organization;
use App\Models\User;

/** Autoregistro publico: una organizacion nueva crea su propia cuenta para autoevaluarse. */
final class RegisterController extends Controller
{
    private Organization $organizations;
    private User $users;

    public function __construct()
    {
        $this->organizations = new Organization();
        $this->users = new User();
    }

    public function show(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/register', ['title' => 'Crear cuenta'], 'auth');
        unset($_SESSION['_old']);
    }

    public function store(): void
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro. Intente nuevamente.');
            $this->redirect('/register');
        }

        $data = [
            'name'              => trim((string) ($_POST['name'] ?? '')),
            'email'             => trim((string) ($_POST['email'] ?? '')),
            'password'          => (string) ($_POST['password'] ?? ''),
            'organization_name' => trim((string) ($_POST['organization_name'] ?? '')),
        ];
        $_SESSION['_old'] = ['name' => $data['name'], 'email' => $data['email'], 'organization_name' => $data['organization_name']];

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre')
            ->required('email', $data['email'], 'Correo')
            ->email('email', $data['email'], 'Correo')
            ->required('organization_name', $data['organization_name'], 'Nombre de la organizacion')
            ->minLength('password', $data['password'], 8, 'Contrasena');

        if ($validator->fails()) {
            Flash::errors($validator->errors());
            $this->redirect('/register');
        }

        if ($this->users->emailExists($data['email'])) {
            Flash::error('Ya existe una cuenta con ese correo.');
            $this->redirect('/register');
        }

        if ($this->organizations->nameExists($data['organization_name'])) {
            Flash::error('Ya existe una organizacion registrada con ese nombre. Si es la suya, pida a un administrador que le cree el usuario.');
            $this->redirect('/register');
        }

        $organizationId = $this->organizations->createAndGetId([
            'name'    => $data['organization_name'],
            'address' => '',
            'email'   => $data['email'],
            'status'  => 'active',
        ]);

        $this->users->createScoped($data['name'], $data['email'], $data['password'], $organizationId);
        $user = $this->users->findByEmail($data['email']);
        unset($user['password']);

        Auth::attempt($user);
        unset($_SESSION['_old']);

        Flash::success('Cuenta creada. Ya puede registrar sus areas y comenzar a autoevaluarse.');
        $this->redirect('/dashboard');
    }
}
