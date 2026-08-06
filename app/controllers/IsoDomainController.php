<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\IsoDomain;

final class IsoDomainController extends Controller
{
    private IsoDomain $domains;

    public function __construct()
    {
        $this->domains = new IsoDomain();
    }

    public function index(): void
    {
        Middleware::auth();
        $search = trim((string) ($_GET['q'] ?? ''));
        $sort = (string) ($_GET['sort'] ?? 'created_at');
        $direction = (string) ($_GET['direction'] ?? 'desc');
        $page = (int) ($_GET['page'] ?? 1);

        $this->view('domains/index', [
            'title' => 'Dominios ISO/IEC 27002',
            'pagination' => $this->domains->paginateList($search, $sort, $direction, $page),
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        Middleware::roles(['admin']);
        $this->view('domains/form', [
            'title' => 'Nuevo dominio',
            'domain' => null,
            'action' => '/domains/store',
        ]);
    }

    public function store(): void
    {
        Middleware::roles(['admin']);
        $data = $this->validatedData('/domains/create');
        if ($this->domains->codeExists($data['code'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un dominio con ese codigo.');
            $this->redirect('/domains/create');
        }

        $this->domains->create($data);
        Flash::success('Dominio creado correctamente.');
        $this->redirect('/domains');
    }

    public function show(): void
    {
        Middleware::auth();
        $domain = $this->domains->find((int) ($_GET['id'] ?? 0));
        if (! $domain) {
            Flash::error('Dominio no encontrado.');
            $this->redirect('/domains');
        }
        $this->view('domains/show', ['title' => 'Detalle del dominio', 'domain' => $domain]);
    }

    public function edit(): void
    {
        Middleware::roles(['admin']);
        $domain = $this->domains->find((int) ($_GET['id'] ?? 0));
        if (! $domain) {
            Flash::error('Dominio no encontrado.');
            $this->redirect('/domains');
        }
        $this->view('domains/form', [
            'title' => 'Editar dominio',
            'domain' => $domain,
            'action' => '/domains/update',
        ]);
    }

    public function update(): void
    {
        Middleware::roles(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData('/domains/edit?id=' . $id);
        if ($this->domains->codeExists($data['code'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un dominio con ese codigo.');
            $this->redirect('/domains/edit?id=' . $id);
        }

        $this->domains->update($id, $data);
        Flash::success('Dominio actualizado correctamente.');
        $this->redirect('/domains');
    }

    public function toggle(): void
    {
        Middleware::roles(['admin']);
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/domains');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';
        $this->domains->setStatus($id, $status);
        Flash::success('Estado del dominio actualizado.');
        $this->redirect('/domains');
    }

    private function validatedData(string $failureRedirect): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/domains');
        }

        $data = [
            'code' => trim((string) ($_POST['code'] ?? '')),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'active'),
        ];

        $validator = (new Validator())
            ->required('code', $data['code'], 'Codigo')
            ->required('name', $data['name'], 'Nombre')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($failureRedirect);
        }

        return $data;
    }
}
