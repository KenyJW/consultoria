<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Response extends BaseModel
{
    /**
     * Upsert de respuesta Sí/No/No aplica por pregunta.
     * No toca la columna "recommendation": ese campo por pregunta quedo
     * retirado del cuestionario (el modulo de Recomendaciones, a nivel de
     * control, lo reemplaza), asi que ya no se sobreescribe aqui.
     */
    public function upsert(int $auditId, int $questionId, array $data): int
{
    $statement = $this->db->prepare(
        'INSERT INTO responses (
            audit_id,
            question_id,
            answer,
            maturity_level,
            justification
        )
         VALUES (
            :audit_id,
            :question_id,
            :answer,
            :maturity_level,
            :justification
         )
         ON DUPLICATE KEY UPDATE
            answer         = VALUES(answer),
            maturity_level = VALUES(maturity_level),
            justification  = VALUES(justification)'
    );

    $statement->execute([
        'audit_id'       => $auditId,
        'question_id'    => $questionId,
        'answer'         => $data['answer'],
        'maturity_level' => $data['maturity_level'] ?? null,
        'justification'  => $data['justification'] ?? null,
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
