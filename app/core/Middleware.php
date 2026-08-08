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

    /**
     * Verifica que el recurso pertenezca a la organizacion del usuario
     * actual. El personal de la consultora (organization_id = null en su
     * cuenta) no tiene restriccion y pasa siempre. Un usuario autoregistrado
     * (organization_id fijo) solo puede acceder a recursos de su propia
     * organizacion; cualquier otra devuelve 403.
     */
    public static function ownsOrganization(?int $resourceOrganizationId): void
    {
        $userOrgId = Auth::organizationId();
        if ($userOrgId === null) {
            return;
        }
        if ($resourceOrganizationId !== $userOrgId) {
            ErrorHandler::render(403, 'No tiene permisos para acceder a este recurso.');
            exit;
        }
    }
}
