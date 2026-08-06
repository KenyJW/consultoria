<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Response extends BaseModel
{
    /** Upsert de respuesta Sí/No/No aplica por pregunta. */
    public function upsert(int $auditId, int $questionId, array $data): int
{
    $statement = $this->db->prepare(
        'INSERT INTO responses (
            audit_id,
            question_id,
            answer,
            justification,
            recommendation
        )
         VALUES (
            :audit_id,
            :question_id,
            :answer,
            :justification,
            :recommendation
         )
         ON DUPLICATE KEY UPDATE
            answer         = VALUES(answer),
            justification  = VALUES(justification),
            recommendation = VALUES(recommendation)'
    );

    $statement->execute([
        'audit_id'       => $auditId,
        'question_id'    => $questionId,
        'answer'         => $data['answer'],
        'justification'  => $data['observation'] ?? null,
        'recommendation' => $data['recommendation'] ?? null,
    ]);

    return $this->findId($auditId, $questionId);
}

    public function findId(int $auditId, int $questionId): int
    {
        $statement = $this->db->prepare(
            'SELECT id FROM responses WHERE audit_id = :audit_id AND question_id = :question_id LIMIT 1'
        );
        $statement->execute(['audit_id' => $auditId, 'question_id' => $questionId]);
        return (int) $statement->fetchColumn();
    }

    /** Todas las respuestas de una auditoría con datos del control (para cálculos). */
    public function forAudit(int $auditId): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, q.control_id
             FROM responses r
             INNER JOIN questions q ON q.id = r.question_id
             WHERE r.audit_id = :audit_id'
        );
        $statement->execute(['audit_id' => $auditId]);
        return $statement->fetchAll();
    }
}
