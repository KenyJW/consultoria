<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted">Codigo</div>
                <div class="fw-semibold"><?= e($control['code']) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Titulo</div>
                <div class="fw-semibold"><?= e($control['title']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Dominio</div>
                <div><?= e(($control['domain_code'] ?? '') . ' ' . ($control['domain_name'] ?? '-')) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Objetivo</div>
                <div><?= nl2br(e($control['objective'] ?? '')) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Descripcion</div>
                <div><?= nl2br(e($control['description'] ?? '')) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Peso</div>
                <div><?= e(number_format((float) $control['weight'], 2)) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Relacion CID</div>
                <div>
                    <?php if ((int) $control['confidentiality'] === 1): ?><span class="badge text-bg-primary">Confidencialidad</span><?php endif; ?>
                    <?php if ((int) $control['integrity'] === 1): ?><span class="badge text-bg-info">Integridad</span><?php endif; ?>
                    <?php if ((int) $control['availability'] === 1): ?><span class="badge text-bg-success">Disponibilidad</span><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Preguntas</div>
                <div><?= (int) $control['questions_count'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $control['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($control['status']) ?></span>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/controls">Volver</a>
            <a class="btn btn-outline-info" href="<?= BASE_URL ?>/questions?control_id=<?= (int) $control['id'] ?>">Ver preguntas</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/controls/edit?id=<?= (int) $control['id'] ?>">Editar</a>
        </div>
    </div>
</div>
