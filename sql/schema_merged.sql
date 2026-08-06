CREATE DATABASE IF NOT EXISTS consultora_iso27002
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE consultora_iso27002;

-- -----------------------------
-- Tablas base (completas)
-- -----------------------------

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'auditor', 'viewer') NOT NULL DEFAULT 'auditor',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE organizations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    address VARCHAR(255) NULL,
    email VARCHAR(160) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_organizations_name (name)
) ENGINE=InnoDB;

CREATE TABLE areas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id INT UNSIGNED NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_areas_organization_name (organization_id, name),
    CONSTRAINT fk_areas_organization FOREIGN KEY (organization_id) REFERENCES organizations(id)
) ENGINE=InnoDB;

CREATE TABLE iso_domains (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    description TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE iso_controls (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    domain_id INT UNSIGNED NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    title VARCHAR(220) NOT NULL,
    description TEXT NULL,
    objective TEXT NULL,
    confidentiality TINYINT UNSIGNED NOT NULL DEFAULT 1,
    integrity TINYINT UNSIGNED NOT NULL DEFAULT 1,
    availability TINYINT UNSIGNED NOT NULL DEFAULT 1,
    weight DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_iso_controls_domain FOREIGN KEY (domain_id) REFERENCES iso_domains(id)
) ENGINE=InnoDB;

CREATE TABLE questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    control_id INT UNSIGNED NOT NULL,
    question TEXT NOT NULL,
    weight DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_questions_control FOREIGN KEY (control_id) REFERENCES iso_controls(id)
) ENGINE=InnoDB;

CREATE TABLE audits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id INT UNSIGNED NOT NULL,
    area_id INT UNSIGNED NOT NULL,
    auditor_user_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    status ENUM('draft', 'in_progress', 'closed') NOT NULL DEFAULT 'draft',
    start_date DATE NOT NULL,
    end_date DATE NULL,
    maturity_score DECIMAL(5,2) NULL,
    risk_score DECIMAL(5,2) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_audits_organization FOREIGN KEY (organization_id) REFERENCES organizations(id),
    CONSTRAINT fk_audits_area FOREIGN KEY (area_id) REFERENCES areas(id),
    CONSTRAINT fk_audits_auditor_user FOREIGN KEY (auditor_user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE responses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    maturity_level TINYINT UNSIGNED NOT NULL DEFAULT 0,
    confidentiality TINYINT UNSIGNED NOT NULL DEFAULT 1,
    integrity TINYINT UNSIGNED NOT NULL DEFAULT 1,
    availability TINYINT UNSIGNED NOT NULL DEFAULT 1,
    answer TEXT NULL,
    recommendation TEXT NULL,
    answer_value ENUM('si','no','no_aplica') NULL,
    justification TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_responses_audit FOREIGN KEY (audit_id) REFERENCES audits(id),
    CONSTRAINT fk_responses_question FOREIGN KEY (question_id) REFERENCES questions(id),
    CONSTRAINT uq_responses_audit_question UNIQUE (audit_id, question_id)
) ENGINE=InnoDB;

CREATE TABLE evidences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    response_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    size_bytes INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_evidences_response FOREIGN KEY (response_id) REFERENCES responses(id)
) ENGINE=InnoDB;

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

-- Usuario administrador por defecto (contraseña placeholder)
INSERT INTO users (name, email, password, role, status)
VALUES (
    'Administrador',
    'admin@datasolutionscr.net',
    'BOOTSTRAP_ADMIN_PASSWORD',
    'admin',
    'active'
);

-- --------------------------------------------------
-- Datos: dominios (7), 15 controles y 75 preguntas
-- --------------------------------------------------

INSERT INTO iso_domains (code, name, description, status) VALUES
('D1', 'Gobierno y políticas', 'Políticas y gobierno de seguridad de la información', 'active'),
('D2', 'Gestión de activos', 'Inventario y clasificación de activos', 'active'),
('D3', 'Control de accesos', 'Control y gestión de accesos y privilegios', 'active'),
('D4', 'Criptografía y comunicaciones', 'Protección de la información en tránsito y en reposo', 'active'),
('D5', 'Operaciones y continuidad', 'Gestión de operaciones, continuidad y copia de seguridad', 'active'),
('D6', 'Seguridad física y ambiental', 'Protección física de las instalaciones y equipos', 'active'),
('D7', 'Cumplimiento y gestión de riesgos', 'Auditoría, cumplimiento normativo y gestión de riesgos', 'active');

