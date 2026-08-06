<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($domain): ?>
                <input type="hidden" name="id" value="<?= (int) $domain['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="code">Codigo</label>
                    <input class="form-control" id="code" name="code" value="<?= e($_SESSION['_old']['code'] ?? $domain['code'] ?? '') ?>" placeholder="Ej. A.5" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="name">Nombre</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? $domain['name'] ?? '') ?>" placeholder="Ej. Control de acceso" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="description">Descripcion</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= e($_SESSION['_old']['description'] ?? $domain['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $domain['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/domains">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
