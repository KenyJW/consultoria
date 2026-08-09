<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

/**
 * Distingue si una auditoría fue realizada por personal de la consultora
 * (independiente, sin organization_id propio) o es una autoevaluación
 * hecha por el propio personal de la organización auditada. No es lo mismo
 * para efectos de valor probatorio del reporte frente a terceros.
 */
function audit_kind_label(?int $auditorOrganizationId): string
{
    return $auditorOrganizationId === null ? 'Auditoría de consultora' : 'Autoevaluación';
}

function audit_kind_badge_class(?int $auditorOrganizationId): string
{
    return $auditorOrganizationId === null ? 'text-bg-dark' : 'text-bg-secondary';
}
