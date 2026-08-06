-- ============================================================
-- Migración Fase 5/6 – Corrección de brechas
-- ============================================================
USE consultora_iso27002;

-- 1. Agregar campo DBA a audits
ALTER TABLE audits
    ADD COLUMN dba_name VARCHAR(160) NULL AFTER auditor_user_id;

-- 2. Cambiar columna answer en responses a ENUM Sí/No/No aplica
--    y agregar maturity_level a nivel de CONTROL (tabla audit_control_maturity)
ALTER TABLE responses
    MODIFY COLUMN answer ENUM('yes','no','na') NULL;

-- 3. Agregar scores de riesgo separados por C, I, D en audits
ALTER TABLE audits
    ADD COLUMN risk_c  DECIMAL(5,2) NULL AFTER risk_score,
    ADD COLUMN risk_i  DECIMAL(5,2) NULL AFTER risk_c,
    ADD COLUMN risk_d  DECIMAL(5,2) NULL AFTER risk_i;

-- 4. Tabla de madurez por control (el nivel se asigna al control, no a cada pregunta)
CREATE TABLE IF NOT EXISTS audit_control_maturity (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id     INT UNSIGNED NOT NULL,
    control_id   INT UNSIGNED NOT NULL,
    maturity_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_acm (audit_id, control_id),
    CONSTRAINT fk_acm_audit   FOREIGN KEY (audit_id)   REFERENCES audits(id),
    CONSTRAINT fk_acm_control FOREIGN KEY (control_id) REFERENCES iso_controls(id)
) ENGINE=InnoDB;
