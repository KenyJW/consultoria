<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

final class Audit extends BaseModel
{
    private array $sortable = ['name', 'organization_name', 'status', 'start_date', 'created_at'];

    /** @param int[]|null $allowedOrgIds Si no es null, restringe a esas organizaciones (auditor con alcance limitado). */
    public function paginateList(string $search, int $organizationId, string $status, string $sort, string $direction, int $page, int $perPage = 10, ?array $allowedOrgIds = null): array
    {
        $sortMap = [
            'name' => 'au.name',
            'organization_name' => 'o.name',
            'status' => 'au.status',
            'start_date' => 'au.start_date',
            'created_at' => 'au.created_at',
        ];
        $sort = in_array($sort, $this->sortable, true) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        $params = [];
        $whereParts = [];

        if ($search !== '') {
            $whereParts[] = '(au.name LIKE :search OR o.name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        if ($organizationId > 0) {
            $whereParts[] = 'au.organization_id = :organization_id';
            $params[':organization_id'] = $organizationId;
        }
        if (in_array($status, ['draft', 'in_progress', 'closed', 'cancelled'], true)) {
            $whereParts[] = 'au.status = :status';
            $params[':status'] = $status;
        }
        if ($allowedOrgIds !== null) {
            $whereParts[] = $allowedOrgIds === []
                ? '1 = 0'
                : 'au.organization_id IN (' . implode(',', array_map('intval', $allowedOrgIds)) . ')';
        }

        $where = $whereParts ? 'WHERE ' . implode(' AND ', $whereParts) : '';
        $sql = "SELECT au.*, o.name AS organization_name, a.name AS area_name, u.name AS auditor_name,
                       u.organization_id AS auditor_organization_id
                FROM audits au
                INNER JOIN organizations o ON o.id = au.organization_id
                INNER JOIN areas a ON a.id = au.area_id
                INNER JOIN users u ON u.id = au.auditor_user_id
                {$where}
                ORDER BY {$sortMap[$sort]} {$direction}";
        $countSql = "SELECT COUNT(*)
                     FROM audits au
                     INNER JOIN organizations o ON o.id = au.organization_id
                     {$where}";

        return $this->paginate($sql, $countSql, $params, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT au.*, o.name AS organization_name, a.name AS area_name, u.name AS auditor_name,
                    u.organization_id AS auditor_organization_id
             FROM audits au
             INNER JOIN organizations o ON o.id = au.organization_id
             INNER JOIN areas a ON a.id = au.area_id
             INNER JOIN users u ON u.id = au.auditor_user_id
             WHERE au.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $audit = $statement->fetch();
        return $audit ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO audits (organization_id, area_id, auditor_user_id, dba_name, name, status, start_date, end_date)
             VALUES (:organization_id, :area_id, :auditor_user_id, :dba_name, :name, :status, :start_date, :end_date)'
        );
        $statement->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE audits
             SET organization_id = :organization_id, area_id = :area_id, auditor_user_id = :auditor_user_id,
                 dba_name = :dba_name, name = :name, start_date = :start_date, end_date = :end_date
             WHERE id = :id'
        );
        return $statement->execute($data);
    }

    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->db->prepare('UPDATE audits SET status = :status WHERE id = :id');
        return $statement->execute(['id' => $id, 'status' => $status]);
    }

    public function close(int $id, ?float $maturityScore, ?float $riskScore, ?float $riskC, ?float $riskI, ?float $riskD): bool
    {
        $statement = $this->db->prepare(
            'UPDATE audits SET status = :status, end_date = CURDATE(),
             maturity_score = :maturity, risk_score = :risk,
             risk_c = :risk_c, risk_i = :risk_i, risk_d = :risk_d
             WHERE id = :id'
        );
        return $statement->execute([
            'id'      => $id,
            'status'  => 'closed',
            'maturity'=> $maturityScore,
            'risk'    => $riskScore,
            'risk_c'  => $riskC,
            'risk_i'  => $riskI,
            'risk_d'  => $riskD,
        ]);
    }

    /**
     * Carga el cuestionario completo (dominios -> controles -> preguntas activas)
     * junto con las respuestas ya guardadas para esta auditoria.
     */
    public function questionnaire(int $auditId): array
{
    $statement = $this->db->prepare(
        'SELECT
            d.id AS domain_id,
            d.code AS domain_code,
            d.name AS domain_name,
            d.description AS domain_description,

            c.id AS control_id,
            c.code AS control_code,
            c.title AS control_title,
            c.description AS control_description,
            c.objective AS control_objective,
            c.iso_reference,
            c.weight AS control_weight,
            c.confidentiality,
            c.integrity,
            c.availability,

            q.id AS question_id,
            q.question AS question_text,
            q.weight AS question_weight,

            r.id AS response_id,
            r.answer,
            r.maturity_level,
            r.justification,
            r.recommendation

         FROM iso_controls c
         INNER JOIN iso_domains d ON d.id = c.domain_id
         INNER JOIN questions q ON q.control_id = c.id AND q.status = "active"
         LEFT JOIN responses r ON r.question_id = q.id AND r.audit_id = :audit_id
         WHERE c.status = "active" AND d.status = "active"
         ORDER BY d.code ASC, c.code ASC, q.id ASC'
    );

    $statement->execute([
        'audit_id' => $auditId
    ]);

    return $statement->fetchAll();
}
    /**
     * Estadísticas para el dashboard.
     * @param int|null $organizationId Si se indica, limita a esa organización (usuarios autoregistrados).
     * @param int[]|null $allowedOrgIds Si no es null (y no se indicó $organizationId), restringe a esas organizaciones.
     */
    public function stats(?int $organizationId = null, ?array $allowedOrgIds = null): array
    {
        $where = '';
        $params = [];
        if ($organizationId !== null) {
            $where = 'WHERE organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        } elseif ($allowedOrgIds !== null) {
            $where = $allowedOrgIds === []
                ? 'WHERE 1 = 0'
                : 'WHERE organization_id IN (' . implode(',', array_map('intval', $allowedOrgIds)) . ')';
        }

        $statement = $this->db->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'closed') AS closed,
                SUM(status = 'in_progress') AS in_progress,
                SUM(status = 'draft') AS draft,
                ROUND(AVG(CASE WHEN maturity_score IS NOT NULL THEN maturity_score END), 2) AS avg_maturity,
                ROUND(AVG(CASE WHEN risk_score IS NOT NULL THEN risk_score END), 2) AS avg_risk
             FROM audits
             {$where}"
        );
        $statement->execute($params);
        $row = $statement->fetch();
        return $row ?: [];
    }

    /** Últimas auditorías cerradas con scores para el dashboard. */
    public function recentClosed(int $limit = 5, ?int $organizationId = null): array
    {
        $where = 'WHERE au.status = "closed" AND au.maturity_score IS NOT NULL';
        if ($organizationId !== null) {
            $where .= ' AND au.organization_id = :organization_id';
        }

        $statement = $this->db->prepare(
            "SELECT au.id, au.name, au.maturity_score, au.risk_score, au.end_date,
                    o.name AS organization_name
             FROM audits au
             INNER JOIN organizations o ON o.id = au.organization_id
             {$where}
             ORDER BY au.end_date DESC
             LIMIT :lim"
        );
        if ($organizationId !== null) {
            $statement->bindValue(':organization_id', $organizationId, \PDO::PARAM_INT);
        }
        $statement->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    /** Madurez promedio por dominio (para heatmap/gráfico del dashboard). */
    public function maturityByDomain(?int $organizationId = null): array
    {
        $where = '';
        $params = [];
        if ($organizationId !== null) {
            $where = 'WHERE au.organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        $statement = $this->db->prepare(
            "SELECT d.name AS domain_name, d.code AS domain_code,
                    ROUND(AVG(acm.maturity_level), 2) AS avg_maturity
             FROM audit_control_maturity acm
             INNER JOIN iso_controls c ON c.id = acm.control_id
             INNER JOIN iso_domains d ON d.id = c.domain_id
             INNER JOIN audits au ON au.id = acm.audit_id
             {$where}
             GROUP BY d.id
             ORDER BY d.code ASC"
        );
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /** Cantidad de preguntas activas y cuántas ya tienen respuesta guardada. */
    public function progress(int $auditId): array
    {
        $total = (int) $this->db->query(
            'SELECT COUNT(*) FROM questions q
             INNER JOIN iso_controls c ON c.id = q.control_id AND c.status = "active"
             INNER JOIN iso_domains d ON d.id = c.domain_id AND d.status = "active"
             WHERE q.status = "active"'
        )->fetchColumn();

        $statement = $this->db->prepare('SELECT COUNT(*) FROM responses WHERE audit_id = :audit_id AND answer IS NOT NULL');
        $statement->execute(['audit_id' => $auditId]);
        $answered = (int) $statement->fetchColumn();

        return [
            'total'    => $total,
            'answered' => $answered,
            'percent'  => $total > 0 ? (int) round($answered / $total * 100) : 0,
        ];
    }
}
