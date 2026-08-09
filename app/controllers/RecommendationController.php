<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\IsoControl;
use App\Models\Recommendation;

final class RecommendationController extends Controller
{
    private Recommendation $model;
    private Audit $audits;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->model       = new Recommendation();
        $this->audits      = new Audit();
        $this->activityLog = new ActivityLog();
    }

    /** Lista de recomendaciones pendientes / en progreso (global para consultora, propia si esta restringido). */
    public function index(): void
    {
        Middleware::auth();
        $orgId = Auth::organizationId();
        $allowedOrgIds = Middleware::assignedOrganizationIds();
        $this->view('recommendations/index', [
            'title'   => 'Seguimiento de recomendaciones',
            'items'   => $this->model->allPending($orgId, $allowedOrgIds),
            'counts'  => $this->model->countByStatus($orgId, $allowedOrgIds),
        ]);
    }

    /**
     * Recomendaciones de una auditoría específica. Si llega desde el
     * reporte (sección "Controles con menor madurez" / "mayor riesgo")
     * con control_id, suggested_maturity y suggested_risk en la query,
     * precarga el formulario de nueva recomendación para ese control en
     * vez de dejarlo en blanco.
     */
    public function forAudit(): void
    {
        Middleware::auth();
        $auditId = (int) ($_GET['audit_id'] ?? 0);
        $audit   = $this->audits->find($auditId);
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/audits'); }
        Middleware::ownsOrganization((int) $audit['organization_id']);

        $preselectControlId  = (int) ($_GET['control_id'] ?? 0);
        $suggestedDescription = null;
        if ($preselectControlId > 0) {
            $control = (new IsoControl())->find($preselectControlId);
            if ($control) {
                $maturity = $_GET['suggested_maturity'] ?? null;
                $risk     = $_GET['suggested_risk'] ?? null;
                $suggestedDescription = sprintf(
                    'El control %s — %s presenta un nivel de madurez de %s/5%s tras esta auditoría. '
                    . 'Se recomienda reforzar los procedimientos relacionados para elevar su nivel de implementación.',
                    $control['code'],
                    $control['title'],
                    $maturity !== null ? number_format((float) $maturity, 0) : '?',
                    $risk !== null ? ' y una exposición al riesgo de ' . number_format((float) $risk, 1) . '%' : ''
                );
            }
        }

        $this->view('recommendations/audit', [
            'title'                 => 'Recomendaciones: ' . $audit['name'],
            'audit'                 => $audit,
            'items'                 => $this->model->forAudit($auditId),
            'controls'              => (new IsoControl())->activeOptions(),
            'preselectControlId'    => $preselectControlId,
            'suggestedDescription'  => $suggestedDescription,
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('Sesion expirada.'); $this->redirect('/recommendations');
        }

        $auditId = (int) ($_POST['audit_id'] ?? 0);
        $audit   = $this->audits->find($auditId);
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/recommendations'); }
        Middleware::ownsOrganization((int) $audit['organization_id']);

        $dueDate = trim((string) ($_POST['due_date'] ?? ''));

        $this->model->create([
            'audit_id'    => $auditId,
            'control_id'  => (int) ($_POST['control_id'] ?? 0),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'responsible' => trim((string) ($_POST['responsible'] ?? '')) ?: null,
            'due_date'    => $dueDate !== '' ? $dueDate : null,
            'status'      => 'pending',
            'notes'       => null,
        ]);

        Flash::success('Recomendacion registrada.');
        $this->redirect('/recommendations/audit?audit_id=' . $auditId);
    }

    public function update(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('Sesion expirada.'); $this->redirect('/recommendations');
        }

        $id  = (int) ($_POST['id'] ?? 0);
        $rec = $this->model->find($id);
        if (! $rec) { Flash::error('Recomendacion no encontrada.'); $this->redirect('/recommendations'); }
        Middleware::ownsOrganization((int) $rec['audit_organization_id']);

        $dueDate = trim((string) ($_POST['due_date'] ?? ''));
        $status  = $_POST['status'] ?? 'pending';
        if (! in_array($status, ['pending', 'in_progress', 'done'], true)) $status = 'pending';

        $this->model->update($id, [
            'description' => trim((string) ($_POST['description'] ?? '')),
            'responsible' => trim((string) ($_POST['responsible'] ?? '')) ?: null,
            'due_date'    => $dueDate !== '' ? $dueDate : null,
            'status'      => $status,
            'notes'       => trim((string) ($_POST['notes'] ?? '')) ?: null,
        ]);

        if ($status !== $rec['status']) {
            $this->activityLog->record(
                (int) $rec['audit_id'],
                (int) (Auth::user()['id'] ?? 0),
                'recommendation_status_changed',
                sprintf(
                    'Cambió el estado de la recomendación sobre %s de "%s" a "%s".',
                    $rec['control_code'], $rec['status'], $status
                )
            );
        }

        Flash::success('Recomendacion actualizada.');
        $this->redirect('/recommendations/audit?audit_id=' . $rec['audit_id']);
    }

    public function delete(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('Sesion expirada.'); $this->redirect('/recommendations');
        }
        $id  = (int) ($_POST['id'] ?? 0);
        $rec = $this->model->find($id);
        if ($rec) {
            Middleware::ownsOrganization((int) $rec['audit_organization_id']);
            $this->model->delete($id);
            Flash::success('Recomendacion eliminada.');
            $this->redirect('/recommendations/audit?audit_id=' . $rec['audit_id']);
        }
        $this->redirect('/recommendations');
    }
}
