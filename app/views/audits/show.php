<?php use App\Core\Csrf;
use App\Core\MaturityCalculator; ?>
<?php
$statusLabels = ['draft' => 'Borrador', 'in_progress' => 'En progreso', 'closed' => 'Cerrada'];
$statusBadge = ['draft' => 'secondary', 'in_progress' => 'warning', 'closed' => 'success'];
?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted">Auditoria</div>
                <div class="fw-semibold"><?= e($audit['name']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $statusBadge[$audit['status']] ?? 'secondary' ?>"><?= e($statusLabels[$audit['status']] ?? $audit['status']) ?></span>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Auditor</div>
                <div><?= e($audit['auditor_name']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Organizacion</div>
                <div><?= e($audit['organization_name']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Area evaluada</div>
                <div><?= e($audit['area_name']) ?></div>
            </div>
            <div class="col-md-2">
                <div class="text-muted">Inicio</div>
                <div><?= e(date('d/m/Y', strtotime($audit['start_date']))) ?></div>
            </div>
            <div class="col-md-2">
                <div class="text-muted">Cierre</div>
                <div><?= $audit['end_date'] ? e(date('d/m/Y', strtotime($audit['end_date']))) : '-' ?></div>
            </div>
            <div class="col-md-12">
                <div class="text-muted mb-1">Avance del cuestionario (<?= (int) $progress['answered'] ?> / <?= (int) $progress['total'] ?>)</div>
                <div class="progress" role="progressbar" style="height: 22px;">
                    <div class="progress-bar" style="width: <?= (int) $progress['percent'] ?>%;"><?= (int) $progress['percent'] ?>%</div>
                </div>
            </div>
            <?php if ($audit['status'] === 'closed'): ?>
                <div class="col-md-4">
                    <div class="text-muted">Madurez promedio</div>
                    <?php if ($audit['maturity_score'] !== null): ?>
                        <div class="h5 mb-0 text-<?= MaturityCalculator::maturityColor((float) $audit['maturity_score']) ?>">
                            <?= e(number_format((float) $audit['maturity_score'], 2)) ?>/5
                        </div>
                        <div class="small text-muted"><?= MaturityCalculator::maturityLabel((float) $audit['maturity_score']) ?></div>
                    <?php else: ?>
                        <div class="h5 mb-0 text-muted">Pendiente</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Índice de riesgo</div>
                    <?php if ($audit['risk_score'] !== null): ?>
                        <div class="h5 mb-0 text-<?= MaturityCalculator::riskColor((float) $audit['risk_score']) ?>">
                            <?= e(number_format((float) $audit['risk_score'], 1)) ?>%
                        </div>
                    <?php else: ?>
                        <div class="h5 mb-0 text-muted">Pendiente</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/audits">Volver</a>
            <?php if ($audit['status'] !== 'closed'): ?>
                <a class="btn btn-primary" href="<?= BASE_URL ?>/audits/run?id=<?= (int) $audit['id'] ?>">Continuar cuestionario</a>
            <?php else: ?>
                <form method="post" action="<?= BASE_URL ?>/audits/reopen" data-confirm="Reabrir esta auditoria para edicion?">
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                    <input type="hidden" name="id" value="<?= (int) $audit['id'] ?>">
                    <button class="btn btn-outline-warning" type="submit">Reabrir</button>
                </form>
                <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/audits/report?id=<?= (int) $audit['id'] ?>" target="_blank">Ver reporte</a>
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/recommendations/audit?audit_id=<?= (int) $audit['id'] ?>">Recomendaciones</a>
                <a class="btn btn-outline-info" href="<?= BASE_URL ?>/comparison?organization_id=<?= (int) $audit['organization_id'] ?>">Comparar historial</a>
            <?php endif; ?>
        </div>
    </div>
</div>
