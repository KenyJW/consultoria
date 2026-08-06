<?php use App\Core\Csrf; ?>
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

    <?php foreach ($groups as $domainId => $domain): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <span class="badge text-bg-dark me-2"><?= e($domain['domain_code']) ?></span>
                <strong><?= e($domain['domain_name']) ?></strong>
            </div>
            <div class="card-body">
                <?php foreach ($domain['controls'] as $controlId => $control): ?>
                    <?php $savedMaturity = (int) ($maturityMap[$controlId] ?? 0); ?>
                    <div class="mb-4 border rounded p-3">
                        <!-- Encabezado del control -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="badge text-bg-light border"><?= e($control['control_code']) ?></span>
                            <span class="fw-semibold"><?= e($control['control_title']) ?></span>
                            <span class="text-muted small">Peso <?= e(number_format((float) $control['control_weight'], 2)) ?></span>
                            <?php if ($control['confidentiality']): ?><span class="badge text-bg-primary">C</span><?php endif; ?>
                            <?php if ($control['integrity']): ?><span class="badge text-bg-info">I</span><?php endif; ?>
                            <?php if ($control['availability']): ?><span class="badge text-bg-success">D</span><?php endif; ?>
                        </div>

                        <!-- Preguntas del control: Sí / No / No aplica -->
                        <?php foreach ($control['questions'] as $q): ?>
                            <?php $qid = (int) $q['question_id']; ?>
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
                                    <div class="col-md-5">
                                        <label class="form-label small mb-1">Observaciones / comentarios</label>
                                        <input class="form-control form-control-sm"
                                               name="observation[<?= $qid ?>]"
                                               value="<?= e($q['observation'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">Recomendacion</label>
                                        <input class="form-control form-control-sm"
                                               name="recommendation[<?= $qid ?>]"
                                               value="<?= e($q['recommendation'] ?? '') ?>">
                                    </div>
                                </div>

                                <?php $evList = $evidences[$qid] ?? []; ?>
                                <?php if ($evList !== []): ?>
                                    <div class="mt-2 small">
                                        <span class="text-muted">Evidencias:</span>
                                        <?php foreach ($evList as $ev): ?>
                                            <span class="badge text-bg-secondary me-1">
                                                <a class="text-white text-decoration-none"
                                                   href="<?= BASE_URL ?>/uploads/evidences/<?= e($ev['stored_name']) ?>"
                                                   target="_blank"><?= e($ev['original_name']) ?></a>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Nivel de madurez del control (asignado por el auditor) -->
                        <div class="mt-3 p-2 bg-white border rounded">
                            <label class="form-label small fw-semibold mb-1">
                                Nivel de madurez del control <?= e($control['control_code']) ?>
                            </label>
                            <select class="form-select form-select-sm w-auto"
                                    name="control_maturity[<?= $controlId ?>]">
                                <option value="0" <?= $savedMaturity === 0 ? 'selected' : '' ?>>0 — No existe</option>
                                <option value="1" <?= $savedMaturity === 1 ? 'selected' : '' ?>>1 — Informal / ocasional</option>
                                <option value="2" <?= $savedMaturity === 2 ? 'selected' : '' ?>>2 — Parcial / algo documentado</option>
                                <option value="3" <?= $savedMaturity === 3 ? 'selected' : '' ?>>3 — Documentado e implementado</option>
                                <option value="4" <?= $savedMaturity === 4 ? 'selected' : '' ?>>4 — Implementado y supervisado</option>
                                <option value="5" <?= $savedMaturity === 5 ? 'selected' : '' ?>>5 — Optimizado / mejora continua</option>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/audits">Salir</a>
        <button class="btn btn-primary" type="submit"
                onclick="document.getElementById('save-mode').value='partial'">Guardar progreso</button>
        <button class="btn btn-success" type="submit"
                onclick="document.getElementById('save-mode').value='final'"
                data-confirm="Finalizar la auditoria? No podra editarla salvo que la reabra.">Finalizar auditoria</button>
    </div>
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

<?php endif; ?>
