<!-- Selector de organización -->
<form method="get" action="<?= BASE_URL ?>/comparison" class="row g-2 mb-4">
    <div class="col-md-4">
        <select class="form-select" name="organization_id">
            <option value="0">Seleccione una organización</option>
            <?php foreach ($organizations as $org): ?>
                <option value="<?= (int) $org['id'] ?>" <?= $orgId === (int) $org['id'] ? 'selected' : '' ?>>
                    <?= e($org['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" type="submit">Ver historial</button>
    </div>
</form>

<?php if ($orgId > 0 && empty($audits)): ?>
    <div class="alert alert-info">Esta organización no tiene auditorías cerradas con resultados calculados.</div>
<?php endif; ?>

<?php if (! empty($audits)): ?>

<!-- Tabla comparativa de scores -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h6 mb-3">Evolución de indicadores</h2>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Auditoría</th>
                        <th>Fecha cierre</th>
                        <th class="text-center">Madurez global</th>
                        <th class="text-center">Riesgo global</th>
                        <th class="text-center">Riesgo C</th>
                        <th class="text-center">Riesgo I</th>
                        <th class="text-center">Riesgo D</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audits as $a): ?>
                        <?php
                        $m = (float) $a['maturity_score'];
                        $r = (float) $a['risk_score'];
                        ?>
                        <tr>
                            <td><?= e($a['name']) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($a['end_date']))) ?></td>
                            <td class="text-center">
                                    <span class="badge text-bg-<?= \App\Core\MaturityCalculator::maturityColor($m) ?>">
                                    <?= number_format($m, 2) ?>/5
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge text-bg-<?= \App\Core\MaturityCalculator::riskColor($r) ?>">
                                    <?= number_format($r, 1) ?>%
                                </span>
                            </td>
                            <td class="text-center"><?= number_format((float) $a['risk_c'], 1) ?>%</td>
                            <td class="text-center"><?= number_format((float) $a['risk_i'], 1) ?>%</td>
                            <td class="text-center"><?= number_format((float) $a['risk_d'], 1) ?>%</td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="<?= BASE_URL ?>/audits/report?id=<?= (int) $a['id'] ?>"
                                   target="_blank">Reporte</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Gráfico de evolución de madurez global -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100 comparison-chart">
            <div class="card-body">
                <h2 class="h6 mb-3">Evolución de madurez global</h2>
                <canvas id="evolutionChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100 comparison-chart">
            <div class="card-body">
                <h2 class="h6 mb-3">Evolución del riesgo (C / I / D)</h2>
                <canvas id="riskChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<?php if ($chartData && count($chartData['datasets']) >= 2): ?>
<!-- Gráfico radar de madurez por dominio (comparativo) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h6 mb-3">Comparación de madurez por dominio entre auditorías</h2>
        <canvas id="radarChart" height="120"></canvas>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const auditLabels = <?= json_encode(array_map(fn($a) => date('d/m/Y', strtotime($a['end_date'])), $audits)) ?>;
    const maturities  = <?= json_encode(array_map(fn($a) => (float) $a['maturity_score'], $audits)) ?>;
    const riskC       = <?= json_encode(array_map(fn($a) => (float) $a['risk_c'], $audits)) ?>;
    const riskI       = <?= json_encode(array_map(fn($a) => (float) $a['risk_i'], $audits)) ?>;
    const riskD       = <?= json_encode(array_map(fn($a) => (float) $a['risk_d'], $audits)) ?>;

    // Evolución madurez
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: auditLabels,
            datasets: [{
                label: 'Madurez global',
                data: maturities,
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: { scales: { y: { min: 0, max: 5 } } }
    });

    // Evolución riesgo CID
    new Chart(document.getElementById('riskChart'), {
        type: 'line',
        data: {
            labels: auditLabels,
            datasets: [
                { label: 'Confidencialidad', data: riskC, borderColor: '#0d6efd', tension: 0.3 },
                { label: 'Integridad',       data: riskI, borderColor: '#fd7e14', tension: 0.3 },
                { label: 'Disponibilidad',   data: riskD, borderColor: '#6f42c1', tension: 0.3 },
            ]
        },
        options: { scales: { y: { min: 0, max: 100 } } }
    });

    <?php if ($chartData && count($chartData['datasets']) >= 2): ?>
    // Radar por dominio
    const colors = ['#0d6efd','#198754','#fd7e14','#6f42c1','#dc3545','#0dcaf0'];
    const radarDatasets = <?= json_encode($chartData['datasets']) ?>.map((ds, i) => ({
        label: ds.label,
        data: ds.data,
        borderColor: colors[i % colors.length],
        backgroundColor: colors[i % colors.length] + '22',
    }));
    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: radarDatasets,
        },
        options: { scales: { r: { min: 0, max: 5, ticks: { stepSize: 1 } } } }
    });
    <?php endif; ?>
})();
</script>

<?php endif; ?>

<style>
/* Limit canvas height to avoid pushing content too far down on small viewports */
.comparison-chart canvas { max-height: 320px; width: 100% !important; height: auto !important; }
#radarChart { max-height: 360px; }
</style>
