<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted">Nombre</div>
                <div class="fw-semibold"><?= e($organization['name']) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Correo</div>
                <div><?= e($organization['email']) ?></div>
            </div>
            <div class="col-md-8">
                <div class="text-muted">DirecciÃ³n</div>
                <div><?= e($organization['address']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Ãreas asociadas</div>
                <div><?= (int) $organization['areas_count'] ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $organization['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($organization['status']) ?></span>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Fecha de registro</div>
                <div><?= e(date('d/m/Y H:i', strtotime($organization['created_at']))) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Ãšltima actualizaciÃ³n</div>
                <div><?= e(date('d/m/Y H:i', strtotime($organization['updated_at']))) ?></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/organizations">Volver</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/organizations/edit?id=<?= (int) $organization['id'] ?>">Editar</a>
        </div>
    </div>
</div>
