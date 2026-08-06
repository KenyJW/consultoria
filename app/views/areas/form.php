<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($area): ?>
                <input type="hidden" name="id" value="<?= (int) $area['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="organization_id">OrganizaciÃ³n</label>
                    <?php $selectedOrganization = (int) ($_SESSION['_old']['organization_id'] ?? $area['organization_id'] ?? 0); ?>
                    <select class="form-select" id="organization_id" name="organization_id" required>
                        <option value="">Seleccione una organizaciÃ³n</option>
                        <?php foreach ($organizations as $organization): ?>
                            <option value="<?= (int) $organization['id'] ?>" <?= $selectedOrganization === (int) $organization['id'] ? 'selected' : '' ?>><?= e($organization['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="name">Nombre</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? $area['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="description">DescripciÃ³n</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= e($_SESSION['_old']['description'] ?? $area['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $area['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/areas">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
