<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($question): ?>
                <input type="hidden" name="id" value="<?= (int) $question['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="control_id">Control</label>
                    <?php $selectedControl = (int) ($_SESSION['_old']['control_id'] ?? $question['control_id'] ?? ($controlId ?? 0)); ?>
                    <select class="form-select" id="control_id" name="control_id" required>
                        <option value="">Seleccione un control</option>
                        <?php foreach ($controls as $control): ?>
                            <option value="<?= (int) $control['id'] ?>" <?= $selectedControl === (int) $control['id'] ? 'selected' : '' ?>><?= e($control['code'] . ' - ' . $control['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="weight">Peso de la pregunta</label>
                    <input class="form-control" id="weight" name="weight" type="number" step="0.01" min="0.1" value="<?= e($_SESSION['_old']['weight'] ?? $question['weight'] ?? '1.00') ?>" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label" for="question">Pregunta</label>
                    <textarea class="form-control" id="question" name="question" rows="3" placeholder="Ej. Existe una politica formal de contrasenas?" required><?= e($_SESSION['_old']['question'] ?? $question['question'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $question['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/questions">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
