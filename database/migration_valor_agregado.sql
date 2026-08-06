-- ============================================================
-- Valor Agregado – Seguimiento de Recomendaciones
-- ============================================================
USE consultora_iso27002;

CREATE TABLE IF NOT EXISTS recommendations (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id     INT UNSIGNED NOT NULL,
    control_id   INT UNSIGNED NOT NULL,
    description  TEXT NOT NULL,
    responsible  VARCHAR(160) NULL,
    due_date     DATE NULL,
    status       ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending',
    notes        TEXT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rec_audit   FOREIGN KEY (audit_id)   REFERENCES audits(id),
    CONSTRAINT fk_rec_control FOREIGN KEY (control_id) REFERENCES iso_controls(id)
) ENGINE=InnoDB;
