<?php
declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    public static function auth(): void
    {
        if (! Auth::check()) {
            Flash::error('Debe iniciar sesion para continuar.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function roles(array $roles): void
    {
        self::auth();
        $role = Auth::user()['role'] ?? null;
        if (! in_array($role, $roles, true)) {
            ErrorHandler::render(403, 'No tiene permisos para acceder a este recurso.');
            exit;
        }
    }
}
