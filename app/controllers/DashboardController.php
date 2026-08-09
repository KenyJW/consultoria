<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Middleware;
use App\Models\Audit;
use App\Models\Organization;
use App\Models\Recommendation;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Middleware::auth();
        $scopedOrgId = Auth::organizationId();
        $audits      = new Audit();
        $organizations = new Organization();

        // Empresa: siempre ve su propia organizacion. Admin/auditor: elige una
        // organizacion especifica (de las que tiene permitidas) para ver su
        // detalle (mapa de calor, madurez/riesgo promedio, auditorias
        // recientes) — sin mezclar el promedio de varias empresas distintas
        // en un solo numero.
        $allowedOrgIds = Middleware::assignedOrganizationIds();
        $selectedOrgId = $scopedOrgId;
        if ($scopedOrgId === null) {
            $requested = (int) ($_GET['organization_id'] ?? 0);
            $selectedOrgId = $requested > 0 ? $requested : null;
            if ($selectedOrgId !== null) {
                Middleware::ownsOrganization($selectedOrgId);
            }
        }

        // Conteo global de auditorias (solo lo usa el admin/auditor para el
        // KPI de "auditorias totales"; para empresa ese numero ya sale de sus
        // propias auditorias via $orgStats). Para un auditor con alcance
        // limitado, "todas las empresas" en realidad significa solo las que
        // tiene asignadas.
        $globalAuditCounts = $scopedOrgId === null ? $audits->stats(null, $allowedOrgIds) : null;
        $orgStats          = $selectedOrgId !== null ? $audits->stats($selectedOrgId) : [];

        $this->view('dashboard/index', [
            'title'             => 'Dashboard',
            'scoped'            => $scopedOrgId !== null,
            'selectedOrgId'     => $selectedOrgId,
            'organizations'     => $scopedOrgId === null ? Middleware::visibleOrganizations() : [],
            'globalAuditCounts' => $globalAuditCounts,
            'orgStats'          => $orgStats,
            'recentClosed'      => $selectedOrgId !== null ? $audits->recentClosed(5, $selectedOrgId) : [],
            'maturityDomain'    => $selectedOrgId !== null ? $audits->maturityByDomain($selectedOrgId) : [],
            'orgCount'          => $scopedOrgId !== null ? 1 : ($allowedOrgIds !== null ? count($allowedOrgIds) : $organizations->count()),
            'recCounts'         => (new Recommendation())->countByStatus($scopedOrgId, $allowedOrgIds),
        ]);
    }
}
