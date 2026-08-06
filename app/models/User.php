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
        $statement = $this->db->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function all(): array
    {
        $statement = $this->db->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function auditors(): array
    {
        $statement = $this->db->prepare('SELECT id, name, email FROM users WHERE role = :role AND status = :status ORDER BY name ASC');
        $statement->execute(['role' => 'auditor', 'status' => 'active']);
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
            'INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)'
        );

        return $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $fields = 'name = :name, email = :email, role = :role, status = :status';
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];

        if (! empty($data['password'])) {
            $fields .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $statement = $this->db->prepare("UPDATE users SET {$fields} WHERE id = :id");
        return $statement->execute($params);
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
