<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <div class="text-muted">Pregunta</div>
                <div class="fw-semibold"><?= e($question['question']) ?></div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Control</div>
                <div><?= e($question['control_code'] . ' - ' . $question['control_title']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Peso</div>
                <div><?= e(number_format((float) $question['weight'], 2)) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Estado</div>
                <span class="badge text-bg-<?= $question['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($question['status']) ?></span>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Fecha de registro</div>
                <div><?= e(date('d/m/Y H:i', strtotime($question['created_at']))) ?></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/questions">Volver</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/questions/edit?id=<?= (int) $question['id'] ?>">Editar</a>
        </div>
    </div>
</div>
