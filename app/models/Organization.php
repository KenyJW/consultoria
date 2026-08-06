<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Organization extends BaseModel
{
    private array $sortable = ['name', 'email', 'status', 'created_at'];

    public function paginateList(string $search, string $sort, string $direction, int $page, int $perPage = 10): array
    {
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = 'WHERE o.name LIKE :search OR o.email LIKE :search OR o.address LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $sql = "SELECT o.*, COUNT(a.id) AS areas_count
                FROM organizations o
                LEFT JOIN areas a ON a.organization_id = o.id
                {$where}
                GROUP BY o.id
                ORDER BY o.{$sort} {$direction}";
        $countSql = "SELECT COUNT(*) FROM organizations o {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function activeOptions(): array
    {
        $statement = $this->db->prepare('SELECT id, name FROM organizations WHERE status = :status ORDER BY name ASC');
        $statement->execute(['status' => 'active']);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT o.*, COUNT(a.id) AS areas_count
             FROM organizations o
             LEFT JOIN areas a ON a.organization_id = o.id
             WHERE o.id = :id
             GROUP BY o.id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $organization = $statement->fetch();
        return $organization ?: null;
    }

    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM organizations WHERE name = :name';
        $params = ['name' => $name];
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
            'INSERT INTO organizations (name, address, email, status) VALUES (:name, :address, :email, :status)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE organizations SET name = :name, address = :address, email = :email, status = :status WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE organizations SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM organizations WHERE status = "active"')->fetchColumn();
    }
}
