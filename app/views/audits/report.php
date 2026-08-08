<?php
use App\Core\MaturityCalculator;

$maturity = (float) ($audit['maturity_score'] ?? 0);
$riskG    = (float) ($audit['risk_score'] ?? 0);
$riskC    = (float) ($audit['risk_c'] ?? 0);
$riskI    = (float) ($audit['risk_i'] ?? 0);
$riskD    = (float) ($audit['risk_d'] ?? 0);

// Estadísticas generales
$totalControls  = count($calc['by_control']);
$avgCompliance  = $totalControls > 0
    ? round(array_sum(array_column($calc['by_control'], 'compliance')) / $totalControls, 1)
    : 0.0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Ejecutivo – <?= e($audit['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
            .page-break { page-break-before: always; }
        }
        body { padding: 2rem; }
    </style>
</head>
<body>

<div class="no-print mb-3 d-flex gap-2">
    <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / Guardar PDF</button>
    <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/audits/show?id=<?= (int) $audit['id'] ?>">Volver</a>
</div>

<!-- Encabezado -->
<div class="border-bottom pb-3 mb-4">
    <h1 class="h4 mb-1">Reporte Ejecutivo de Auditoría ISO/IEC 27002</h1>
    <div class="text-muted small">Generado el <?= date('d/m/Y H:i') ?></div>
</div>

<!-- 1. Datos generales -->
<h2 class="h6 fw-bold mb-2">1. Datos generales</h2>
<div class="row g-2 mb-4 small">
    <div class="col-6"><strong>Auditoría:</strong> <?= e($audit['name']) ?></div>
    <div class="col-6"><strong>Organización:</strong> <?= e($audit['organization_name']) ?></div>
    <div class="col-6"><strong>Área evaluada:</strong> <?= e($audit['area_name']) ?></div>
    <div class="col-6"><strong>Auditor:</strong> <?= e($audit['auditor_name']) ?></div>
    <div class="col-6"><strong>DBA responsable:</strong> <?= e($audit['dba_name'] ?? '—') ?></div>
    <div class="col-3"><strong>Inicio:</strong> <?= e(date('d/m/Y', strtotime($audit['start_date']))) ?></div>
    <div class="col-3"><strong>Cierre:</strong> <?= $audit['end_date'] ? e(date('d/m/Y', strtotime($audit['end_date']))) : '—' ?></div>
</div>

<!-- 2. Resultados generales -->
<h2 class="h6 fw-bold mb-2">2. Resultados generales</h2>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-<?= MaturityCalculator::maturityColor($maturity) ?> text-center">
            <div class="card-body py-2">
                <div class="small text-muted">Madurez global</div>
                <div class="h4 fw-bold text-<?= MaturityCalculator::maturityColor($maturity) ?> mb-0">
                    <?= number_format($maturity, 2) ?>/5
                </div>
                <span class="badge text-bg-<?= MaturityCalculator::maturityColor($maturity) ?>">
                    <?= MaturityCalculator::maturityLabel($maturity) ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-<?= MaturityCalculator::riskColor($riskG) ?> text-center">
            <div class="card-body py-2">
                <div class="small text-muted">Riesgo global</div>
                <div class="h4 fw-bold text-<?= MaturityCalculator::riskColor($riskG) ?> mb-0">
                    <?= number_format($riskG, 1) ?>%
                </div>
                <span class="badge text-bg-<?= MaturityCalculator::riskColor($riskG) ?>">
                    <?= MaturityCalculator::riskLabel($riskG) ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-secondary text-center">
            <div class="card-body py-2">
                <div class="small text-muted">Controles</div>
                <div class="h4 fw-bold mb-0"><?= $totalControls ?></div>
                <div class="small text-muted">evaluados</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-secondary text-center">
            <div class="card-body py-2">
                <div class="small text-muted">Cumplimiento</div>
                <div class="h4 fw-bold mb-0"><?= $avgCompliance ?>%</div>
                <div class="small text-muted">promedio</div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Exposición al riesgo por dimensión CID -->
<h2 class="h6 fw-bold mb-2">3. Exposición al riesgo por dimensión</h2>
<div class="row g-3 mb-4">
    <?php foreach (['Confidencialidad' => $riskC, 'Integridad' => $riskI, 'Disponibilidad' => $riskD] as $dim => $val): ?>
        <div class="col-md-4">
            <div class="card border-<?= MaturityCalculator::riskColor($val) ?>">
                <div class="card-body py-2 text-center">
                    <div class="small text-muted"><?= $dim ?></div>
                    <div class="h4 fw-bold text-<?= MaturityCalculator::riskColor($val) ?> mb-0">
                        <?= number_format($val, 1) ?>%
                    </div>
                    <div class="progress mt-1" style="height:8px;">
                        <div class="progress-bar bg-<?= MaturityCalculator::riskColor($val) ?>"
                             style="width:<?= min(100, $val) ?>%"></div>
                    </div>
                    <span class="badge text-bg-<?= MaturityCalculator::riskColor($val) ?> mt-1">
                        <?= MaturityCalculator::riskLabel($val) ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- 4. Resultados por dominio (heatmap) -->
<h2 class="h6 fw-bold mb-2">4. Resultados por dominio</h2>
<?php if (! empty($maturityDomain)): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <h3 class="h6 mb-3">Mapa de calor — Madurez por dominio</h3>
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
                        <?php foreach ($maturityDomain as $d): $m = (float) $d['avg_maturity']; $cell = (int) round($m); ?>
                            <tr>
                                <td class="text-start text-nowrap">
                                    <span class="badge bg-secondary me-1"><?= e($d['domain_code']) ?></span>
                                    <?= e($d['domain_name']) ?>
                                </td>
                                <?php for ($lvl = 0; $lvl <= 5; $lvl++):
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
        </div>
    </div>
<?php endif; ?>
<table class="table table-sm table-bordered mb-4">
    <thead class="table-light">
        <tr>
            <th>Dominio</th>
            <th class="text-center">Madurez</th>
            <th class="text-center">Nivel</th>
            <th class="text-center">Cumplimiento</th>
            <th class="text-center">Riesgo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controlRows as $cr):
            $domId = (int) $cr['domain_id'];
            if (! isset($shownDomains[$domId])):
                $shownDomains[$domId] = true;
                $dm = (float) ($calc['by_domain'][$domId]['maturity'] ?? 0);
                $dc = (float) ($calc['by_domain'][$domId]['compliance'] ?? 0);
                $dr = (float) ($calc['by_domain'][$domId]['risk'] ?? 0);
        ?>
            <tr>
                <td><span class="badge bg-secondary me-1"><?= e($cr['domain_code']) ?></span><?= e($cr['domain_name']) ?></td>
                <td class="text-center">
                    <div class="progress" style="height:16px;">
                        <div class="progress-bar bg-<?= MaturityCalculator::maturityColor($dm) ?>"
                             style="width:<?= min(100, $dm/5*100) ?>%"><?= number_format($dm,2) ?></div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::maturityColor($dm) ?>">
                        <?= MaturityCalculator::maturityLabel($dm) ?>
                    </span>
                </td>
                <td class="text-center"><?= number_format($dc, 1) ?>%</td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::riskColor($dr) ?>">
                        <?= number_format($dr, 2) ?>
                    </span>
                </td>
            </tr>
        <?php endif; endforeach; ?>
    </tbody>
