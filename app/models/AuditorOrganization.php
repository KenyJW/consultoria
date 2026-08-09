<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\BaseModel;

/** Que organizaciones cliente tiene asignadas cada auditor global de la consultora. */
final class AuditorOrganization extends BaseModel
{
    /** @return int[] */
    public function organizationIdsForUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT organization_id FROM auditor_organizations WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        return array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** Organizaciones asignadas, con nombre (para mostrarlas en el formulario de usuario). */
    public function organizationsForUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT o.id, o.name
             FROM auditor_organizations ao
             INNER JOIN organizations o ON o.id = ao.organization_id
             WHERE ao.user_id = :user_id
             ORDER BY o.name ASC'
        );
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }

    /** Reemplaza el conjunto de organizaciones asignadas a un auditor por la lista dada. */
    public function syncForUser(int $userId, array $organizationIds): void
    {
        $delete = $this->db->prepare('DELETE FROM auditor_organizations WHERE user_id = :user_id');
        $delete->execute(['user_id' => $userId]);

        if ($organizationIds === []) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO auditor_organizations (user_id, organization_id) VALUES (:user_id, :organization_id)'
        );
        foreach (array_unique(array_map('intval', $organizationIds)) as $organizationId) {
            if ($organizationId > 0) {
                $insert->execute(['user_id' => $userId, 'organization_id' => $organizationId]);
            }
        }
    }
}
