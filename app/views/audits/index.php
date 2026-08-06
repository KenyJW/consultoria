<?php use App\Core\Csrf; ?>
<?php
$statusLabels = ['draft' => 'Borrador', 'in_progress' => 'En progreso', 'closed' => 'Cerrada'];
$statusBadge = ['draft' => 'secondary', 'in_progress' => 'warning', 'closed' => 'success'];
?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/audits">
    <div class="col-md-3">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por nombre u organizacion">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="organization_id">
            <option value="0">Todas las organizaciones</option>
            <?php foreach ($organizations as $organization): ?>
                <option value="<?= (int) $organization['id'] ?>" <?= $organizationId === (int) $organization['id'] ? 'selected' : '' ?>><?= e($organization['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="status">
            <option value="">Todos los estados</option>
            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Borrador</option>
            <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
            <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Cerrada</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nombre</option>
            <option value="start_date" <?= $sort === 'start_date' ? 'selected' : '' ?>>Inicio</option>
            <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Estado</option>
        </select>
    </div>
    <div class="col-md-1">
        <select class="form-select" name="direction">
            <option value="desc" <?= $direction === 'desc' ? 'selected' : '' ?>>Desc</option>
            <option value="asc" <?= $direction === 'asc' ? 'selected' : '' ?>>Asc</option>
        </select>
    </div>
    <div class="col-md-1 d-grid">
        <button class="btn btn-outline-primary" type="submit">Buscar</button>
    </div>
</form>
<div class="d-flex justify-content-end mb-3">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/audits/create">Nueva auditoria</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Auditoria</th>
                    <th>Organizacion</th>
                    <th>Area</th>
                    <th>Auditor</th>
                    <th>Inicio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $audit): ?>
                    <tr>
                        <td><?= e($audit['name']) ?></td>
                        <td><?= e($audit['organization_name']) ?></td>
                        <td><?= e($audit['area_name']) ?></td>
                        <td><?= e($audit['auditor_name']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($audit['start_date']))) ?></td>
                        <td><span class="badge text-bg-<?= $statusBadge[$audit['status']] ?? 'secondary' ?>"><?= e($statusLabels[$audit['status']] ?? $audit['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/audits/show?id=<?= (int) $audit['id'] ?>">Ver</a>
                            <?php if ($audit['status'] !== 'closed'): ?>
                                <a class="btn btn-sm btn-outline-success" href="<?= BASE_URL ?>/audits/run?id=<?= (int) $audit['id'] ?>">Responder</a>
                                <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/audits/edit?id=<?= (int) $audit['id'] ?>">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay auditorias registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
