<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Area extends BaseModel
{
    private array $sortable = ['name', 'organization_name', 'status', 'created_at'];

    public function paginateList(string $search, int $organizationId, string $sort, string $direction, int $page, int $perPage = 10): array
    {
        $sortMap = [
            'name' => 'a.name',
            'organization_name' => 'o.name',
            'status' => 'a.status',
            'created_at' => 'a.created_at',
        ];
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $whereParts = [];

        if ($search !== '') {
            $whereParts[] = '(a.name LIKE :search OR a.description LIKE :search OR o.name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($organizationId > 0) {
            $whereParts[] = 'a.organization_id = :organization_id';
            $params[':organization_id'] = $organizationId;
        }

        $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';
        $sql = "SELECT a.*, o.name AS organization_name
                FROM areas a
                INNER JOIN organizations o ON o.id = a.organization_id
                {$where}
                ORDER BY {$sortMap[$sort]} {$direction}";
        $countSql = "SELECT COUNT(*)
                     FROM areas a
                     INNER JOIN organizations o ON o.id = a.organization_id
                     {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT a.*, o.name AS organization_name
             FROM areas a
             INNER JOIN organizations o ON o.id = a.organization_id
             WHERE a.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $area = $statement->fetch();
        return $area ?: null;
    }

    public function nameExists(int $organizationId, string $name, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM areas WHERE organization_id = :organization_id AND name = :name';
        $params = ['organization_id' => $organizationId, 'name' => $name];
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
            'INSERT INTO areas (organization_id, name, description, status) VALUES (:organization_id, :name, :description, :status)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE areas SET organization_id = :organization_id, name = :name, description = :description, status = :status WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE areas SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }
}
