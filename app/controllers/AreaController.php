<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\Area;
use App\Models\Organization;

final class AreaController extends Controller
{
    private Area $areas;
    private Organization $organizations;

    public function __construct()
    {
        $this->areas = new Area();
        $this->organizations = new Organization();
    }

    public function index(): void
    {
        Middleware::auth();
        $search = trim((string) ($_GET['q'] ?? ''));
        $organizationId = (int) ($_GET['organization_id'] ?? 0);
        $sort = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $page = (int) ($_GET['page'] ?? 1);

        $this->view('areas/index', [
            'title' => 'Areas',
            'pagination' => $this->areas->paginateList($search, $organizationId, $sort, $direction, $page),
            'organizations' => $this->organizations->activeOptions(),
            'search' => $search,
            'organizationId' => $organizationId,
            'sort' => $sort,
            'direction' => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('areas/form', [
            'title' => 'Nueva area',
            'area' => null,
            'organizations' => $this->organizations->activeOptions(),
            'action' => '/areas/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData('/areas/create');
        if ($this->areas->nameExists((int) $data['organization_id'], $data['name'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un area con ese nombre para la organizacion seleccionada.');
            $this->redirect('/areas/create');
        }

        $this->areas->create($data);
        Flash::success('Area creada correctamente.');
        $this->redirect('/areas');
    }

    public function show(): void
    {
        Middleware::auth();
        $area = $this->areas->find((int) ($_GET['id'] ?? 0));
        if (! $area) {
            Flash::error('Area no encontrada.');
            $this->redirect('/areas');
        }
        $this->view('areas/show', ['title' => 'Detalle de area', 'area' => $area]);
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $area = $this->areas->find((int) ($_GET['id'] ?? 0));
        if (! $area) {
            Flash::error('Area no encontrada.');
            $this->redirect('/areas');
        }
        $this->view('areas/form', [
            'title' => 'Editar area',
            'area' => $area,
            'organizations' => $this->organizations->activeOptions(),
            'action' => '/areas/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/areas/edit?id=' . $id);
        if ($this->areas->nameExists((int) $data['organization_id'], $data['name'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un area con ese nombre para la organizacion seleccionada.');
            $this->redirect('/areas/edit?id=' . $id);
        }

        $this->areas->update($id, $data);
        Flash::success('Area actualizada correctamente.');
        $this->redirect('/areas');
    }

    public function toggle(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/areas');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';
        $this->areas->setStatus($id, $status);
        Flash::success('Estado del area actualizado.');
        $this->redirect('/areas');
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/areas');
        }

        $data = [
            'organization_id' => (int) ($_POST['organization_id'] ?? 0),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($data['organization_id'] <= 0) {
            $_SESSION['_old'] = $data;
            Flash::error('Seleccione una organizacion valida.');
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
