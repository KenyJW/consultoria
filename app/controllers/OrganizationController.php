<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\Organization;

final class OrganizationController extends Controller
{
    private Organization $organizations;

    public function __construct()
    {
        $this->organizations = new Organization();
    }

    public function index(): void
    {
        Middleware::auth();
        $search = trim((string) ($_GET['q'] ?? ''));
        $sort = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $page = (int) ($_GET['page'] ?? 1);

        $this->view('organizations/index', [
            'title' => 'Organizaciones',
            'pagination' => $this->organizations->paginateList($search, $sort, $direction, $page),
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('organizations/form', [
            'title' => 'Nueva organizacion',
            'organization' => null,
            'action' => '/organizations/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData('/organizations/create');
        if ($this->organizations->nameExists($data['name'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe una organizacion con ese nombre.');
            $this->redirect('/organizations/create');
        }

        $this->organizations->create($data);
        Flash::success('Organizacion creada correctamente.');
        $this->redirect('/organizations');
    }

    public function show(): void
    {
        Middleware::auth();
        $organization = $this->organizations->find((int) ($_GET['id'] ?? 0));
        if (! $organization) {
            Flash::error('Organizacion no encontrada.');
            $this->redirect('/organizations');
        }

        $this->view('organizations/show', ['title' => 'Detalle de organizacion', 'organization' => $organization]);
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $organization = $this->organizations->find((int) ($_GET['id'] ?? 0));
        if (! $organization) {
            Flash::error('Organizacion no encontrada.');
            $this->redirect('/organizations');
        }

        $this->view('organizations/form', [
            'title' => 'Editar organizacion',
            'organization' => $organization,
            'action' => '/organizations/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/organizations/edit?id=' . $id);
        if ($this->organizations->nameExists($data['name'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe una organizacion con ese nombre.');
            $this->redirect('/organizations/edit?id=' . $id);
        }

        $this->organizations->update($id, $data);
        Flash::success('Organizacion actualizada correctamente.');
        $this->redirect('/organizations');
    }

    public function toggle(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/organizations');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';
        $this->organizations->setStatus($id, $status);
        Flash::success('Estado de organizacion actualizado.');
        $this->redirect('/organizations');
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/organizations');
        }

        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre')
            ->email('email', $data['email'], 'Correo')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($failureRedirect);
        }

        return $data;
    }
}
