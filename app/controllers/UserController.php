<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\ErrorHandler;
use App\Core\Flash;
use App\Core\Middleware;
use App\Core\Validator;
use App\Models\Organization;
use App\Models\User;

/**
 * Gestion de usuarios. El admin ve y gestiona a todos; un usuario con rol
 * "auditor" ligado a una organizacion (Auth::organizationId() no nulo) solo
 * ve y gestiona el equipo de su propia organizacion ("Mi equipo") — no
 * puede crear administradores ni tocar usuarios de otras organizaciones.
 * El personal de la consultora sin organizacion asignada (auditores
 * globales) y los usuarios "viewer" no tienen acceso a esta seccion.
 */
final class UserController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->requireManageAccess();
        $scopedOrgId = Auth::organizationId();

        $this->view('users/index', [
            'title' => $scopedOrgId !== null ? 'Mi equipo' : 'Usuarios',
            'users' => $scopedOrgId !== null ? $this->users->forOrganization($scopedOrgId) : $this->users->all(),
            'scoped' => $scopedOrgId !== null,
        ]);
        unset($_SESSION['_old']);
    }

    public function create(): void
    {
        $this->requireManageAccess();
        $this->view('users/form', [
            'title' => 'Nuevo usuario',
            'user' => null,
            'action' => '/users/store',
            'scoped' => Auth::organizationId() !== null,
            'organizations' => (new Organization())->activeOptions(),
        ]);
    }

    public function store(): void
    {
        $this->requireManageAccess();
        $data = $this->validatedData(true);
        if ($this->users->emailExists($data['email'])) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un usuario con ese correo.');
            $this->redirect('/users/create');
        }

        $this->users->create($data);
        Flash::success('Usuario creado correctamente.');
        $this->redirect('/users');
    }

    public function edit(): void
    {
        $this->requireManageAccess();
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->users->find($id);
        if (! $user) {
            Flash::error('Usuario no encontrado.');
            $this->redirect('/users');
        }
        $this->requireSameOrganization($user);

        $this->view('users/form', [
            'title' => 'Editar usuario',
            'user' => $user,
            'action' => '/users/update',
            'scoped' => Auth::organizationId() !== null,
            'organizations' => (new Organization())->activeOptions(),
        ]);
    }

    public function update(): void
    {
        $this->requireManageAccess();
        $id = (int) ($_POST['id'] ?? 0);
        $current = $this->users->find($id);
        if (! $current) { Flash::error('Usuario no encontrado.'); $this->redirect('/users'); }
        $this->requireSameOrganization($current);

        $data = $this->validatedData(false, $id);
        if ($this->users->emailExists($data['email'], $id)) {
            $_SESSION['_old'] = $data;
            Flash::error('Ya existe un usuario con ese correo.');
            $this->redirect('/users/edit?id=' . $id);
        }

        $this->users->update($id, $data);
        Flash::success('Usuario actualizado correctamente.');
        $this->redirect('/users');
    }

    public function destroy(): void
    {
        $this->requireManageAccess();
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/users');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $current = $this->users->find($id);
        if ($current) {
            $this->requireSameOrganization($current);
        }
        if ($id !== (int) (Auth::user()['id'] ?? 0) && $current) {
            $this->users->delete($id);
            Flash::success('Usuario desactivado correctamente.');
        }
        $this->redirect('/users');
    }

    /** Admin: acceso total. Auditor ligado a una organizacion: acceso a esta seccion, acotado despues por organizacion. Todo lo demas: bloqueado. */
    private function requireManageAccess(): void
    {
        Middleware::auth();
        $user = Auth::user();
        $isAdmin = ($user['role'] ?? null) === 'admin';
        $isOrgAuditor = ($user['role'] ?? null) === 'auditor' && Auth::organizationId() !== null;
        if (! $isAdmin && ! $isOrgAuditor) {
            ErrorHandler::render(403, 'No tiene permisos para acceder a este recurso.');
            exit;
        }
    }

    /** Un auditor de organizacion solo puede tocar usuarios de su misma organizacion. Admin no tiene restriccion. */
    private function requireSameOrganization(array $targetUser): void
    {
        $scopedOrgId = Auth::organizationId();
        if ($scopedOrgId === null) {
            return;
        }
        $targetOrgId = isset($targetUser['organization_id']) ? (int) $targetUser['organization_id'] : null;
        if ($targetOrgId !== $scopedOrgId) {
            ErrorHandler::render(403, 'No tiene permisos para acceder a este recurso.');
            exit;
        }
    }

    private function validatedData(bool $requirePassword, ?int $id = null): array
    {
        if (! Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error('La sesion expiro.');
            $this->redirect('/users');
        }

        $scopedOrgId = Auth::organizationId();
        $allowedRoles = $scopedOrgId !== null ? ['auditor', 'viewer'] : ['admin', 'auditor', 'viewer'];

        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'role' => (string) ($_POST['role'] ?? 'auditor'),
            'status' => (string) ($_POST['status'] ?? 'active'),
            'organization_id' => $scopedOrgId,
        ];

        if ($scopedOrgId === null) {
            // Solo el admin puede ligar (o no) un usuario a una organizacion especifica.
            $orgRaw = (int) ($_POST['organization_id'] ?? 0);
            $data['organization_id'] = $orgRaw > 0 ? $orgRaw : null;
        }

        $validator = (new Validator())
            ->required('name', $data['name'], 'Nombre')
            ->email('email', $data['email'], 'Correo')
            ->in('role', $data['role'], $allowedRoles, 'Rol')
            ->in('status', $data['status'], ['active', 'inactive'], 'Estado');

        if ($requirePassword) {
            $validator->minLength('password', $data['password'], 8, 'Contrasena');
        }

        if ($validator->fails()) {
            $_SESSION['_old'] = $data;
            Flash::errors($validator->errors());
            $this->redirect($id ? '/users/edit?id=' . $id : '/users/create');
        }

        return $data;
    }
}
