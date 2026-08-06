<?php use App\Core\Csrf; ?>
<?php
$statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En progreso', 'done' => 'Completada'];
$statusBadge  = ['pending' => 'warning',   'in_progress' => 'info',        'done' => 'success'];
?>

<div class="mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/audits/show?id=<?= (int) $audit['id'] ?>">
        &larr; Volver a la auditoría
    </a>
    <a class="btn btn-outline-secondary btn-sm ms-1" href="<?= BASE_URL ?>/recommendations">
        Ver todas las recomendaciones
    </a>
</div>

<!-- Formulario nueva recomendación -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Nueva recomendación</div>
    <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/recommendations/store" class="row g-2">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <input type="hidden" name="audit_id" value="<?= (int) $audit['id'] ?>">
            <div class="col-md-3">
                <label class="form-label small">Control</label>
                <select class="form-select form-select-sm" name="control_id" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($controls as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['code'] . ' — ' . mb_substr($c['title'], 0, 50)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Descripción</label>
                <input class="form-control form-control-sm" name="description" required placeholder="Descripción de la recomendación">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Responsable</label>
                <input class="form-control form-control-sm" name="responsible" placeholder="Nombre">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Fecha límite</label>
                <input class="form-control form-control-sm" type="date" name="due_date">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100" type="submit">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de recomendaciones -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($items)): ?>
            <p class="text-muted text-center py-3">No hay recomendaciones registradas para esta auditoría.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <?php $vencido = $item['due_date'] && $item['due_date'] < date('Y-m-d') && $item['status'] !== 'done'; ?>
                <div class="border rounded p-3 mb-3 <?= $vencido ? 'border-danger' : '' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-secondary me-1"><?= e($item['control_code']) ?></span>
                            <span class="small text-muted"><?= e($item['control_title']) ?></span>
                        </div>
                        <span class="badge text-bg-<?= $statusBadge[$item['status']] ?>">
                            <?= $statusLabels[$item['status']] ?>
                        </span>
                    </div>
                    <p class="mb-2"><?= e($item['description']) ?></p>

                    <!-- Formulario de actualización inline -->
                    <form method="post" action="<?= BASE_URL ?>/recommendations/update" class="row g-2 align-items-end">
                        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                        <input type="hidden" name="description" value="<?= e($item['description']) ?>">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Responsable</label>
                            <input class="form-control form-control-sm" name="responsible"
                                   value="<?= e($item['responsible'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Fecha límite <?= $vencido ? '⚠️' : '' ?></label>
                            <input class="form-control form-control-sm <?= $vencido ? 'border-danger' : '' ?>"
                                   type="date" name="due_date"
                                   value="<?= e($item['due_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Estado</label>
                            <select class="form-select form-select-sm" name="status">
                                <?php foreach ($statusLabels as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $item['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Notas de seguimiento</label>
                            <input class="form-control form-control-sm" name="notes"
                                   value="<?= e($item['notes'] ?? '') ?>">
                        </div>
                        <div class="col-md-1 d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" type="submit">✓</button>
                        </div>
                    </form>

                    <?php if ($item['notes']): ?>
                        <div class="mt-2 small text-muted"><em>Nota: <?= e($item['notes']) ?></em></div>
                    <?php endif; ?>

                    <!-- Eliminar -->
                    <form method="post" action="<?= BASE_URL ?>/recommendations/delete"
                          class="mt-2" data-confirm="Eliminar esta recomendación?">
                        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
