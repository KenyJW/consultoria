<?php
use App\Core\Csrf;

$maturityShortLabels = [0 => 'No existe', 1 => 'Informal', 2 => 'Parcial', 3 => 'Definido', 4 => 'Gestionado', 5 => 'Optimizado'];
?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="text-muted">Organizacion / Area</div>
            <div class="fw-semibold"><?= e($audit['organization_name']) ?> &middot; <?= e($audit['area_name']) ?></div>
            <?php if ($audit['dba_name']): ?>
                <div class="small text-muted">DBA: <?= e($audit['dba_name']) ?></div>
            <?php endif; ?>
        </div>
        <div style="min-width: 240px;">
            <div class="text-muted mb-1">Avance (<?= (int) $progress['answered'] ?> / <?= (int) $progress['total'] ?>)</div>
            <div class="progress" role="progressbar" style="height: 20px;">
                <div class="progress-bar" style="width: <?= (int) $progress['percent'] ?>%;"><?= (int) $progress['percent'] ?>%</div>
            </div>
        </div>
    </div>
</div>

<?php if ($groups === []): ?>
    <div class="alert alert-warning">No hay controles ni preguntas activas. Registre dominios, controles y preguntas antes de auditar.</div>
<?php else: ?>

<form method="post" action="<?= BASE_URL ?>/audits/save" id="questionnaire-form">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="audit_id" value="<?= (int) $audit['id'] ?>">
    <input type="hidden" name="mode" id="save-mode" value="partial">
    <input type="hidden" name="exit_after_save" id="exit-after-save" value="0">

    <?php foreach ($groups as $domainId => $domain): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <span class="badge text-bg-dark me-2"><?= e($domain['domain_code']) ?></span>
                <strong><?= e($domain['domain_name']) ?></strong>
            </div>
            <div class="card-body">
                <?php foreach ($domain['controls'] as $controlId => $control): ?>
                    <?php $computedMaturity = $maturityMap[$controlId] ?? null; ?>
                    <div class="mb-4 border rounded p-3">
                        <!-- Encabezado del control -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge text-bg-light border"><?= e($control['control_code']) ?></span>
                            <span class="fw-semibold"><?= e($control['control_title']) ?></span>
                            <span class="text-muted small">Peso <?= e(number_format((float) $control['control_weight'], 2)) ?></span>
                            <?php if ($control['confidentiality']): ?><span class="badge text-bg-primary">C</span><?php endif; ?>
                            <?php if ($control['integrity']): ?><span class="badge text-bg-info">I</span><?php endif; ?>
                            <?php if ($control['availability']): ?><span class="badge text-bg-success">D</span><?php endif; ?>
                            <span class="badge text-bg-secondary ms-auto" title="Madurez del control, calculada como promedio ponderado de la madurez de sus preguntas">
                                Madurez control: <?= $computedMaturity !== null ? (int) $computedMaturity . '/5' : '—' ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#controlModal_c<?= $controlId ?>">
                                ⓘ Ver información completa de este control
                            </button>
                        </div>

                        <!-- Ficha completa del control: de donde viene, que exige, y que preguntas lo componen -->
                        <div class="modal fade" id="controlModal_c<?= $controlId ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <span class="badge text-bg-light border me-1"><?= e($control['control_code']) ?></span>
                                            <?= e($control['control_title']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h6 class="text-muted text-uppercase small mb-1">Dominio ISO/IEC 27002</h6>
                                        <p class="mb-3">
                                            <span class="badge text-bg-dark me-1"><?= e($domain['domain_code']) ?></span>
                                            <strong><?= e($domain['domain_name']) ?></strong>
                                            <?php if (! empty($domain['domain_description'])): ?>
                                                <br><span class="text-muted"><?= e($domain['domain_description']) ?></span>
                                            <?php endif; ?>
                                        </p>

                                        <?php if (! empty($control['iso_reference'])): ?>
                                            <h6 class="text-muted text-uppercase small mb-1">Referencia normativa</h6>
                                            <p class="mb-3">ISO/IEC 27002:2022, cláusula(s) <strong><?= e($control['iso_reference']) ?></strong></p>
                                        <?php endif; ?>

                                        <?php if (! empty($control['control_objective'])): ?>
                                            <h6 class="text-muted text-uppercase small mb-1">Objetivo del control</h6>
                                            <p class="mb-3"><?= nl2br(e($control['control_objective'])) ?></p>
                                        <?php endif; ?>

                                        <?php if (! empty($control['control_description'])): ?>
                                            <h6 class="text-muted text-uppercase small mb-1">Descripción</h6>
                                            <p class="mb-3"><?= nl2br(e($control['control_description'])) ?></p>
                                        <?php endif; ?>

                                        <h6 class="text-muted text-uppercase small mb-1">Peso e impacto en la tríada CID</h6>
                                        <p class="mb-1">
                                            Peso <strong><?= e(number_format((float) $control['control_weight'], 2)) ?></strong>
                                            dentro de su dominio (a mayor peso, mayor influencia de este control en el cálculo
                                            de madurez y riesgo del dominio).
                                        </p>
                                        <p class="mb-3">
                                            Este control afecta:
                                            <?php if ($control['confidentiality']): ?><span class="badge text-bg-primary me-1">Confidencialidad</span><?php endif; ?>
                                            <?php if ($control['integrity']): ?><span class="badge text-bg-info me-1">Integridad</span><?php endif; ?>
                                            <?php if ($control['availability']): ?><span class="badge text-bg-success me-1">Disponibilidad</span><?php endif; ?>
                                            <?php if (! $control['confidentiality'] && ! $control['integrity'] && ! $control['availability']): ?>
                                                <span class="text-muted">Sin dimensión CID asignada</span>
                                            <?php endif; ?>
                                        </p>

                                        <h6 class="text-muted text-uppercase small mb-1">Preguntas que evalúan este control</h6>
                                        <ol class="mb-0 ps-3">
                                            <?php foreach ($control['questions'] as $qq): ?>
                                                <li><?= e($qq['question_text']) ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preguntas del control: Sí / No / No aplica, madurez y justificación por pregunta -->
                        <?php foreach ($control['questions'] as $q): ?>
                            <?php
                            $qid = (int) $q['question_id'];
                            $savedMaturity = $q['maturity_level'] !== null ? (int) $q['maturity_level'] : null;
                            $scale = $q['maturity_scale'];
                            ?>
                            <div class="bg-light-subtle border rounded p-3 mb-2">
                                <div class="mb-2 fw-medium"><?= e($q['question_text']) ?></div>

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Respuesta</label>
                                        <div class="d-flex gap-2">
                                            <?php foreach (['yes' => 'Sí', 'no' => 'No', 'na' => 'N/A'] as $val => $lbl): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                           name="answer[<?= $qid ?>]"
                                                           id="ans_<?= $qid ?>_<?= $val ?>"
                                                           value="<?= $val ?>"
                                                           <?= ($q['answer'] ?? '') === $val ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="ans_<?= $qid ?>_<?= $val ?>"><?= $lbl ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Nivel de madurez (0-5)</label>
                                        <select class="form-select form-select-sm maturity-select"
                                                name="maturity_level[<?= $qid ?>]"
                                                data-target="#matdesc_<?= $qid ?>">
                                            <option value="">Sin calificar</option>
                                            <?php for ($lvl = 0; $lvl <= 5; $lvl++): ?>
                                                <option value="<?= $lvl ?>"
                                                        data-desc="<?= e($scale[$lvl] ?? '') ?>"
                                                        <?= $savedMaturity === $lvl ? 'selected' : '' ?>>
                                                    <?= $lvl ?> — <?= e($maturityShortLabels[$lvl]) ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Recomendación</label>
                                        <input class="form-control form-control-sm"
                                               name="recommendation[<?= $qid ?>]"
                                               value="<?= e($q['recommendation'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="form-text maturity-desc mt-1" id="matdesc_<?= $qid ?>">
                                    <?= $savedMaturity !== null
                                        ? e($scale[$savedMaturity] ?? '')
                                        : 'Seleccione un nivel para ver qué representa en esta pregunta.' ?>
                                </div>

                                <div class="mt-2">
                                    <label class="form-label small mb-1">
                                        Justificación del nivel de madurez
                                        <span class="text-muted">(evidencia real observada, máx. 500 caracteres)</span>
                                    </label>
                                    <textarea class="form-control form-control-sm justification-input"
                                              name="justification[<?= $qid ?>]"
                                              maxlength="500" rows="2"
                                              data-counter="#justcount_<?= $qid ?>"><?= e($q['justification'] ?? '') ?></textarea>
                                    <div class="form-text text-end">
                                        <span id="justcount_<?= $qid ?>"><?= mb_strlen((string) ($q['justification'] ?? '')) ?></span>/500
                                    </div>
                                </div>

                                <?php $evList = $evidences[$qid] ?? []; ?>
                                <?php if ($evList !== []): ?>
                                    <div class="mt-2 small">
                                        <span class="text-muted">Evidencias:</span>
                                        <?php foreach ($evList as $ev): ?>
                                            <span class="badge text-bg-secondary me-1">
                                                <a class="text-white text-decoration-none"
                                                   href="<?= BASE_URL ?>/audits/evidence?id=<?= (int) $ev['id'] ?>"
                                                   target="_blank"><?= e($ev['original_name']) ?></a>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <button class="btn btn-outline-secondary" type="submit"
                onclick="document.getElementById('save-mode').value='partial'; document.getElementById('exit-after-save').value='1';">
            Guardar y salir
        </button>
        <button class="btn btn-primary" type="submit"
                onclick="document.getElementById('save-mode').value='partial'; document.getElementById('exit-after-save').value='0';">
            Guardar progreso
        </button>
        <button class="btn btn-success" type="submit"
                onclick="document.getElementById('save-mode').value='final'"
                data-confirm="Finalizar la auditoria? No podra editarla salvo que la reabra.">Finalizar auditoria</button>
    </div>
</form>

<form method="post" action="<?= BASE_URL ?>/audits/cancel" class="text-end mb-4"
      data-confirm="Cancelar esta auditoria? El progreso guardado se conserva, pero quedara marcada como cancelada hasta que la reabra.">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="id" value="<?= (int) $audit['id'] ?>">
    <button class="btn btn-link text-danger btn-sm" type="submit">Cancelar esta auditoría</button>
</form>

<!-- Adjuntar evidencia -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white"><strong>Adjuntar evidencia</strong></div>
    <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/audits/upload-evidence" enctype="multipart/form-data" class="row g-2 align-items-end">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <input type="hidden" name="audit_id" value="<?= (int) $audit['id'] ?>">
            <div class="col-md-5">
                <label class="form-label small mb-1">Pregunta asociada</label>
                <select class="form-select form-select-sm" name="question_id" required>
                    <option value="">Seleccione una pregunta</option>
                    <?php foreach ($groups as $domain): ?>
                        <?php foreach ($domain['controls'] as $control): ?>
                            <?php foreach ($control['questions'] as $q): ?>
                                <option value="<?= (int) $q['question_id'] ?>"><?= e($control['control_code'] . ' - ' . mb_substr($q['question_text'], 0, 60)) ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small mb-1">Archivo (PDF, imagen, Office; max 5 MB)</label>
                <input class="form-control form-control-sm" type="file" name="evidence" required>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-primary btn-sm" type="submit">Subir</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.maturity-select').forEach(function (sel) {
    sel.addEventListener('change', function () {
        var target = document.querySelector(sel.dataset.target);
        if (! target) { return; }
        var opt = sel.options[sel.selectedIndex];
        target.textContent = (opt && opt.dataset.desc)
            ? opt.dataset.desc
            : 'Seleccione un nivel para ver qué representa en esta pregunta.';
    });
});
document.querySelectorAll('.justification-input').forEach(function (field) {
    var counter = document.querySelector(field.dataset.counter);
    if (! counter) { return; }
    field.addEventListener('input', function () {
        counter.textContent = field.value.length;
    });
});
</script>

<?php endif; ?>
