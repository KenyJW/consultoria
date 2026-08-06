<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($organization): ?>
                <input type="hidden" name="id" value="<?= (int) $organization['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Nombre</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? $organization['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Correo</label>
                    <input class="form-control" id="email" name="email" type="email" value="<?= e($_SESSION['_old']['email'] ?? $organization['email'] ?? '') ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="address">DirecciÃ³n</label>
                    <input class="form-control" id="address" name="address" value="<?= e($_SESSION['_old']['address'] ?? $organization['address'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $organization['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/organizations">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
