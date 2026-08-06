<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Models\Audit;
use App\Models\IsoControl;
use App\Models\Recommendation;

final class RecommendationController extends Controller
{
    private Recommendation $model;

    public function __construct()
    {
        $this->model = new Recommendation();
    }

    /** Lista global de recomendaciones pendientes / en progreso. */
    public function index(): void
    {
        Middleware::auth();
        $this->view('recommendations/index', [
            'title'   => 'Seguimiento de recomendaciones',
            'items'   => $this->model->allPending(),
            'counts'  => $this->model->countByStatus(),
        ]);
    }

    /** Recomendaciones de una auditoría específica. */
    public function forAudit(): void
    {
        Middleware::auth();
        $auditId = (int) ($_GET['audit_id'] ?? 0);
        $audit   = (new Audit())->find($auditId);
        if (! $audit) { Flash::error('Auditoria no encontrada.'); $this->redirect('/audits'); }

        $this->view('recommendations/audit', [
            'title'    => 'Recomendaciones: ' . $audit['name'],
            'audit'    => $audit,
            'items'    => $this->model->forAudit($auditId),
            'controls' => (new IsoControl())->activeOptions(),
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin', 'auditor']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('Sesion expirada.'); $this->redirect('/recommendations');
        }

        $auditId  = (int) ($_POST['audit_id'] ?? 0);
        $dueDate  = trim((string) ($_POST['due_date'] ?? ''));

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

        $id      = (int) ($_POST['id'] ?? 0);
        $rec     = $this->model->find($id);
        if (! $rec) { Flash::error('Recomendacion no encontrada.'); $this->redirect('/recommendations'); }

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

        Flash::success('Recomendacion actualizada.');
        $this->redirect('/recommendations/audit?audit_id=' . $rec['audit_id']);
    }

    public function delete(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('Sesion expirada.'); $this->redirect('/recommendations');
        }
        $id  = (int) ($_POST['id'] ?? 0);
        $rec = $this->model->find($id);
        if ($rec) {
            $this->model->delete($id);
            Flash::success('Recomendacion eliminada.');
            $this->redirect('/recommendations/audit?audit_id=' . $rec['audit_id']);
        }
        $this->redirect('/recommendations');
    }
}