INSERT INTO iso_controls (domain_id, code, title, description, objective, weight, confidentiality, integrity, availability, status) VALUES
((SELECT id FROM iso_domains WHERE code='D1'), 'C1', 'Política de seguridad', 'Existencia y comunicación de la política de seguridad', 'Establecer las directrices de seguridad aplicables a la base de datos.', 1.00, 1, 1, 1, 'active'),
((SELECT id FROM iso_domains WHERE code='D1'), 'C2', 'Organización de la seguridad', 'Roles y responsabilidades de seguridad', 'Definir roles y responsabilidades en seguridad.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D2'), 'C3', 'Inventario de activos', 'Identificación y clasificación de activos', 'Mantener inventario y clasificación de activos.', 1.00, 1, 0, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D2'), 'C4', 'Propiedad de activos', 'Responsables y propietarios de activos', 'Asignar propietarios y custodios.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D3'), 'C5', 'Control de accesos lógicos', 'Gestión de cuentas y accesos', 'Aplicar controles de control de acceso.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D3'), 'C6', 'Gestión de privilegios', 'Asignación y revisión de privilegios', 'Revisar y limitar privilegios.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D4'), 'C7', 'Criptografía', 'Uso adecuado de controles criptográficos', 'Proteger datos en reposo y en tránsito.', 1.00, 1, 0, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D4'), 'C8', 'Seguridad en comunicaciones', 'Protección de información en tránsito', 'Asegurar canales y transferencias.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D5'), 'C9', 'Seguridad en operaciones', 'Procedimientos operativos y seguridad', 'Operar de forma segura y controlada.', 1.00, 0, 1, 1, 'active'),
((SELECT id FROM iso_domains WHERE code='D5'), 'C10', 'Gestión de incidentes', 'Detección, respuesta y registro de incidentes', 'Detectar y responder incidentes.', 1.00, 0, 1, 1, 'active'),
((SELECT id FROM iso_domains WHERE code='D6'), 'C11', 'Control físico', 'Protección de accesos físicos y equipos', 'Proteger instalaciones y equipos.', 1.00, 0, 0, 1, 'active'),
((SELECT id FROM iso_domains WHERE code='D6'), 'C12', 'Protección ambiental', 'Medidas ambientales y de infraestructura', 'Mitigar riesgos ambientales.', 1.00, 0, 0, 1, 'active'),
((SELECT id FROM iso_domains WHERE code='D7'), 'C13', 'Evaluación de riesgos', 'Procesos de identificación y tratamiento de riesgos', 'Identificar y tratar riesgos.', 1.00, 1, 1, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D7'), 'C14', 'Cumplimiento legal', 'Cumplimiento de requisitos legales y contractuales', 'Asegurar cumplimiento legal.', 1.00, 0, 0, 0, 'active'),
((SELECT id FROM iso_domains WHERE code='D7'), 'C15', 'Auditoría y revisión', 'Revisión periódica y auditorías internas', 'Revisar y mejorar controles.', 1.00, 1, 1, 0, 'active');

-- Preguntas: 5 por cada control => 75 preguntas
INSERT INTO questions (control_id, question, weight, status) VALUES
((SELECT id FROM iso_controls WHERE code='C1'), '¿Existe una política de seguridad documentada y aprobada por la dirección?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C1'), '¿La política de seguridad se comunica a todos los empleados y terceros relevantes?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C1'), '¿La política se revisa periódicamente y se actualiza según cambios organizacionales?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C1'), '¿Se dispone de indicadores o métricas para verificar el cumplimiento de la política?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C1'), '¿La política define el alcance y los objetivos de seguridad de la información?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C2'), '¿Están definidas las responsabilidades y roles de seguridad en la organización?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C2'), '¿Existe un comité o responsable asignado para gobernanza de seguridad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C2'), '¿Se realizan actividades de concienciación y formación en seguridad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C2'), '¿Se gestionan acuerdos y responsabilidades con terceros?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C2'), '¿Se establecen criterios para aceptación y gestión de riesgos?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C3'), '¿Existe un inventario actualizado de activos de información?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C3'), '¿Los activos están clasificados según su criticidad y sensibilidad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C3'), '¿Se registran los propietarios y custodios de cada activo?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C3'), '¿Se realizan revisiones periódicas del inventario de activos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C3'), '¿Hay controles para el manejo y etiquetado de soportes y activos?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C4'), '¿Están asignados claramente los propietarios de activos y sus responsabilidades?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C4'), '¿Se definen procesos para la adquisición y baja de activos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C4'), '¿Se controla el acceso físico y lógico a los activos críticos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C4'), '¿Existe un registro de ubicación para activos móviles y portátiles?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C4'), '¿Se realizan copias de respaldo de activos críticos según política?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C5'), '¿Se aplican controles de acceso basados en el principio de menor privilegio?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C5'), '¿Existen procedimientos para la gestión del ciclo de vida de cuentas de usuario?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C5'), '¿Se auditan y revisan los accesos periódicamente?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C5'), '¿Se utilizan mecanismos de autenticación adecuados (2FA, contraseñas fuertes)?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C5'), '¿Se gestionan cuentas de servicio y accesos compartidos de forma segura?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C6'), '¿Se revisan y actualizan privilegios cuando cambian roles o personas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C6'), '¿Hay procedimientos para la aprobación y registro de privilegios elevados?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C6'), '¿Se monitorizan actividades de cuentas con privilegios altos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C6'), '¿Se aplican controles para prevenir el abuso de privilegios?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C6'), '¿Se separan funciones críticas para evitar conflictos de interés?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C7'), '¿Se utilizan algoritmos y parámetros criptográficos aprobados por la organización?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C7'), '¿Se gestionan y protegen adecuadamente las claves criptográficas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C7'), '¿Se aplican criptografía para proteger datos sensibles en reposo cuando es necesario?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C7'), '¿Se protege la información confidencial en backups mediante criptografía cuando corresponde?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C7'), '¿Se revisan periodicamente las políticas de uso de criptografía?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C8'), '¿Se cifran o aseguran canales de comunicación que transportan información sensible?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C8'), '¿Se usan VPNs o canales seguros para accesos remotos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C8'), '¿Se controlan y registran las conexiones entrantes y salientes críticas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C8'), '¿Se aplican políticas para uso seguro de correo y transferencias de archivos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C8'), '¿Se gestionan acuerdos con proveedores de servicios de comunicaciones?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C9'), '¿Existen procedimientos operativos documentados y conocidos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C9'), '¿Se aplican controles para cambios en producción (change management)?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C9'), '¿Se realiza monitoreo y registro de eventos operativos relevantes?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C9'), '¿Se gestionan parches y vulnerabilidades en sistemas críticos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C9'), '¿Se realizan revisiones de configuración y seguridad periódicas?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C10'), '¿Existe un proceso formal para la gestión de incidentes de seguridad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C10'), '¿Se registran y clasifican los incidentes y se da seguimiento?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C10'), '¿Se realizan pruebas y ejercicios de respuesta a incidentes?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C10'), '¿Se dispone de canales definidos para reportar incidentes?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C10'), '¿Se retroalimenta la organización con lecciones aprendidas tras incidentes?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C11'), '¿Se controlan accesos físicos a instalaciones críticas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C11'), '¿Existen medidas de protección para equipos y servidores?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C11'), '¿Se gestionan visitantes y accesos temporales de forma segura?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C11'), '¿Se realizan inspecciones y mantenimiento de controles físicos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C11'), '¿Hay controles de redundancia y recuperación física para equipos críticos?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C12'), '¿Se adoptan medidas ambientales para proteger activos (temperatura, humedad)?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C12'), '¿Se dispone de sistemas contra incendios y detección adecuada?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C12'), '¿Se protege la infraestructura eléctrica y suministros críticos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C12'), '¿Se realizan evaluaciones de riesgo físico y ambiental periódicas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C12'), '¿Hay planes de contingencia ante fallas ambientales?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C13'), '¿Se realiza identificación y evaluación periódica de riesgos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C13'), '¿Se documentan los tratamientos y planes de mitigación de riesgos?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C13'), '¿Se priorizan riesgos según impacto y probabilidad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C13'), '¿Se revisan los riesgos tras cambios significativos en la organización?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C13'), '¿Se comunican los riesgos a la alta dirección y partes interesadas?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C14'), '¿Se identifican requisitos legales y regulatorios aplicables?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C14'), '¿Se gestionan los contratos y obligaciones con proveedores en materia de seguridad?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C14'), '¿Se mantiene evidencia de cumplimiento y registros necesarios?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C14'), '¿Se realizan auditorías para verificar cumplimiento legal?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C14'), '¿Se evalúa impacto de cambios regulatorios en la organización?', 1.00, 'active'),

((SELECT id FROM iso_controls WHERE code='C15'), '¿Se realizan auditorías internas periódicas de seguridad de la información?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C15'), '¿Se documentan hallazgos y se hace seguimiento a las acciones correctivas?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C15'), '¿Se revisan los controles y resultados de auditoría con la dirección?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C15'), '¿Se mantienen registros de auditoría y evidencias suficientes?', 1.00, 'active'),
((SELECT id FROM iso_controls WHERE code='C15'), '¿Se evalúa la eficacia de las acciones tomadas tras auditorías?', 1.00, 'active');
