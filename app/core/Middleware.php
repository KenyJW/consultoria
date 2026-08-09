<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\AuditorOrganization;
use App\Models\Organization;

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
     * Lista de organization_id que el usuario actual tiene permitido ver o
     * tocar, o null si no tiene ninguna restriccion de este tipo (admin,
     * viewer global, o un usuario autoregistrado — ese ya se restringe
     * aparte via Auth::organizationId()). Un auditor de la consultora sin
     * organizacion propia (role=auditor, organization_id NULL) solo puede
     * trabajar con las organizaciones que un admin le haya asignado en
     * auditor_organizations; si no tiene ninguna, devuelve [] (no ve nada).
     */
    public static function assignedOrganizationIds(): ?array
    {
        $user = Auth::user();
        if ($user === null) {
            return [];
        }
        if ($user['role'] === 'auditor' && ($user['organization_id'] ?? null) === null) {
            return (new AuditorOrganization())->organizationIdsForUser((int) $user['id']);
        }
        return null;
    }

    /**
     * Organizaciones activas que el usuario actual puede ver en listas y
     * selectores (crear auditoria, comparacion, filtros...), ya filtradas
     * segun su alcance real.
     */
    public static function visibleOrganizations(): array
    {
        $all = (new Organization())->activeOptions();

        $scopedOrgId = Auth::organizationId();
        if ($scopedOrgId !== null) {
            return array_values(array_filter($all, fn($o) => (int) $o['id'] === $scopedOrgId));
        }

        $allowed = self::assignedOrganizationIds();
        if ($allowed === null) {
            return $all;
        }
        return array_values(array_filter($all, fn($o) => in_array((int) $o['id'], $allowed, true)));
    }

    /**
     * Verifica que el recurso pertenezca a una organizacion que el usuario
     * actual puede tocar. Un usuario autoregistrado (organization_id fijo)
     * solo puede acceder a recursos de su propia organizacion. El personal
     * de la consultora sin restriccion (admin, viewer global) pasa siempre.
     * Un auditor de la consultora (organization_id NULL) solo pasa si la
     * organizacion del recurso esta entre las que tiene asignadas.
     */
    public static function ownsOrganization(?int $resourceOrganizationId): void
    {
        $userOrgId = Auth::organizationId();
        if ($userOrgId !== null) {
            if ($resourceOrganizationId !== $userOrgId) {
                ErrorHandler::render(403, 'No tiene permisos para acceder a este recurso.');
                exit;
            }
            return;
        }

        $allowed = self::assignedOrganizationIds();
        if ($allowed === null) {
            return;
        }
        if ($resourceOrganizationId === null || ! in_array($resourceOrganizationId, $allowed, true)) {
            ErrorHandler::render(403, 'No tiene esta organización asignada.');
            exit;
        }
    }
}
