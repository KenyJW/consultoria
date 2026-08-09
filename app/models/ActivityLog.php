<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

/** Bitácora de cambios: registra quién hizo qué acción sensible y cuándo. */
final class ActivityLog extends BaseModel
{
    public function record(?int $auditId, ?int $userId, string $action, string $description): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO activity_log (audit_id, user_id, action, description)
             VALUES (:audit_id, :user_id, :action, :description)'
        );
        $statement->execute([
            'audit_id'    => $auditId,
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
        ]);
    }

    /** Historial de una auditoría específica, más reciente primero. */
    public function forAudit(int $auditId): array
    {
        $statement = $this->db->prepare(
            'SELECT l.*, u.name AS user_name
             FROM activity_log l
             LEFT JOIN users u ON u.id = l.user_id
             WHERE l.audit_id = :audit_id
             ORDER BY l.created_at DESC, l.id DESC'
        );
        $statement->execute(['audit_id' => $auditId]);
        return $statement->fetchAll();
    }
}
