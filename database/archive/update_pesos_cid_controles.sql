-- OBSOLETO: ya incorporado en sql/schema.sql (pesos y CID reales de C1-C15).
-- Se conserva solo como referencia historica, NO importar.

-- ============================================================
-- Actualización de peso y relación CID por control (C1–C15)
-- según la justificación del documento de análisis (sección 4)
-- Ejecutar DESPUÉS de sql/schema.sql y migration_fase3_4.sql
-- ============================================================
USE consultora_iso27002;

UPDATE iso_controls SET weight = 1.50, confidentiality = 1, integrity = 1, availability = 1 WHERE code = 'C1';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 1, availability = 0 WHERE code = 'C2';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 1, availability = 0 WHERE code = 'C3';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 1, availability = 0 WHERE code = 'C4';
UPDATE iso_controls SET weight = 2.00, confidentiality = 1, integrity = 1, availability = 1 WHERE code = 'C5';
UPDATE iso_controls SET weight = 2.00, confidentiality = 1, integrity = 1, availability = 1 WHERE code = 'C6';
UPDATE iso_controls SET weight = 1.50, confidentiality = 1, integrity = 0, availability = 0 WHERE code = 'C7';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 1, availability = 0 WHERE code = 'C8';
UPDATE iso_controls SET weight = 1.50, confidentiality = 0, integrity = 1, availability = 1 WHERE code = 'C9';
UPDATE iso_controls SET weight = 1.50, confidentiality = 0, integrity = 1, availability = 1 WHERE code = 'C10';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 0, availability = 1 WHERE code = 'C11';
UPDATE iso_controls SET weight = 1.00, confidentiality = 0, integrity = 0, availability = 1 WHERE code = 'C12';
UPDATE iso_controls SET weight = 1.50, confidentiality = 1, integrity = 1, availability = 1 WHERE code = 'C13';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 0, availability = 0 WHERE code = 'C14';
UPDATE iso_controls SET weight = 1.00, confidentiality = 1, integrity = 1, availability = 1 WHERE code = 'C15';
