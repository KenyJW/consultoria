<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Question extends BaseModel
{
    private array $sortable = ['question', 'control_code', 'status', 'created_at'];

    public function paginateList(string $search, int $controlId, string $sort, string $direction, int $page, int $perPage = 10): array
    {
        $sortMap = [
            'question' => 'q.question',
            'control_code' => 'c.code',
            'status' => 'q.status',
            'created_at' => 'q.created_at',
        ];
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $whereParts = [];

        if ($search !== '') {
            $whereParts[] = '(q.question LIKE :search OR c.code LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($controlId > 0) {
            $whereParts[] = 'q.control_id = :control_id';
            $params[':control_id'] = $controlId;
        }

        $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';
        $sql = "SELECT q.*, c.code AS control_code, c.title AS control_title
                FROM questions q
                INNER JOIN iso_controls c ON c.id = q.control_id
                {$where}
                ORDER BY {$sortMap[$sort]} {$direction}";
        $countSql = "SELECT COUNT(*) FROM questions q INNER JOIN iso_controls c ON c.id = q.control_id {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT q.*, c.code AS control_code, c.title AS control_title
             FROM questions q
             INNER JOIN iso_controls c ON c.id = q.control_id
             WHERE q.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $question = $statement->fetch();
        return $question ?: null;
    }

    public function create(array $data): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO questions (control_id, question, weight, status) VALUES (:control_id, :question, :weight, :status)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE questions SET control_id = :control_id, question = :question, weight = :weight, status = :status WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE questions SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }
}
