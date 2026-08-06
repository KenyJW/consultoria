<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\MaturityCalculator;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\Area;
use App\Models\Audit;
use App\Models\AuditControlMaturity;
use App\Models\Evidence;
use App\Models\Organization;
use App\Models\Response;
use App\Models\User;

final class AuditController extends Controller
{
    private const MAX_EVIDENCE_BYTES = 5_242_880;
    private const ALLOWED_MIME = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/webp',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    private Audit $audits;
    private Organization $organizations;
    private Area $areas;
    private User $users;
    private Response $responses;
    private Evidence $evidences;
    private AuditControlMaturity $controlMaturity;

    public function __construct()
    {
        $this->audits          = new Audit();
        $this->organizations   = new Organization();
        $this->areas           = new Area();
        $this->users           = new User();
        $this->responses       = new Response();
        $this->evidences       = new Evidence();
        $this->controlMaturity = new AuditControlMaturity();
    }

    public function index(): void
    {
        Middleware::auth();
        $search         = trim((string) ($_GET['q'] ?? ''));
        $organizationId = (int) ($_GET['organization_id'] ?? 0);
        $status         = (string) ($_GET['status'] ?? '');
        $sort           = (string) ($_GET['sort'] ?? 'created_at');
        $direction      = (string) ($_GET['direction'] ?? 'desc');
        $page           = (int) ($_GET['page'] ?? 1);

        $this->view('audits/index', [
            'title'          => 'Auditorias',
            'pagination'     => $this->audits->paginateList($search, $organizationId, $status, $sort, $direction, $page),
            'organizations'  => $this->organizations->activeOptions(),
            'search'         => $search,
            'organizationId' => $organizationId,
            'status'         => $status,
            'sort'           => $sort,
            'direction'      => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin', 'auditor']);
        $this->view('audits/form', [
            'title'         => 'Nueva auditoria',
            'audit'         => null,
            'organizations' => $this->organizations->activeOptions(),
            'auditors'      => $this->users->auditors(),
            'action'        => '/audits/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin', 'auditor']);
        $data           = $this->validatedData('/audits/create');
        $data['status'] = 'draft';
        $id             = $this->audits->create($data);
        Flash::success('Auditoria creada. Ahora puede responder el cuestionario.');
        $this->redirect('/audits/run?id=' . $id);
    }

    public function edit(): void
    {
        Middleware::roles(['admin', 'auditor']);
        $audit = $this->audits->find((int) ($_GET['id'] ?? 0));
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/audits'); }
        $this->view('audits/form', [
            'title'         => 'Editar auditoria',
            'audit'         => $audit,
            'organizations' => $this->organizations->activeOptions(),
            'auditors'      => $this->users->auditors(),
            'action'        => '/audits/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin', 'auditor']);
        $id   = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/audits/edit?id=' . $id);
        $this->audits->update($id, $data);
        Flash::success('Auditoria actualizada correctamente.');
        $this->redirect('/audits');
    }

    public function show(): void
    {
        Middleware::auth();
        $audit = $this->audits->find((int) ($_GET['id'] ?? 0));
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/audits'); }
        $this->view('audits/show', [
            'title'    => 'Detalle de auditoria',
            'audit'    => $audit,
            'progress' => $this->audits->progress((int) $audit['id']),
        ]);
    }

    public function areasJson(): void
    {
        Middleware::auth();
        $organizationId = (int) ($_GET['organization_id'] ?? 0);
        $db             = \App\Core\Database::getConnection();
        $statement      = $db->prepare('SELECT id, name FROM areas WHERE organization_id = :oid AND status = "active" ORDER BY name ASC');
        $statement->execute(['oid' => $organizationId]);
        header('Content-Type: application/json');
        echo json_encode($statement->fetchAll());
        exit;
    }

    public function run(): void
    {
        Middleware::roles(['admin', 'auditor']);
        $audit = $this->audits->find((int) ($_GET['id'] ?? 0));
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/audits'); }
        if ($audit['status'] === 'closed') {
            Flash::error('La auditoria esta cerrada y no admite cambios.');
            $this->redirect('/audits/show?id=' . $audit['id']);
        }

        $rows = $this->audits->questionnaire((int) $audit['id']);
        $this->view('audits/run', [
            'title'          => 'Cuestionario: ' . $audit['name'],
            'audit'          => $audit,
            'groups'         => $this->groupQuestionnaire($rows),
            'evidences'      => $this->evidences->forAuditByQuestion((int) $audit['id']),
            'progress'       => $this->audits->progress((int) $audit['id']),
            'maturityMap'    => $this->controlMaturity->mapForAudit((int) $audit['id']),
        ]);
    }

    public function save(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.'); $this->redirect('/audits');
        }

        $auditId = (int) ($_POST['audit_id'] ?? 0);
        $audit   = $this->audits->find($auditId);
        if (! $audit || $audit['status'] === 'closed') {
            Flash::error('Auditoria no valida.'); $this->redirect('/audits');
        }

        $mode            = ($_POST['mode'] ?? 'partial') === 'final' ? 'final' : 'partial';
        $answers         = (array) ($_POST['answer'] ?? []);
        $observations    = (array) ($_POST['observation'] ?? []);
        $recommendations = (array) ($_POST['recommendation'] ?? []);
        $maturityLevels  = (array) ($_POST['control_maturity'] ?? []);

        // Guardar respuestas Sí/No/Na por pregunta
        $saved = 0;
        foreach ($answers as $questionId => $answer) {
            $questionId = (int) $questionId;
            $answer     = in_array($answer, ['yes', 'no', 'na'], true) ? $answer : null;
            $this->responses->upsert($auditId, $questionId, [
                'answer'         => $answer,
                'observation'    => trim((string) ($observations[$questionId] ?? '')) ?: null,
                'recommendation' => trim((string) ($recommendations[$questionId] ?? '')) ?: null,
            ]);
            $saved++;
        }

        // Guardar nivel de madurez por control
        foreach ($maturityLevels as $controlId => $level) {
            $level = max(0, min(5, (int) $level));
            $this->controlMaturity->upsert($auditId, (int) $controlId, $level);
        }

        if ($mode === 'final') {
            $controlRows = $this->controlMaturity->forAudit($auditId);
            $responseRows = $this->responses->forAudit($auditId);
            $calc = MaturityCalculator::calculate($controlRows, $responseRows);
            $this->audits->close(
                $auditId,
                $calc['maturity_score'],
                $calc['risk_score'],
                $calc['risk_c'],
                $calc['risk_i'],
                $calc['risk_d']
            );
            Flash::success(
                'Auditoria finalizada. Madurez: ' . $calc['maturity_score'] . '/5 — ' .
                'Riesgo global: ' . $calc['risk_score'] . '% ' .
                '(C: ' . $calc['risk_c'] . '% / I: ' . $calc['risk_i'] . '% / D: ' . $calc['risk_d'] . '%)'
            );
            $this->redirect('/audits/report?id=' . $auditId);
        }

        if ($audit['status'] === 'draft') {
            $this->audits->setStatus($auditId, 'in_progress');
        }
        Flash::success('Progreso guardado (' . $saved . ' respuestas).');
        $this->redirect('/audits/run?id=' . $auditId);
    }

    public function reopen(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.'); $this->redirect('/audits');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $this->audits->setStatus($id, 'in_progress');
        Flash::success('Auditoria reabierta para edicion.');
        $this->redirect('/audits/run?id=' . $id);
    }

    public function uploadEvidence(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.'); $this->redirect('/audits');
        }

        $auditId    = (int) ($_POST['audit_id'] ?? 0);
        $questionId = (int) ($_POST['question_id'] ?? 0);
        $audit      = $this->audits->find($auditId);
        if (! $audit || $audit['status'] === 'closed') {
            Flash::error('Auditoria no valida.'); $this->redirect('/audits');
        }

        $file = $_FILES['evidence'] ?? null;
        if (! $file || ! is_uploaded_file($file['tmp_name'] ?? '') || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Flash::error('No se recibio ningun archivo valido.');
            $this->redirect('/audits/run?id=' . $auditId);
        }
        if ((int) $file['size'] > self::MAX_EVIDENCE_BYTES) {
            Flash::error('El archivo supera el limite de 5 MB.');
            $this->redirect('/audits/run?id=' . $auditId);
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($file['tmp_name']);
        if (! in_array($mime, self::ALLOWED_MIME, true)) {
            Flash::error('Tipo de archivo no permitido.');
            $this->redirect('/audits/run?id=' . $auditId);
        }

        $responseId = $this->responses->findId($auditId, $questionId);
        if ($responseId === 0) {
            $responseId = $this->responses->upsert($auditId, $questionId, [
                'answer' => null, 'observation' => null, 'recommendation' => null,
            ]);
        }

        $uploadDir = BASE_PATH . '/public/uploads/evidences';
        if (! is_dir($uploadDir)) { mkdir($uploadDir, 0775, true); }

        $extension  = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
        $storedName = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . strtolower($extension) : '');
        if (! move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $storedName)) {
            Flash::error('No fue posible almacenar el archivo.');
            $this->redirect('/audits/run?id=' . $auditId);
        }

