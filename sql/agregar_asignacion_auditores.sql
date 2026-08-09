-- ============================================================
--  Agrega la tabla auditor_organizations (que empresas puede
--  auditar cada auditor de la consultora) a una base de datos
--  "consultora_iso27002" ya existente, sin tocar el resto del
--  esquema ni los datos ya cargados.
--
--  Si vas a crear la base de datos desde cero, no hace falta este
--  script: sql/schema.sql ya incluye esta tabla.
--
--  Despues de correr esto, cualquier auditor global (rol "auditor"
--  sin organizacion propia) que ya existiera queda SIN ninguna
--  organizacion asignada hasta que un admin se la asigne desde
--  Usuarios > Editar. Mientras tanto no vera ni podra tocar
--  ninguna empresa (antes tenia acceso a todas).
-- ============================================================

USE consultora_iso27002;

CREATE TABLE IF NOT EXISTS auditor_organizations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    organization_id INT UNSIGNED NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_auditor_organization (user_id, organization_id),
    CONSTRAINT fk_ao_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_ao_organization FOREIGN KEY (organization_id) REFERENCES organizations(id)
) ENGINE=InnoDB;
