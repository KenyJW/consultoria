<?php use App\Core\Csrf; ?>
<?php
$statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En progreso', 'done' => 'Completada'];
$statusBadge  = ['pending' => 'warning',   'in_progress' => 'info',        'done' => 'success'];
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small">Pendientes</div>
                <div class="display-6 fw-bold text-warning"><?= (int) ($counts['pending'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small">En progreso</div>
                <div class="display-6 fw-bold text-info"><?= (int) ($counts['in_progress'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-muted small">Completadas</div>
                <div class="display-6 fw-bold text-success"><?= (int) ($counts['done'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($items)): ?>
            <p class="text-muted text-center py-4">No hay recomendaciones pendientes.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Auditoría / Organización</th>
                            <th>Control</th>
                            <th>Descripción</th>
                            <th>Responsable</th>
                            <th>Vence</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php $vencido = $item['due_date'] && $item['due_date'] < date('Y-m-d'); ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($item['audit_name']) ?></div>
                                    <div class="text-muted small"><?= e($item['organization_name']) ?></div>
                                </td>
                                <td><span class="badge bg-secondary"><?= e($item['control_code']) ?></span></td>
                                <td class="small"><?= e(mb_substr($item['description'], 0, 80)) ?><?= mb_strlen($item['description']) > 80 ? '…' : '' ?></td>
                                <td class="small"><?= e($item['responsible'] ?? '—') ?></td>
                                <td class="small <?= $vencido ? 'text-danger fw-bold' : '' ?>">
                                    <?= $item['due_date'] ? e(date('d/m/Y', strtotime($item['due_date']))) : '—' ?>
                                    <?= $vencido ? ' ⚠️' : '' ?>
                                </td>
                                <td>
                                    <span class="badge text-bg-<?= $statusBadge[$item['status']] ?? 'secondary' ?>">
                                        <?= $statusLabels[$item['status']] ?? $item['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="<?= BASE_URL ?>/recommendations/audit?audit_id=<?= (int) $item['audit_id'] ?>">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
