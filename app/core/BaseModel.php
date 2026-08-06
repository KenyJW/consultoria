<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function paginate(string $sql, string $countSql, array $params, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(5, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $count = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $count->bindValue($key, $value);
        }
        $count->execute();
        $total = (int) $count->fetchColumn();

        $statement = $this->db->prepare($sql . ' LIMIT :limit OFFSET :offset');
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
