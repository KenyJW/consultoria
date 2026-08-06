<?php use App\Core\Csrf; ?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/organizations">
    <div class="col-md-7">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por nombre, correo o direcciÃ³n">
    </div>
    <div class="col-md-2">
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nombre</option>
            <option value="email" <?= $sort === 'email' ? 'selected' : '' ?>>Correo</option>
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
    <a class="btn btn-primary" href="<?= BASE_URL ?>/organizations/create">Nueva organizaciÃ³n</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Ãreas</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $organization): ?>
                    <tr>
                        <td><?= e($organization['name']) ?></td>
                        <td><?= e($organization['email']) ?></td>
                        <td><?= (int) $organization['areas_count'] ?></td>
                        <td><span class="badge text-bg-<?= $organization['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($organization['status']) ?></span></td>
                        <td><?= e(date('d/m/Y', strtotime($organization['created_at']))) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/organizations/show?id=<?= (int) $organization['id'] ?>">Ver</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/organizations/edit?id=<?= (int) $organization['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/organizations/toggle" data-confirm="Â¿Cambiar estado de esta organizaciÃ³n?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $organization['id'] ?>">
                                <input type="hidden" name="status" value="<?= $organization['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= $organization['status'] === 'active' ? 'Inactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay organizaciones registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
