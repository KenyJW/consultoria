<?php use App\Core\Csrf; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= BASE_URL . $action ?>">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <?php if ($audit): ?>
                <input type="hidden" name="id" value="<?= (int) $audit['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label" for="name">Nombre de la auditoria</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? $audit['name'] ?? '') ?>" placeholder="Ej. Auditoria BD - I semestre 2026" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="organization_id">Organizacion</label>
                    <?php $selectedOrg = (int) ($_SESSION['_old']['organization_id'] ?? $audit['organization_id'] ?? 0); ?>
                    <select class="form-select" id="organization_id" name="organization_id" required data-selected-area="<?= (int) ($_SESSION['_old']['area_id'] ?? $audit['area_id'] ?? 0) ?>">
                        <option value="">Seleccione una organizacion</option>
                        <?php foreach ($organizations as $organization): ?>
                            <option value="<?= (int) $organization['id'] ?>" <?= $selectedOrg === (int) $organization['id'] ? 'selected' : '' ?>><?= e($organization['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="area_id">Area evaluada</label>
                    <select class="form-select" id="area_id" name="area_id" required>
                        <option value="">Seleccione primero una organizacion</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="auditor_user_id">Auditor asignado</label>
                    <?php $selectedAuditor = (int) ($_SESSION['_old']['auditor_user_id'] ?? $audit['auditor_user_id'] ?? 0); ?>
                    <select class="form-select" id="auditor_user_id" name="auditor_user_id" required>
                        <option value="">Seleccione un auditor</option>
                        <?php foreach ($auditors as $auditor): ?>
                            <option value="<?= (int) $auditor['id'] ?>" <?= $selectedAuditor === (int) $auditor['id'] ? 'selected' : '' ?>><?= e($auditor['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="dba_name">Administrador de BD (DBA)</label>
                    <input class="form-control" id="dba_name" name="dba_name"
                           value="<?= e($_SESSION['_old']['dba_name'] ?? $audit['dba_name'] ?? '') ?>"
                           placeholder="Nombre del DBA responsable">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="start_date">Fecha de inicio</label>
                    <input class="form-control" id="start_date" name="start_date" type="date" value="<?= e($_SESSION['_old']['start_date'] ?? $audit['start_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="end_date">Fecha de cierre (opcional)</label>
                    <input class="form-control" id="end_date" name="end_date" type="date" value="<?= e($_SESSION['_old']['end_date'] ?? $audit['end_date'] ?? '') ?>">
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/audits">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    const orgSelect = document.getElementById('organization_id');
    const areaSelect = document.getElementById('area_id');
    const preselected = parseInt(orgSelect.getAttribute('data-selected-area') || '0', 10);

    async function loadAreas(preselect) {
        const orgId = parseInt(orgSelect.value || '0', 10);
        areaSelect.innerHTML = '<option value="">Cargando...</option>';
        if (orgId <= 0) {
            areaSelect.innerHTML = '<option value="">Seleccione primero una organizacion</option>';
            return;
        }
        try {
            const res = await fetch('<?= BASE_URL ?>/audits/areas-json?organization_id=' + orgId);
            const areas = await res.json();
            areaSelect.innerHTML = '<option value="">Seleccione un area</option>';
            areas.forEach(function (a) {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.name;
                if (preselect && parseInt(a.id, 10) === preselect) opt.selected = true;
                areaSelect.appendChild(opt);
            });
        } catch (e) {
            areaSelect.innerHTML = '<option value="">Error al cargar areas</option>';
        }
    }

    orgSelect.addEventListener('change', function () { loadAreas(0); });
    if (parseInt(orgSelect.value || '0', 10) > 0) {
        loadAreas(preselected);
    }
})();
</script>
