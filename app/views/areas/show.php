<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted">Área</div>
                <div class="fw-semibold"><?= e($area['name']) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Organización</div>
                <div><?= e($area['organization_name']) ?></div>
            </div>
            <div class="col-md-12">
                <div class="text-muted">Descripción</div>
                <div><?= nl2br(e($area['description'])) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $area['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($area['status']) ?></span>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Fecha de registro</div>
                <div><?= e(date('d/m/Y H:i', strtotime($area['created_at']))) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">�?ltima actualización</div>
                <div><?= e(date('d/m/Y H:i', strtotime($area['updated_at']))) ?></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/areas">Volver</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/areas/edit?id=<?= (int) $area['id'] ?>">Editar</a>
        </div>
    </div>
</div>
