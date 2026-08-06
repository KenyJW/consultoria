<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\User;

final class LoginController extends Controller
{
    public function show(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', ['title' => 'Iniciar sesion'], 'auth');
        unset($_SESSION['_old']);
    }

    public function authenticate(): void
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro. Intente nuevamente.');
            $this->redirect('/login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $_SESSION['_old'] = ['email' => $email];

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            Flash::error('Ingrese un correo valido y una contrasena.');
            $this->redirect('/login');
        }

        $user = (new User())->verifyCredentials($email, $password);
        if ($user === null) {
            Flash::error('Credenciales invalidas o usuario inactivo.');
            $this->redirect('/login');
        }

        Auth::attempt($user);
        unset($_SESSION['_old']);
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/dashboard');
        }

        Auth::logout();
        $this->redirect('/login');
    }
}