        $this->evidences->create([
            'response_id'   => $responseId,
            'original_name' => substr((string) $file['name'], 0, 255),
            'stored_name'   => $storedName,
            'mime_type'     => $mime,
            'size_bytes'    => (int) $file['size'],
        ]);

        Flash::success('Evidencia cargada correctamente.');
        $this->redirect('/audits/run?id=' . $auditId);
    }

    public function deleteEvidence(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.'); $this->redirect('/audits');
        }
        $auditId  = (int) ($_POST['audit_id'] ?? 0);
        $evidence = $this->evidences->find((int) ($_POST['id'] ?? 0));
        if ($evidence) {
            $path = BASE_PATH . '/public/uploads/evidences/' . $evidence['stored_name'];
            if (is_file($path)) { unlink($path); }
            $this->evidences->delete((int) $evidence['id']);
            Flash::success('Evidencia eliminada.');
        }
        $this->redirect('/audits/run?id=' . $auditId);
    }

    /** Reporte ejecutivo de una auditoría cerrada (Fase 6). */
    public function report(): void
    {
        Middleware::auth();
        $audit = $this->audits->find((int) ($_GET['id'] ?? 0));
        if (! $audit || $audit['status'] !== 'closed') {
            Flash::error('Reporte disponible solo para auditorias cerradas.');
            $this->redirect('/audits');
        }

        $controlRows  = $this->controlMaturity->forAudit((int) $audit['id']);
        $responseRows = $this->responses->forAudit((int) $audit['id']);
        $calc         = MaturityCalculator::calculate($controlRows, $responseRows);

        $rows   = $this->audits->questionnaire((int) $audit['id']);
        $groups = $this->groupQuestionnaire($rows);

        // Controles con menor madurez (top 5)
        $byControl = $calc['by_control'];
        uasort($byControl, fn($a, $b) => $a['maturity'] <=> $b['maturity']);
        $lowestMaturity = array_slice($byControl, 0, 5, true);

        // Controles con mayor riesgo (top 5)
        $byControlRisk = $calc['by_control'];
        uasort($byControlRisk, fn($a, $b) => $b['risk'] <=> $a['risk']);
        $highestRisk = array_slice($byControlRisk, 0, 5, true);

        // Mapa control_id → nombre
        $controlNames = [];
        foreach ($controlRows as $cr) {
            $controlNames[(int) $cr['control_id']] = $cr['control_code'] . ' — ' . $cr['control_title'];
        }

        $this->view('audits/report', [
            'title'         => 'Reporte: ' . $audit['name'],
            'audit'         => $audit,
            'calc'          => $calc,
            'groups'        => $groups,
            'controlRows'   => $controlRows,
            'lowestMaturity'=> $lowestMaturity,
            'highestRisk'   => $highestRisk,
            'controlNames'  => $controlNames,
            'maturityDomain' => $this->audits->maturityByDomain(),
        ], 'none');
    }

    private function groupQuestionnaire(array $rows): array
    {
        $groups = [];
        foreach ($rows as $row) {
            $domainId  = (int) $row['domain_id'];
            $controlId = (int) $row['control_id'];

            if (! isset($groups[$domainId])) {
                $groups[$domainId] = [
                    'domain_code' => $row['domain_code'],
                    'domain_name' => $row['domain_name'],
                    'controls'    => [],
                ];
            }
            if (! isset($groups[$domainId]['controls'][$controlId])) {
                $groups[$domainId]['controls'][$controlId] = [
                    'control_id'     => $controlId,
                    'control_code'   => $row['control_code'],
                    'control_title'  => $row['control_title'],
                    'control_weight' => $row['control_weight'],
                    'confidentiality'=> (int) $row['confidentiality'],
                    'integrity'      => (int) $row['integrity'],
                    'availability'   => (int) $row['availability'],
                    'questions'      => [],
                ];
            }
            $groups[$domainId]['controls'][$controlId]['questions'][] = [
                'question_id'   => (int) $row['question_id'],
                'question_text' => $row['question_text'],
                'answer'        => $row['answer'],
                'observation'   => $row['observation'],
                'recommendation'=> $row['recommendation'],
            ];
        }
        return $groups;
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.'); $this->redirect('/audits');
        }

        $endDate = trim((string) ($_POST['end_date'] ?? ''));
        $data = [
            'organization_id'  => (int) ($_POST['organization_id'] ?? 0),
            'area_id'          => (int) ($_POST['area_id'] ?? 0),
            'auditor_user_id'  => (int) ($_POST['auditor_user_id'] ?? 0),
            'dba_name'         => trim((string) ($_POST['dba_name'] ?? '')) ?: null,
            'name'             => trim((string) ($_POST['name'] ?? '')),
            'start_date'       => trim((string) ($_POST['start_date'] ?? '')),
            'end_date'         => $endDate !== '' ? $endDate : null,
        ];

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre de la auditoria')
            ->required('start_date', $data['start_date'], 'Fecha de inicio');

        foreach (['organization_id' => 'una organizacion', 'area_id' => 'un area', 'auditor_user_id' => 'un auditor'] as $field => $label) {
            if ($data[$field] <= 0) {
                $_SESSION['_old'] = $data;
                Flash::error('Seleccione ' . $label . ' valida.');
                $this->redirect($failureRedirect);
            }
        }

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($failureRedirect);
        }

        return $data;
    }
}