</table>

<!-- 5. Controles con menor nivel de madurez -->
<h2 class="h6 fw-bold mb-2">5. Controles con menor nivel de madurez</h2>
<table class="table table-sm table-bordered mb-4">
    <thead class="table-light">
        <tr><th>Control</th><th class="text-center">Madurez</th><th class="text-center">Cumplimiento</th></tr>
    </thead>
    <tbody>
        <?php foreach ($lowestMaturity as $cid => $bc): ?>
            <tr>
                <td><?= e($controlNames[$cid] ?? 'Control ' . $cid) ?></td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::maturityColor((float)$bc['maturity']) ?>">
                        <?= $bc['maturity'] ?>/5
                    </span>
                </td>
                <td class="text-center"><?= $bc['compliance'] ?>%</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 6. Controles con mayor exposición al riesgo -->
<h2 class="h6 fw-bold mb-2">6. Controles con mayor exposición al riesgo</h2>
<table class="table table-sm table-bordered mb-4">
    <thead class="table-light">
        <tr><th>Control</th><th class="text-center">Riesgo</th><th class="text-center">Madurez</th></tr>
    </thead>
    <tbody>
        <?php foreach ($highestRisk as $cid => $bc): ?>
            <tr>
                <td><?= e($controlNames[$cid] ?? 'Control ' . $cid) ?></td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::riskColor((float)$bc['risk']) ?>">
                        <?= number_format((float)$bc['risk'], 2) ?>
                    </span>
                </td>
                <td class="text-center"><?= $bc['maturity'] ?>/5</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 7. Indicadores estadísticos -->
