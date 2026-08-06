<?php use App\Core\Csrf; ?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/controls">
    <div class="col-md-4">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por codigo o titulo">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="domain_id">
            <option value="0">Todos los dominios</option>
            <?php foreach ($domains as $domain): ?>
                <option value="<?= (int) $domain['id'] ?>" <?= $domainId === (int) $domain['id'] ? 'selected' : '' ?>><?= e($domain['code'] . ' - ' . $domain['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="code" <?= $sort === 'code' ? 'selected' : '' ?>>Codigo</option>
            <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Titulo</option>
            <option value="domain_name" <?= $sort === 'domain_name' ? 'selected' : '' ?>>Dominio</option>
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
    <a class="btn btn-primary" href="<?= BASE_URL ?>/controls/create">Nuevo control</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Titulo</th>
                    <th>Dominio</th>
                    <th>Peso</th>
                    <th>C/I/D</th>
                    <th>Preguntas</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $control): ?>
                    <tr>
                        <td><span class="badge text-bg-light border"><?= e($control['code']) ?></span></td>
                        <td><?= e($control['title']) ?></td>
                        <td><?= e($control['domain_code'] ?? '-') ?></td>
                        <td><?= e(number_format((float) $control['weight'], 2)) ?></td>
                        <td>
                            <?php if ((int) $control['confidentiality'] === 1): ?><span class="badge text-bg-primary">C</span><?php endif; ?>
                            <?php if ((int) $control['integrity'] === 1): ?><span class="badge text-bg-info">I</span><?php endif; ?>
                            <?php if ((int) $control['availability'] === 1): ?><span class="badge text-bg-success">D</span><?php endif; ?>
                        </td>
                        <td><?= (int) $control['questions_count'] ?></td>
                        <td><span class="badge text-bg-<?= $control['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($control['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/controls/show?id=<?= (int) $control['id'] ?>">Ver</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/controls/edit?id=<?= (int) $control['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/controls/toggle" data-confirm="Cambiar estado de este control?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $control['id'] ?>">
                                <input type="hidden" name="status" value="<?= $control['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= $control['status'] === 'active' ? 'Inactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay controles registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
