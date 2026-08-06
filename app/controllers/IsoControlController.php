<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\IsoControl;
use App\Models\IsoDomain;

final class IsoControlController extends Controller
{
    private IsoControl $controls;
    private IsoDomain $domains;

    public function __construct()
    {
        $this->controls = new IsoControl();
        $this->domains = new IsoDomain();
    }

    public function index(): void
    {
        Middleware::auth();
        $search = trim((string) ($_GET['q'] ?? ''));
        $domainId = (int) ($_GET['domain_id'] ?? 0);
        $sort = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $page = (int) ($_GET['page'] ?? 1);

        $this->view('controls/index', [
            'title' => 'Controles ISO/IEC 27002',
            'pagination' => $this->controls->paginateList($search, $domainId, $sort, $direction, $page),
            'domains' => $this->domains->activeOptions(),
            'search' => $search,
            'domainId' => $domainId,
            'sort' => $sort,
            'direction' => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('controls/form', [
            'title' => 'Nuevo control',
            'control' => null,
            'domains' => $this->domains->activeOptions(),
            'action' => '/controls/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData('/controls/create');
        if ($this->controls->codeExists($data['code'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un control con ese codigo.');
            $this->redirect('/controls/create');
        }

        $this->controls->create($data);
        Flash::success('Control creado correctamente.');
        $this->redirect('/controls');
    }

    public function show(): void
    {
        Middleware::auth();
        $control = $this->controls->find((int) ($_GET['id'] ?? 0));
        if (! $control) {
            Flash::error('Control no encontrado.');
            $this->redirect('/controls');
        }
        $this->view('controls/show', ['title' => 'Detalle del control', 'control' => $control]);
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $control = $this->controls->find((int) ($_GET['id'] ?? 0));
        if (! $control) {
            Flash::error('Control no encontrado.');
            $this->redirect('/controls');
        }
        $this->view('controls/form', [
            'title' => 'Editar control',
            'control' => $control,
            'domains' => $this->domains->activeOptions(),
            'action' => '/controls/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/controls/edit?id=' . $id);
        if ($this->controls->codeExists($data['code'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un control con ese codigo.');
            $this->redirect('/controls/edit?id=' . $id);
        }

        $this->controls->update($id, $data);
        Flash::success('Control actualizado correctamente.');
        $this->redirect('/controls');
    }

    public function toggle(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/controls');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';
        $this->controls->setStatus($id, $status);
        Flash::success('Estado del control actualizado.');
        $this->redirect('/controls');
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/controls');
        }

        $weight = (float) ($_POST['weight'] ?? 1);
        if ($weight < 0.1) {
            $weight = 1.0;
        }

        $data = [
            'domain_id' => (int) ($_POST['domain_id'] ?? 0),
            'code' => trim((string) ($_POST['code'] ?? '')),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'objective' => trim((string) ($_POST['objective'] ?? '')),
            'weight' => $weight,
            'confidentiality' => isset($_POST['confidentiality']) ? 1 : 0,
            'integrity' => isset($_POST['integrity']) ? 1 : 0,
            'availability' => isset($_POST['availability']) ? 1 : 0,
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('code', $data['code'], 'Codigo')
            ->required('title', $data['title'], 'Titulo')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($data['domain_id'] <= 0) {
            $_SESSION['_old'] = $data;
            Flash::error('Seleccione un dominio valido.');
            $this->redirect($failureRedirect);
        }

        if ($data['confidentiality'] === 0 && $data['integrity'] === 0 && $data['availability'] === 0) {
            $_SESSION['_old'] = $data;
            Flash::error('El control debe relacionarse al menos con una dimension (C, I o D).');
            $this->redirect($failureRedirect);
        }

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($failureRedirect);
        }

        return $data;
    }
}
