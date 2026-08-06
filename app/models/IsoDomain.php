<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class IsoDomain extends BaseModel
{
    private array $sortable = ['code', 'name', 'status', 'created_at'];

    public function paginateList(string $search, string $sort, string $direction, int $page, int $perPage = 10): array
    {
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = 'WHERE d.code LIKE :search OR d.name LIKE :search OR d.description LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $sql = "SELECT d.*, COUNT(c.id) AS controls_count
                FROM iso_domains d
                LEFT JOIN iso_controls c ON c.domain_id = d.id
                {$where}
                GROUP BY d.id
                ORDER BY d.{$sort} {$direction}";
        $countSql = "SELECT COUNT(*) FROM iso_domains d {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function activeOptions(): array
    {
        $statement = $this->db->prepare('SELECT id, code, name FROM iso_domains WHERE status = :status ORDER BY code ASC');
        $statement->execute(['status' => 'active']);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT d.*, COUNT(c.id) AS controls_count
             FROM iso_domains d
             LEFT JOIN iso_controls c ON c.domain_id = d.id
             WHERE d.id = :id
             GROUP BY d.id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $domain = $statement->fetch();
        return $domain ?: null;
    }

    public function codeExists(string $code, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM iso_domains WHERE code = :code';
        $params = ['code' => $code];
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
            'INSERT INTO iso_domains (code, name, description, status) VALUES (:code, :name, :description, :status)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE iso_domains SET code = :code, name = :name, description = :description, status = :status WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE iso_domains SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }
}
