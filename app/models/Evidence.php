<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Evidence extends BaseModel
{
    public function create(array $data): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO evidences (response_id, original_name, stored_name, mime_type, size_bytes)
             VALUES (:response_id, :original_name, :stored_name, :mime_type, :size_bytes)'
        );
        return $statement->execute($data);
    }

    public function forResponse(int $responseId): array
    {
        $statement = $this->db->prepare('SELECT * FROM evidences WHERE response_id = :response_id ORDER BY id DESC');
        $statement->execute(['response_id' => $responseId]);
        return $statement->fetchAll();
    }

    /** Todas las evidencias de una auditoria, indexadas por question_id (para pintar el cuestionario). */
    public function forAuditByQuestion(int $auditId): array
    {
        $statement = $this->db->prepare(
            'SELECT e.*, r.question_id
             FROM evidences e
             INNER JOIN responses r ON r.id = e.response_id
             WHERE r.audit_id = :audit_id
             ORDER BY e.id DESC'
        );
        $statement->execute(['audit_id' => $auditId]);

        $byQuestion = [];
        foreach ($statement->fetchAll() as $row) {
            $byQuestion[(int) $row['question_id']][] = $row;
        }
        return $byQuestion;
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM evidences WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $evidence = $statement->fetch();
        return $evidence ?: null;
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM evidences WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }
}
