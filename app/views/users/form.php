<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (! empty($_SESSION['_errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['_errors'] as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($user): ?>
                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Nombre</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? $user['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Correo</label>
                    <input class="form-control" id="email" name="email" type="email" value="<?= e($_SESSION['_old']['email'] ?? $user['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="password">Contraseña</label>
                    <input class="form-control" id="password" name="password" type="password" <?= $user ? '' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="role">Rol</label>
                    <select class="form-select" id="role" name="role">
                        <?php $selectedRole = $_SESSION['_old']['role'] ?? $user['role'] ?? 'auditor'; ?>
                        <?php if (! $scoped): ?>
                            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <?php endif; ?>
                        <option value="auditor" <?= $selectedRole === 'auditor' ? 'selected' : '' ?>>Auditor</option>
                        <option value="viewer" <?= $selectedRole === 'viewer' ? 'selected' : '' ?>>Consulta</option>
                    </select>
                    <?php if ($scoped): ?>
                        <div class="form-text">Queda en su misma organización.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Estado</label>
                    <?php $selectedStatus = $_SESSION['_old']['status'] ?? $user['status'] ?? 'active'; ?>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php if (! $scoped): ?>
                    <div class="col-md-6">
                        <label class="form-label" for="organization_id">Organización</label>
                        <?php $selectedOrg = (int) ($_SESSION['_old']['organization_id'] ?? $user['organization_id'] ?? 0); ?>
                        <select class="form-select" id="organization_id" name="organization_id">
                            <option value="0" <?= $selectedOrg === 0 ? 'selected' : '' ?>>Sin organización (personal de la consultora)</option>
                            <?php foreach ($organizations as $organization): ?>
                                <option value="<?= (int) $organization['id'] ?>" <?= $selectedOrg === (int) $organization['id'] ? 'selected' : '' ?>><?= e($organization['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            Sin organización = auditor/administrador de la consultora, se puede asignar a cualquier empresa.
                            Con organización = queda restringido a ver y trabajar solo esa empresa.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/users">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
