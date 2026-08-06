<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($control): ?>
                <input type="hidden" name="id" value="<?= (int) $control['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="domain_id">Dominio</label>
                    <?php $selectedDomain = (int) ($_SESSION['_old']['domain_id'] ?? $control['domain_id'] ?? 0); ?>
                    <select class="form-select" id="domain_id" name="domain_id" required>
                        <option value="">Seleccione un dominio</option>
                        <?php foreach ($domains as $domain): ?>
                            <option value="<?= (int) $domain['id'] ?>" <?= $selectedDomain === (int) $domain['id'] ? 'selected' : '' ?>><?= e($domain['code'] . ' - ' . $domain['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="code">Codigo del control</label>
                    <input class="form-control" id="code" name="code" value="<?= e($_SESSION['_old']['code'] ?? $control['code'] ?? '') ?>" placeholder="Ej. A.5.1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="weight">Peso / importancia</label>
                    <input class="form-control" id="weight" name="weight" type="number" step="0.01" min="0.1" value="<?= e($_SESSION['_old']['weight'] ?? $control['weight'] ?? '1.00') ?>" required>
                    <div class="form-text">Valor relativo del control (ej. 1.0 = normal, 2.0 = critico).</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label" for="title">Titulo / nombre del control</label>
                    <input class="form-control" id="title" name="title" value="<?= e($_SESSION['_old']['title'] ?? $control['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="objective">Objetivo del control</label>
                    <textarea class="form-control" id="objective" name="objective" rows="3"><?= e($_SESSION['_old']['objective'] ?? $control['objective'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="description">Descripcion</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= e($_SESSION['_old']['description'] ?? $control['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-8">
                    <label class="form-label d-block">Relacion con la triada CID</label>
                    <?php
                    $c = (int) ($_SESSION['_old']['confidentiality'] ?? $control['confidentiality'] ?? 1);
                    $i = (int) ($_SESSION['_old']['integrity'] ?? $control['integrity'] ?? 1);
                    $a = (int) ($_SESSION['_old']['availability'] ?? $control['availability'] ?? 1);
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="confidentiality" name="confidentiality" value="1" <?= $c === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="confidentiality">Confidencialidad</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="integrity" name="integrity" value="1" <?= $i === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="integrity">Integridad</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="availability" name="availability" value="1" <?= $a === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="availability">Disponibilidad</label>
                    </div>
                    <div class="form-text">Seleccione al menos una dimension afectada por el control.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $control['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/controls">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
