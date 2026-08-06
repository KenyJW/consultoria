<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class IsoControl extends BaseModel
{
    private array $sortable = ['code', 'title', 'domain_name', 'status', 'created_at'];

    public function paginateList(string $search, int $domainId, string $sort, string $direction, int $page, int $perPage = 10): array
    {
        $sortMap = [
            'code' => 'c.code',
            'title' => 'c.title',
            'domain_name' => 'd.name',
            'status' => 'c.status',
            'created_at' => 'c.created_at',
        ];
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $whereParts = [];

        if ($search !== '') {
            $whereParts[] = '(c.code LIKE :search OR c.title LIKE :search OR c.description LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($domainId > 0) {
            $whereParts[] = 'c.domain_id = :domain_id';
            $params[':domain_id'] = $domainId;
        }

        $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';
        $sql = "SELECT c.*, d.name AS domain_name, d.code AS domain_code, COUNT(q.id) AS questions_count
                FROM iso_controls c
                LEFT JOIN iso_domains d ON d.id = c.domain_id
                LEFT JOIN questions q ON q.control_id = c.id
                {$where}
                GROUP BY c.id
                ORDER BY {$sortMap[$sort]} {$direction}";
        $countSql = "SELECT COUNT(*) FROM iso_controls c LEFT JOIN iso_domains d ON d.id = c.domain_id {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function activeOptions(): array
    {
        $statement = $this->db->prepare('SELECT id, code, title FROM iso_controls WHERE status = :status ORDER BY code ASC');
        $statement->execute(['status' => 'active']);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT c.*, d.name AS domain_name, d.code AS domain_code, COUNT(q.id) AS questions_count
             FROM iso_controls c
             LEFT JOIN iso_domains d ON d.id = c.domain_id
             LEFT JOIN questions q ON q.control_id = c.id
             WHERE c.id = :id
             GROUP BY c.id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $control = $statement->fetch();
        return $control ?: null;
    }

    public function codeExists(string $code, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM iso_controls WHERE code = :code';
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
            'INSERT INTO iso_controls (domain_id, code, title, description, objective, weight, confidentiality, integrity, availability, status)
             VALUES (:domain_id, :code, :title, :description, :objective, :weight, :confidentiality, :integrity, :availability, :status)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE iso_controls
             SET domain_id = :domain_id, code = :code, title = :title, description = :description,
                 objective = :objective, weight = :weight, confidentiality = :confidentiality,
                 integrity = :integrity, availability = :availability, status = :status
             WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE iso_controls SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }
}
