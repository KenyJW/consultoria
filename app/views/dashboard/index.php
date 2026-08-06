<?php
use App\Core\MaturityCalculator;

$avgMaturity = (float) ($stats['avg_maturity'] ?? 0);
$avgRisk     = (float) ($stats['avg_risk'] ?? 0);
?>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Organizaciones activas</div>
                <div class="display-6 fw-bold"><?= (int) $orgCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Auditorías totales</div>
                <div class="display-6 fw-bold"><?= (int) ($stats['total'] ?? 0) ?></div>
                <div class="small text-muted">
                    <?= (int) ($stats['in_progress'] ?? 0) ?> en progreso &middot;
                    <?= (int) ($stats['draft'] ?? 0) ?> borrador
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Madurez promedio</div>
                <?php if ($avgMaturity > 0): ?>
                    <div class="display-6 fw-bold text-<?= MaturityCalculator::maturityColor($avgMaturity) ?>">
                        <?= number_format($avgMaturity, 2) ?><span class="fs-6 text-muted">/5</span>
                    </div>
                    <div class="small text-muted"><?= MaturityCalculator::maturityLabel($avgMaturity) ?></div>
                <?php else: ?>
                    <div class="display-6 fw-bold text-muted">—</div>
                    <div class="small text-muted">Sin auditorías cerradas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Riesgo promedio</div>
                <?php if ($avgRisk > 0): ?>
                    <div class="display-6 fw-bold text-<?= MaturityCalculator::riskColor($avgRisk) ?>">
                        <?= number_format($avgRisk, 1) ?><span class="fs-6 text-muted">%</span>
                    </div>
                    <div class="small text-muted"><?= MaturityCalculator::riskLabel($avgRisk) ?></div>
                <?php else: ?>
                    <div class="display-6 fw-bold text-muted">—</div>
                    <div class="small text-muted">Sin auditorías cerradas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- KPIs recomendaciones -->
<?php if (($recCounts['pending'] ?? 0) + ($recCounts['in_progress'] ?? 0) > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
    <span class="fs-5">⚠️</span>
    <div>
        <strong><?= (int) ($recCounts['pending'] ?? 0) ?> recomendaciones pendientes</strong>
        y <?= (int) ($recCounts['in_progress'] ?? 0) ?> en progreso.
        <a href="<?= BASE_URL ?>/recommendations" class="alert-link ms-2">Ver todas →</a>
    </div>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <!-- HEATMAP real: tabla dominio × nivel de madurez -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Mapa de calor — Madurez por dominio</h2>
                <?php if (empty($maturityDomain)): ?>
                    <p class="text-muted small">Sin datos. Complete al menos una auditoría.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 text-center" style="font-size:0.8rem;">
                            <thead>
                                <tr class="table-dark">
                                    <th class="text-start">Dominio</th>
                                    <?php for ($lvl = 0; $lvl <= 5; $lvl++): ?>
                                        <th><?= $lvl ?></th>
                                    <?php endfor; ?>
                                    <th>Promedio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maturityDomain as $d): ?>
                                    <?php $m = (float) $d['avg_maturity']; $cell = (int) round($m); ?>
                                    <tr>
                                        <td class="text-start text-nowrap">
                                            <span class="badge bg-secondary me-1"><?= e($d['domain_code']) ?></span>
                                            <?= e($d['domain_name']) ?>
                                        </td>
                                        <?php for ($lvl = 0; $lvl <= 5; $lvl++): ?>
                                            <?php
                                            $bg = $lvl === $cell
                                                ? match(true) {
                                                    $m < 1 => '#dc3545',
                                                    $m < 2 => '#fd7e14',
                                                    $m < 3 => '#ffc107',
                                                    $m < 4 => '#0dcaf0',
                                                    $m < 5 => '#198754',
                                                    default => '#146c43',
                                                  }
                                                : '#f8f9fa';
                                            $fg = $lvl === $cell && $m >= 3 ? '#fff' : '#212529';
                                            ?>
                                            <td style="background:<?= $bg ?>;color:<?= $fg ?>;font-weight:<?= $lvl === $cell ? 'bold' : 'normal' ?>;">
                                                <?= $lvl === $cell ? '●' : '' ?>
                                            </td>
                                        <?php endfor; ?>
                                        <td class="fw-bold text-<?= MaturityCalculator::maturityColor($m) ?>">
                                            <?= number_format($m, 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 d-flex gap-2 flex-wrap" style="font-size:0.75rem;">
                        <span style="color:#dc3545">● 0 No existe</span>
                        <span style="color:#fd7e14">● 1 Informal</span>
                        <span style="color:#ffc107">● 2 Parcial</span>
                        <span style="color:#0dcaf0">● 3 Definido</span>
                        <span style="color:#198754">● 4 Gestionado</span>
                        <span style="color:#146c43">● 5 Optimizado</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de barras -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Madurez promedio por dominio</h2>
                <?php if (empty($maturityDomain)): ?>
                    <p class="text-muted small">Sin datos disponibles.</p>
                <?php else: ?>
                    <canvas id="maturityChart" class="fixed-chart" height="220"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Últimas auditorías cerradas -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Últimas auditorías cerradas</h2>
        <?php if (empty($recentClosed)): ?>
            <p class="text-muted small">No hay auditorías cerradas con resultados calculados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Auditoría</th>
                            <th>Organización</th>
                            <th>Cierre</th>
                            <th class="text-center">Madurez</th>
                            <th class="text-center">Riesgo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentClosed as $a): ?>
                            <?php $m = (float) $a['maturity_score']; $r = (float) $a['risk_score']; ?>
                            <tr>
                                <td><?= e($a['name']) ?></td>
                                <td><?= e($a['organization_name']) ?></td>
                                <td><?= e(date('d/m/Y', strtotime($a['end_date']))) ?></td>
                                <td class="text-center">
                                    <span class="badge text-bg-<?= MaturityCalculator::maturityColor($m) ?>">
                                        <?= number_format($m, 2) ?>/5
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge text-bg-<?= MaturityCalculator::riskColor($r) ?>">
                                        <?= number_format($r, 1) ?>%
                                    </span>
                                </td>
                                <td class="text-end d-flex gap-1 justify-content-end">
                                    <a href="<?= BASE_URL ?>/audits/report?id=<?= (int) $a['id'] ?>"
                                       class="btn btn-outline-secondary btn-sm" target="_blank">Reporte</a>
                                    <a href="<?= BASE_URL ?>/recommendations/audit?audit_id=<?= (int) $a['id'] ?>"
                                       class="btn btn-outline-warning btn-sm">Recomendaciones</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (! empty($maturityDomain)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?= json_encode(array_column($maturityDomain, 'domain_code')) ?>;
    const data   = <?= json_encode(array_map(fn($d) => (float) $d['avg_maturity'], $maturityDomain)) ?>;
    const colors = data.map(v => v < 1 ? '#dc3545' : v < 2 ? '#fd7e14' : v < 3 ? '#ffc107' : v < 4 ? '#0dcaf0' : '#198754');
    new Chart(document.getElementById('maturityChart'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Madurez', data, backgroundColor: colors, borderRadius: 4 }] },
        options: {
            scales: { y: { min: 0, max: 5, ticks: { stepSize: 1 } } },
            plugins: { legend: { display: false } },
            responsive: true,
            maintainAspectRatio: false,
        }
    });
})();
</script>
<?php endif; ?>

<style>
/* Evita que el canvas crezca indefinidamente en pantallas pequeñas */
.fixed-chart { max-height: 320px; width: 100% !important; height: auto !important; }
</style>
