<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Recommendation extends BaseModel
{
    public function forAudit(int $auditId): array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, c.code AS control_code, c.title AS control_title
             FROM recommendations r
             INNER JOIN iso_controls c ON c.id = r.control_id
             WHERE r.audit_id = :audit_id
             ORDER BY r.status ASC, r.due_date ASC'
        );
        $statement->execute(['audit_id' => $auditId]);
        return $statement->fetchAll();
    }

    public function allPending(): array
    {
        return $this->db->query(
            'SELECT r.*, c.code AS control_code, c.title AS control_title,
                    au.name AS audit_name, o.name AS organization_name
             FROM recommendations r
             INNER JOIN iso_controls c  ON c.id  = r.control_id
             INNER JOIN audits au       ON au.id = r.audit_id
             INNER JOIN organizations o ON o.id  = au.organization_id
             WHERE r.status != "done"
             ORDER BY r.due_date ASC, r.status ASC'
        )->fetchAll();
    }

    public function countByStatus(): array
    {
        $row = $this->db->query(
            'SELECT
                SUM(status = "pending")     AS pending,
                SUM(status = "in_progress") AS in_progress,
                SUM(status = "done")        AS done
             FROM recommendations'
        )->fetch();
        return $row ?: ['pending' => 0, 'in_progress' => 0, 'done' => 0];
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT r.*, c.code AS control_code, c.title AS control_title,
                    au.name AS audit_name
             FROM recommendations r
             INNER JOIN iso_controls c ON c.id  = r.control_id
             INNER JOIN audits au      ON au.id = r.audit_id
             WHERE r.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO recommendations (audit_id, control_id, description, responsible, due_date, status, notes)
             VALUES (:audit_id, :control_id, :description, :responsible, :due_date, :status, :notes)'
        );
        return $statement->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement  = $this->db->prepare(
            'UPDATE recommendations
             SET description = :description, responsible = :responsible,
                 due_date = :due_date, status = :status, notes = :notes
             WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM recommendations WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }
}
