-- ============================================================
--  Agrega la tabla activity_log (bitacora de cambios) a una base
--  de datos "consultora_iso27002" ya existente, sin tocar el resto
--  del esquema ni los datos ya cargados.
--
--  Si vas a crear la base de datos desde cero, no hace falta este
--  script: sql/schema.sql ya incluye esta tabla.
-- ============================================================

USE consultora_iso27002;

CREATE TABLE IF NOT EXISTS activity_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id    INT UNSIGNED NULL,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(60) NOT NULL,
    description TEXT NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_log_audit FOREIGN KEY (audit_id) REFERENCES audits(id),
    CONSTRAINT fk_activity_log_user  FOREIGN KEY (user_id)  REFERENCES users(id)
) ENGINE=InnoDB;
