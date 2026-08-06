<?php use App\Core\Csrf; ?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/areas">
    <div class="col-md-4">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por Ã¡rea u organizaciÃ³n">
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
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Ãrea</option>
            <option value="organization_name" <?= $sort === 'organization_name' ? 'selected' : '' ?>>OrganizaciÃ³n</option>
            <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Estado</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="direction">
            <option value="desc" <?= $direction === 'desc' ? 'selected' : '' ?>>Descendente</option>
            <option value="asc" <?= $direction === 'asc' ? 'selected' : '' ?>>Ascendente</option>
        </select>
    </div>
    <div class="col-md-1 d-grid">
        <button class="btn btn-outline-primary" type="submit">Buscar</button>
    </div>
</form>
<div class="d-flex justify-content-end mb-3">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/areas/create">Nueva Ã¡rea</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Ãrea</th>
                    <th>OrganizaciÃ³n</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $area): ?>
                    <tr>
                        <td><?= e($area['name']) ?></td>
                        <td><?= e($area['organization_name']) ?></td>
                        <td><span class="badge text-bg-<?= $area['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($area['status']) ?></span></td>
                        <td><?= e(date('d/m/Y', strtotime($area['created_at']))) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/areas/show?id=<?= (int) $area['id'] ?>">Ver</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/areas/edit?id=<?= (int) $area['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/areas/toggle" data-confirm="Â¿Cambiar estado de esta Ã¡rea?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $area['id'] ?>">
                                <input type="hidden" name="status" value="<?= $area['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= $area['status'] === 'active' ? 'Inactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay Ã¡reas registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
