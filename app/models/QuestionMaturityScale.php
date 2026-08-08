<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class QuestionMaturityScale extends BaseModel
{
    /** Las 6 descripciones (niveles 0-5) de una pregunta, indexadas por nivel. */
    public function forQuestion(int $questionId): array
    {
        $statement = $this->db->prepare(
            'SELECT level, description FROM question_maturity_scale
             WHERE question_id = :question_id ORDER BY level ASC'
        );
        $statement->execute(['question_id' => $questionId]);

        $map = [];
        foreach ($statement->fetchAll() as $row) {
            $map[(int) $row['level']] = $row['description'];
        }
        return $map;
    }

    /** Las 6 descripciones de TODAS las preguntas activas, indexadas por question_id => [level => texto]. */
    public function forAllQuestions(): array
    {
        $statement = $this->db->query(
            'SELECT question_id, level, description FROM question_maturity_scale ORDER BY question_id ASC, level ASC'
        );

        $map = [];
        foreach ($statement->fetchAll() as $row) {
            $map[(int) $row['question_id']][(int) $row['level']] = $row['description'];
        }
        return $map;
    }

    /** Reemplaza las 6 descripciones de una pregunta (usado por el CRUD de Preguntas). */
    public function replaceForQuestion(int $questionId, array $descriptionsByLevel): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO question_maturity_scale (question_id, level, description)
             VALUES (:question_id, :level, :description)
             ON DUPLICATE KEY UPDATE description = VALUES(description)'
        );

        for ($level = 0; $level <= 5; $level++) {
            $statement->execute([
                'question_id' => $questionId,
                'level'       => $level,
                'description' => trim((string) ($descriptionsByLevel[$level] ?? '')),
            ]);
        }
    }
}
