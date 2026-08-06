<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middleware;
use App\Models\Audit;
use App\Models\AuditControlMaturity;
use App\Models\Organization;

final class ComparisonController extends Controller
{
    public function index(): void
    {
        Middleware::auth();
        $this->view('comparison/index', [
            'title'         => 'Comparación histórica',
            'organizations' => (new Organization())->activeOptions(),
            'orgId'         => 0,
            'audits'        => [],
            'chartData'     => null,
        ]);
    }

    public function show(): void
    {
        Middleware::auth();
        $orgId  = (int) ($_GET['organization_id'] ?? 0);
        $audits = [];
        $chartData = null;

        if ($orgId > 0) {
            $auditModel = new Audit();
            $acmModel   = new AuditControlMaturity();

            // Auditorías cerradas de la organización ordenadas por fecha
            $db        = \App\Core\Database::getConnection();
            $statement = $db->prepare(
                'SELECT au.id, au.name, au.end_date, au.maturity_score, au.risk_score,
                        au.risk_c, au.risk_i, au.risk_d
                 FROM audits au
                 WHERE au.organization_id = :oid AND au.status = "closed"
                   AND au.maturity_score IS NOT NULL
                 ORDER BY au.end_date ASC'
            );
            $statement->execute(['oid' => $orgId]);
            $audits = $statement->fetchAll();

            if (count($audits) >= 1) {
                // Para cada auditoría obtener madurez por dominio
                $domainMap = [];
                foreach ($audits as $a) {
                    $rows = $acmModel->forAudit((int) $a['id']);
                    foreach ($rows as $r) {
                        $domainMap[$r['domain_code']] = $r['domain_name'];
                    }
                }

                $datasets = [];
                foreach ($audits as $a) {
                    $rows      = $acmModel->forAudit((int) $a['id']);
                    $byDomain  = [];
                    $weights   = [];
                    foreach ($rows as $r) {
                        $dc = $r['domain_code'];
                        if (! isset($byDomain[$dc])) { $byDomain[$dc] = 0.0; $weights[$dc] = 0.0; }
                        $byDomain[$dc] += (float) $r['maturity_level'] * (float) $r['weight'];
                        $weights[$dc]  += (float) $r['weight'];
                    }
                    $domainMaturities = [];
                    foreach (array_keys($domainMap) as $dc) {
                        $domainMaturities[] = isset($weights[$dc]) && $weights[$dc] > 0
                            ? round($byDomain[$dc] / $weights[$dc], 2) : 0.0;
                    }
                    $datasets[] = [
                        'label' => $a['name'] . ' (' . date('d/m/Y', strtotime($a['end_date'])) . ')',
                        'data'  => $domainMaturities,
                    ];
                }

                $chartData = [
                    'labels'   => array_keys($domainMap),
                    'datasets' => $datasets,
                ];
            }
        }

        $this->view('comparison/index', [
            'title'         => 'Comparación histórica',
            'organizations' => (new Organization())->activeOptions(),
            'orgId'         => $orgId,
            'audits'        => $audits,
            'chartData'     => $chartData,
        ]);
    }
}
