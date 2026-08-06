<?php
declare(strict_types=1);

namespace App\Controllers;

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
        $audits = new Audit();
        $this->view('dashboard/index', [
            'title'          => 'Dashboard',
            'stats'          => $audits->stats(),
            'recentClosed'   => $audits->recentClosed(5),
            'maturityDomain' => $audits->maturityByDomain(),
            'orgCount'       => (new Organization())->count(),
            'recCounts'      => (new Recommendation())->countByStatus(),
        ]);
    }
}
