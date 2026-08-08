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
        $orgId  = Auth::organizationId();
        $audits = new Audit();
        $this->view('dashboard/index', [
            'title'          => 'Dashboard',
            'stats'          => $audits->stats($orgId),
            'recentClosed'   => $audits->recentClosed(5, $orgId),
            'maturityDomain' => $audits->maturityByDomain($orgId),
            'orgCount'       => $orgId !== null ? 1 : (new Organization())->count(),
            'recCounts'      => (new Recommendation())->countByStatus($orgId),
        ]);
    }
}
