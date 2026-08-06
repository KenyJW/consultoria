<?php use App\Core\Csrf; ?>
<form class="row g-2 mb-3" method="get" action="<?= BASE_URL ?>/questions">
    <div class="col-md-4">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por texto o codigo de control">
    </div>
    <div class="col-md-3">
        <select class="form-select" name="control_id">
            <option value="0">Todos los controles</option>
            <?php foreach ($controls as $control): ?>
                <option value="<?= (int) $control['id'] ?>" <?= $controlId === (int) $control['id'] ? 'selected' : '' ?>><?= e($control['code'] . ' - ' . $control['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" name="sort">
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Registro</option>
            <option value="question" <?= $sort === 'question' ? 'selected' : '' ?>>Pregunta</option>
            <option value="control_code" <?= $sort === 'control_code' ? 'selected' : '' ?>>Control</option>
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
    <a class="btn btn-primary" href="<?= BASE_URL ?>/questions/create">Nueva pregunta</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Control</th>
                    <th>Pregunta</th>
                    <th>Peso</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagination['items'] as $question): ?>
                    <tr>
                        <td><span class="badge text-bg-light border"><?= e($question['control_code']) ?></span></td>
                        <td><?= e($question['question']) ?></td>
                        <td><?= e(number_format((float) $question['weight'], 2)) ?></td>
                        <td><span class="badge text-bg-<?= $question['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($question['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/questions/show?id=<?= (int) $question['id'] ?>">Ver</a>
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/questions/edit?id=<?= (int) $question['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/questions/toggle" data-confirm="Cambiar estado de esta pregunta?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $question['id'] ?>">
                                <input type="hidden" name="status" value="<?= $question['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= $question['status'] === 'active' ? 'Inactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pagination['items'] === []): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay preguntas registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require dirname(__DIR__) . '/partials/pagination.php'; ?>
    </div>
</div>
