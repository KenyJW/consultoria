<?php use App\Core\Csrf; ?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/domains">
    <div class="col-md-5">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por codigo, nombre o descripcion">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="code" <?= $sort === 'code' ? 'selected' : '' ?>>Codigo</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nombre</option>
            <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Estado</option>
        </select>
    </div>
    <div class="col-md-3">
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
    <a class="btn btn-primary" href="<?= BASE_URL ?>/domains/create">Nuevo dominio</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Controles</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $domain): ?>
                    <tr>
                        <td><span class="badge text-bg-light border"><?= e($domain['code']) ?></span></td>
                        <td><?= e($domain['name']) ?></td>
                        <td><?= (int) $domain['controls_count'] ?></td>
                        <td><span class="badge text-bg-<?= $domain['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($domain['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/domains/show?id=<?= (int) $domain['id'] ?>">Ver</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/domains/edit?id=<?= (int) $domain['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/domains/toggle" data-confirm="Cambiar estado de este dominio?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $domain['id'] ?>">
                                <input type="hidden" name="status" value="<?= $domain['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= $domain['status'] === 'active' ? 'Inactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay dominios registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
