<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class AuditControlMaturity extends BaseModel
{
    /** Upsert del nivel de madurez asignado a un control dentro de una auditoría. */
    public function upsert(int $auditId, int $controlId, int $maturityLevel): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO audit_control_maturity (audit_id, control_id, maturity_level)
             VALUES (:audit_id, :control_id, :maturity_level)
             ON DUPLICATE KEY UPDATE maturity_level = VALUES(maturity_level)'
        );
        $statement->execute([
            'audit_id'      => $auditId,
            'control_id'    => $controlId,
            'maturity_level'=> $maturityLevel,
        ]);
    }

    /**
     * Devuelve los niveles de madurez de todos los controles de una auditoría
     * junto con los metadatos del control necesarios para el cálculo.
     */
    public function forAudit(int $auditId): array
    {
        $statement = $this->db->prepare(
            'SELECT acm.control_id, acm.maturity_level,
                    c.weight, c.confidentiality, c.integrity, c.availability, c.domain_id,
                    c.code AS control_code, c.title AS control_title,
                    d.name AS domain_name, d.code AS domain_code
             FROM audit_control_maturity acm
             INNER JOIN iso_controls c ON c.id = acm.control_id
             INNER JOIN iso_domains  d ON d.id = c.domain_id
             WHERE acm.audit_id = :audit_id
             ORDER BY d.code ASC, c.code ASC'
        );
        $statement->execute(['audit_id' => $auditId]);
        return $statement->fetchAll();
    }

    /** Madurez de un control específico en una auditoría (para pre-rellenar el formulario). */
    public function getLevel(int $auditId, int $controlId): int
    {
        $statement = $this->db->prepare(
            'SELECT maturity_level FROM audit_control_maturity
             WHERE audit_id = :audit_id AND control_id = :control_id LIMIT 1'
        );
        $statement->execute(['audit_id' => $auditId, 'control_id' => $controlId]);
        return (int) $statement->fetchColumn();
    }

    /** Todos los niveles de una auditoría indexados por control_id. */
    public function mapForAudit(int $auditId): array
    {
        $rows = $this->forAudit($auditId);
        $map  = [];
        foreach ($rows as $row) {
            $map[(int) $row['control_id']] = (int) $row['maturity_level'];
        }
        return $map;
    }
}
