<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted">Codigo</div>
                <div class="fw-semibold"><?= e($domain['code']) ?></div>
            </div>
            <div class="col-md-8">
                <div class="text-muted">Nombre</div>
                <div class="fw-semibold"><?= e($domain['name']) ?></div>
            </div>
            <div class="col-md-12">
                <div class="text-muted">Descripcion</div>
                <div><?= nl2br(e($domain['description'] ?? '')) ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Controles asociados</div>
                <div><?= (int) $domain['controls_count'] ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $domain['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($domain['status']) ?></span>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Fecha de registro</div>
                <div><?= e(date('d/m/Y H:i', strtotime($domain['created_at']))) ?></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/domains">Volver</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/domains/edit?id=<?= (int) $domain['id'] ?>">Editar</a>
        </div>
    </div>
</div>
