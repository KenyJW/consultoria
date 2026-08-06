-- ============================================================
-- Migracion Fase 3 y 4
-- El esquema base ya define iso_domains, iso_controls, questions,
-- audits, responses y evidences. Esta migracion agrega a
-- iso_controls las columnas que exige el enunciado del proyecto:
-- objetivo del control, y la relacion con la triada CID.
-- (El campo weight ya existe en iso_controls y questions.)
-- ============================================================

USE consultora_iso27002;

ALTER TABLE iso_controls
    ADD COLUMN objective       TEXT NULL            AFTER description,
    ADD COLUMN confidentiality TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER objective,
    ADD COLUMN integrity       TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER confidentiality,
    ADD COLUMN availability    TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER integrity;

-- Nota: iso_controls.weight ya debe existir. Si en tu esquema no estuviera,
-- descomenta la siguiente linea:
-- ALTER TABLE iso_controls ADD COLUMN weight DECIMAL(5,2) NOT NULL DEFAULT 1.00 AFTER objective;

-- ============================================================
-- Datos de ejemplo (opcional) para probar Fase 3 y 4
-- ============================================================

INSERT INTO iso_domains (code, name, description, status) VALUES
    ('A.5', 'Politicas de seguridad de la informacion', 'Directrices y politicas aprobadas por la direccion.', 'active'),
    ('A.8', 'Control de acceso', 'Gestion de accesos logicos a las bases de datos.', 'active'),
    ('A.12', 'Seguridad de las operaciones', 'Respaldos, registros y monitoreo operativo.', 'active');

INSERT INTO iso_controls (domain_id, code, title, description, objective, weight, confidentiality, integrity, availability, status) VALUES
    ((SELECT id FROM iso_domains WHERE code = 'A.8'), 'A.8.1', 'Politica de control de acceso', 'Existencia y aplicacion de una politica de acceso a la BD.', 'Restringir el acceso a la informacion.', 2.00, 1, 1, 0, 'active'),
    ((SELECT id FROM iso_domains WHERE code = 'A.8'), 'A.8.2', 'Gestion de privilegios', 'Asignacion y revision periodica de privilegios.', 'Evitar accesos indebidos.', 2.00, 1, 1, 1, 'active'),
    ((SELECT id FROM iso_domains WHERE code = 'A.12'), 'A.12.1', 'Copias de respaldo', 'Respaldos automaticos y pruebas de restauracion.', 'Garantizar disponibilidad e integridad.', 3.00, 0, 1, 1, 'active');

INSERT INTO questions (control_id, question, weight, status) VALUES
    ((SELECT id FROM iso_controls WHERE code = 'A.8.1'), 'Existe una politica formal de contrasenas para las cuentas de base de datos?', 1.00, 'active'),
    ((SELECT id FROM iso_controls WHERE code = 'A.8.2'), 'Se revisan periodicamente los privilegios de los usuarios de la BD?', 1.00, 'active'),
    ((SELECT id FROM iso_controls WHERE code = 'A.12.1'), 'Existen respaldos automaticos de la base de datos?', 1.00, 'active'),
    ((SELECT id FROM iso_controls WHERE code = 'A.12.1'), 'Se realizan pruebas de restauracion de los respaldos?', 1.00, 'active');
