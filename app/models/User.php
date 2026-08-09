<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;
use PDO;

final class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, name, email, role, status, organization_id, created_at FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    /** Todos los usuarios (vista de administrador). */
    public function all(): array
    {
        $statement = $this->db->query(
            'SELECT u.id, u.name, u.email, u.role, u.status, u.organization_id, u.created_at,
                    o.name AS organization_name
             FROM users u
             LEFT JOIN organizations o ON o.id = u.organization_id
             ORDER BY u.id DESC'
        );
        return $statement->fetchAll();
    }

    /** Usuarios de una organizacion especifica (vista de empresa: "Mi equipo"). */
    public function forOrganization(int $organizationId): array
    {
        $statement = $this->db->prepare(
            'SELECT id, name, email, role, status, organization_id, created_at
             FROM users WHERE organization_id = :organization_id ORDER BY id DESC'
        );
        $statement->execute(['organization_id' => $organizationId]);
        return $statement->fetchAll();
    }

    /** Auditores disponibles para asignar a una auditoria: personal de la consultora (sin organizacion) + los de la organizacion dada. */
    public function auditors(?int $organizationId = null): array
    {
        $sql = 'SELECT u.id, u.name, u.email, o.name AS organization_name
                FROM users u
                LEFT JOIN organizations o ON o.id = u.organization_id
                WHERE u.role = :role AND u.status = :status
                  AND (u.organization_id IS NULL';
        $params = ['role' => 'auditor', 'status' => 'active'];
        if ($organizationId !== null) {
            $sql .= ' OR u.organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }
        $sql .= ') ORDER BY (u.organization_id IS NULL) DESC, u.name ASC';

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = ['email' => $email];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $ignoreId;
        }
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public function create(array $data): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, status, organization_id)
             VALUES (:name, :email, :password, :role, :status, :organization_id)'
        );

        return $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status'],
            'organization_id' => $data['organization_id'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $fields = 'name = :name, email = :email, role = :role, status = :status, organization_id = :organization_id';
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'organization_id' => $data['organization_id'] ?? null,
        ];

        if (! empty($data['password'])) {
            $fields .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $statement = $this->db->prepare("UPDATE users SET {$fields} WHERE id = :id");
        return $statement->execute($params);
    }

    /** Alta de usuario via autoregistro publico (/register): rol auditor, ligado a su propia organizacion. */
    public function createScoped(string $name, string $email, string $password, int $organizationId): int
    {
        $statement = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, status, organization_id)
             VALUES (:name, :email, :password, 'auditor', 'active', :organization_id)"
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'organization_id' => $organizationId,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('UPDATE users SET status = "inactive" WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }

    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (! $user || $user['status'] !== 'active') {
            return null;
        }

        if ($user['password'] === 'BOOTSTRAP_ADMIN_PASSWORD') {
            $initialPassword = getenv('INITIAL_ADMIN_PASSWORD') ?: 'Admin123*';
            if (! hash_equals($initialPassword, $password)) {
                return null;
            }
            $this->updatePassword((int) $user['id'], $password);
        } elseif (! password_verify($password, $user['password'])) {
            return null;
        }

        $this->markLogin((int) $user['id']);
        unset($user['password']);
        return $user;
    }

    private function updatePassword(int $id, string $password): void
    {
        $statement = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        $statement->execute([
            'id' => $id,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    private function markLogin(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