<h2 class="h6 fw-bold mb-2">7. Indicadores estadísticos</h2>
<?php
$maturities   = array_column($calc['by_control'], 'maturity');
$compliances  = array_column($calc['by_control'], 'compliance');
$risks        = array_column($calc['by_control'], 'risk');
$countBelow2  = count(array_filter($maturities, fn($m) => $m < 2));
$countAbove3  = count(array_filter($maturities, fn($m) => $m >= 3));
?>
<div class="row g-2 mb-4 small">
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Madurez mínima</div><strong><?= min($maturities ?: [0]) ?>/5</strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Madurez máxima</div><strong><?= max($maturities ?: [0]) ?>/5</strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Controles madurez &lt; 2</div><strong><?= $countBelow2 ?></strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Controles madurez ≥ 3</div><strong><?= $countAbove3 ?></strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Cumplimiento mínimo</div><strong><?= min($compliances ?: [0]) ?>%</strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Cumplimiento máximo</div><strong><?= max($compliances ?: [0]) ?>%</strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Riesgo mínimo</div><strong><?= number_format(min($risks ?: [0]), 2) ?></strong></div></div>
    <div class="col-md-3"><div class="card p-2 text-center"><div class="text-muted">Riesgo máximo</div><strong><?= number_format(max($risks ?: [0]), 2) ?></strong></div></div>
</div>

<!-- 8. Resultados por control -->
<div class="page-break"></div>
<h2 class="h6 fw-bold mb-2">8. Resultados por control</h2>
<table class="table table-sm table-bordered mb-4">
    <thead class="table-light">
        <tr>
            <th>Control</th>
            <th class="text-center">Madurez</th>
            <th class="text-center">Cumplimiento</th>
            <th class="text-center">Riesgo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controlRows as $cr):
            $cid = (int) $cr['control_id'];
            $bc  = $calc['by_control'][$cid] ?? null;
            if (! $bc) continue;
        ?>
            <tr>
                <td><span class="badge bg-light text-dark border me-1"><?= e($cr['control_code']) ?></span><?= e($cr['control_title']) ?></td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::maturityColor((float)$bc['maturity']) ?>">
                        <?= $bc['maturity'] ?>/5
                    </span>
                </td>
                <td class="text-center"><?= $bc['compliance'] ?>%</td>
                <td class="text-center">
                    <span class="badge text-bg-<?= MaturityCalculator::riskColor((float)$bc['risk']) ?>">
                        <?= number_format((float)$bc['risk'], 2) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 9. Detalle del cuestionario -->
<div class="page-break"></div>
<h2 class="h6 fw-bold mb-3">9. Detalle del cuestionario</h2>
<?php foreach ($groups as $domain): ?>
    <h3 class="h6 mt-3 mb-2 text-secondary">
        <?= e($domain['domain_code']) ?> — <?= e($domain['domain_name']) ?>
    </h3>
    <?php foreach ($domain['controls'] as $control): ?>
        <p class="mb-1 small"><strong><?= e($control['control_code']) ?>:</strong> <?= e($control['control_title']) ?></p>
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light">
                <tr>
                    <th>Pregunta</th>
                    <th class="text-center" style="width:70px">Respuesta</th>
                    <th class="text-center" style="width:60px">Madurez</th>
                    <th>Justificación del nivel de madurez</th>
                    <th>Recomendación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($control['questions'] as $q): ?>
                    <?php
                    $ansLabels = ['yes' => '✅ Sí', 'no' => '❌ No', 'na' => '➖ N/A'];
                    $ans = $q['answer'] ?? null;
                    $mat = $q['maturity_level'] ?? null;
                    ?>
                    <tr>
                        <td class="small"><?= e($q['question_text']) ?></td>
                        <td class="text-center small"><?= $ans ? $ansLabels[$ans] : '—' ?></td>
                        <td class="text-center small"><?= $mat !== null ? (int) $mat . '/5' : '—' ?></td>
                        <td class="small"><?= e($q['justification'] ?? '—') ?></td>
                        <td class="small"><?= e($q['recommendation'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- Gráfico de madurez por dominio -->
<div class="page-break"></div>
<h2 class="h6 fw-bold mb-2">10. Gráfico de madurez por dominio</h2>
<canvas id="reportChart" height="120" class="mb-4"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    <?php
    $domainLabels = [];
    $domainData   = [];
    $shownD = [];
    foreach ($controlRows as $cr) {
        $did = (int) $cr['domain_id'];
        if (isset($shownD[$did])) continue;
        $shownD[$did] = true;
        $domainLabels[] = $cr['domain_code'];
        $domainData[]   = (float) ($calc['by_domain'][$did]['maturity'] ?? 0);
    }
    ?>
    const labels = <?= json_encode($domainLabels) ?>;
    const data   = <?= json_encode($domainData) ?>;
    const colors = data.map(v => v < 2 ? '#dc3545' : v < 3 ? '#ffc107' : v < 4 ? '#0dcaf0' : '#198754');
    new Chart(document.getElementById('reportChart'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Madurez', data, backgroundColor: colors, borderRadius: 4 }] },
        options: {
            scales: { y: { min: 0, max: 5, ticks: { stepSize: 1 } } },
            plugins: { legend: { display: false } },
        }
    });
})();
</script>

</body>
</html>
