-- ============================================================
--  Actualizacion de catalogo ISO a controles/preguntas
--  especificos de administracion de bases de datos
--
--  Uso: SOLO si ya tenias la base de datos "consultora_iso27002"
--  creada con una version anterior de sql/schema.sql (la de
--  controles genericos de seguridad organizacional) y quieres
--  conservar las organizaciones/auditorias/usuarios que ya
--  registraste, en vez de reimportar todo desde cero.
--
--  Este script actualiza EN SITIO los mismos codigos (D1-D7,
--  C1-C15) y las mismas 75 preguntas (mismos id 1-75, en el
--  mismo orden de insercion que el schema.sql original), por lo
--  que no rompe las llaves foraneas de responses/audit_control_
--  maturity/recommendations que ya existan. Las respuestas viejas
--  seguiran asociadas a la pregunta correcta por posicion, pero su
--  contenido (Si/No/madurez) fue capturado contra el enunciado
--  ANTERIOR, asi que cualquier auditoria de prueba que ya hayas
--  cerrado con el catalogo viejo conviene reabrirla y revisarla
--  con las preguntas nuevas antes de usarla como evidencia real.
--
--  Si prefieres partir de cero, en vez de este script simplemente
--  reimporta sql/schema.sql sobre una base de datos vacia.
-- ============================================================

USE consultora_iso27002;

-- ------------------------------------------------------------
-- 1) Dominios (por codigo)
-- ------------------------------------------------------------
UPDATE iso_domains SET name = 'Gobierno y políticas de la base de datos', description = 'Políticas, roles y responsabilidades para la administración segura de bases de datos' WHERE code = 'D1';
UPDATE iso_domains SET name = 'Gestión de activos de datos', description = 'Inventario, clasificación, propiedad y ciclo de vida de las bases de datos y su información' WHERE code = 'D2';
UPDATE iso_domains SET name = 'Control de accesos a la base de datos', description = 'Gestión de cuentas, privilegios y monitoreo de la actividad sobre las bases de datos' WHERE code = 'D3';
UPDATE iso_domains SET name = 'Criptografía y protección de datos', description = 'Cifrado de datos en reposo y en tránsito, y enmascaramiento de información sensible' WHERE code = 'D4';
UPDATE iso_domains SET name = 'Operaciones, respaldo y continuidad de la base de datos', description = 'Respaldos, gestión de parches y gestión de cambios sobre las bases de datos' WHERE code = 'D5';
UPDATE iso_domains SET name = 'Seguridad física y de infraestructura del servidor de base de datos', description = 'Protección física y ambiental de los servidores que alojan las bases de datos' WHERE code = 'D6';
UPDATE iso_domains SET name = 'Cumplimiento, auditoría y gestión de riesgos de la base de datos', description = 'Evaluación de riesgos, cumplimiento normativo y auditoría interna de la base de datos' WHERE code = 'D7';

-- ------------------------------------------------------------
-- 2) Controles (por codigo) — incluye el objetivo, antes vacio
-- ------------------------------------------------------------
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D1'), title='Política de seguridad de la base de datos', description='Existencia, aprobación y comunicación de una política de seguridad específica para la administración de bases de datos', objective='Garantizar que la organización cuente con lineamientos formales y vigentes que guíen la administración segura de sus bases de datos, alineados con la norma ISO/IEC 27002.', weight=1.00, confidentiality=1, integrity=1, availability=1, iso_reference='5.1' WHERE code='C1';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D1'), title='Roles, responsabilidades y segregación de funciones del DBA', description='Definición de roles, responsabilidades y segregación de funciones del administrador de base de datos (DBA)', objective='Asegurar que las funciones de administración de bases de datos estén claramente asignadas y separadas de las de desarrollo/aplicación, para reducir el riesgo de errores o abuso de privilegios.', weight=1.50, confidentiality=1, integrity=1, availability=0, iso_reference='5.2 / 5.3' WHERE code='C2';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D2'), title='Inventario y clasificación de bases de datos y datos sensibles', description='Identificación, inventario y clasificación de las bases de datos y de la información sensible que contienen', objective='Conocer con exactitud qué bases de datos existen, dónde están y qué nivel de sensibilidad tiene la información que almacenan, como base para priorizar su protección.', weight=1.00, confidentiality=1, integrity=1, availability=0, iso_reference='5.9 / 5.12' WHERE code='C3';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D2'), title='Propiedad, retención y eliminación segura de datos', description='Asignación de propietarios de datos y gestión del ciclo de vida (retención y eliminación) de la información almacenada', objective='Asegurar que cada base de datos tenga un responsable definido y que los datos se conserven o eliminen conforme a criterios de negocio y requisitos legales, evitando exposición innecesaria.', weight=1.00, confidentiality=1, integrity=0, availability=0, iso_reference='5.10 / 8.10' WHERE code='C4';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D3'), title='Gestión de cuentas y autenticación de usuarios de la base de datos', description='Ciclo de vida de cuentas de usuario de la base de datos y mecanismos de autenticación utilizados', objective='Garantizar que solo cuentas autorizadas e identificables puedan autenticarse contra la base de datos, y que las cuentas por defecto o inactivas no representen una puerta de entrada no controlada.', weight=1.50, confidentiality=1, integrity=1, availability=0, iso_reference='5.16 / 5.17 / 8.5' WHERE code='C5';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D3'), title='Gestión de privilegios y mínimo privilegio', description='Asignación, revisión y revocación de privilegios sobre la base de datos según el principio de mínimo privilegio', objective='Reducir el riesgo de accesos indebidos o excesivos asegurando que cada usuario y aplicación tenga únicamente los privilegios estrictamente necesarios para su función.', weight=2.00, confidentiality=1, integrity=1, availability=0, iso_reference='8.2 / 8.3' WHERE code='C6';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D3'), title='Monitoreo y auditoría de actividad privilegiada en la base de datos', description='Registro, protección y revisión de los registros (logs) de actividad de cuentas privilegiadas sobre la base de datos', objective='Detectar oportunamente actividad no autorizada o anómala sobre la base de datos y garantizar que los registros de auditoría no puedan ser alterados por quienes son objeto de la auditoría.', weight=1.50, confidentiality=1, integrity=1, availability=0, iso_reference='8.15 / 8.16' WHERE code='C7';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D4'), title='Cifrado de datos en reposo', description='Uso de cifrado y gestión de llaves criptográficas para proteger los datos almacenados en la base de datos', objective='Asegurar que la información sensible almacenada permanezca ilegible ante un acceso no autorizado al motor de base de datos o a sus archivos físicos.', weight=1.50, confidentiality=1, integrity=0, availability=0, iso_reference='8.24' WHERE code='C8';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D4'), title='Cifrado en tránsito y enmascaramiento de datos', description='Protección de las conexiones hacia la base de datos y enmascaramiento de datos sensibles en ambientes no productivos', objective='Evitar la exposición de información sensible mientras viaja por la red o cuando se replica en ambientes de desarrollo y pruebas.', weight=1.00, confidentiality=1, integrity=0, availability=0, iso_reference='8.24 / 8.20 / 8.11' WHERE code='C9';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D5'), title='Gestión de respaldos y recuperación', description='Política, ejecución y prueba de respaldos (backups) de las bases de datos críticas', objective='Garantizar que la organización pueda recuperar la información de sus bases de datos ante pérdida, corrupción o desastre, dentro de tiempos de recuperación aceptables.', weight=2.00, confidentiality=0, integrity=1, availability=1, iso_reference='8.13' WHERE code='C10';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D5'), title='Gestión de parches y vulnerabilidades del motor de base de datos', description='Identificación, prueba y aplicación de parches de seguridad y gestión de vulnerabilidades del motor de base de datos', objective='Reducir la ventana de exposición ante vulnerabilidades conocidas del motor de base de datos, manteniendo versiones soportadas y actualizadas.', weight=1.50, confidentiality=1, integrity=1, availability=1, iso_reference='8.8 / 8.19' WHERE code='C11';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D5'), title='Gestión de cambios en esquema y configuración', description='Control de cambios sobre la estructura, configuración y parámetros de las bases de datos en producción', objective='Evitar que cambios no controlados en el esquema o la configuración de la base de datos generen errores, inconsistencias o interrupciones del servicio.', weight=1.00, confidentiality=0, integrity=1, availability=1, iso_reference='8.32' WHERE code='C12';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D6'), title='Seguridad física del centro de datos y servidores de base de datos', description='Control de acceso físico a los servidores y áreas donde residen las bases de datos', objective='Impedir el acceso físico no autorizado al hardware donde se ejecutan las bases de datos, que podría derivar en robo, manipulación o daño directo a la información.', weight=1.00, confidentiality=1, integrity=0, availability=1, iso_reference='7.1 / 7.2' WHERE code='C13';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D6'), title='Protección ambiental e infraestructura de soporte', description='Condiciones ambientales y de infraestructura de soporte (energía, climatización, incendio) para los servidores de base de datos', objective='Asegurar la disponibilidad continua de los servidores de base de datos ante fallas ambientales o de infraestructura eléctrica.', weight=0.75, confidentiality=0, integrity=0, availability=1, iso_reference='7.5 / 7.8' WHERE code='C14';
UPDATE iso_controls SET domain_id=(SELECT id FROM iso_domains WHERE code='D7'), title='Gestión de riesgos, cumplimiento normativo y auditoría interna de la base de datos', description='Evaluación de riesgos, cumplimiento legal aplicable y auditorías internas sobre los controles de seguridad de la base de datos', objective='Asegurar que la organización identifique y trate proactivamente los riesgos sobre sus bases de datos, cumpla la normativa de protección de datos aplicable, y verifique periódicamente la efectividad de sus controles mediante auditoría interna.', weight=1.50, confidentiality=1, integrity=1, availability=1, iso_reference='5.31 / 5.35 / 6.1' WHERE code='C15';

-- ------------------------------------------------------------
-- 3) Preguntas (por id, mismo orden de insercion del schema.sql
--    original: 5 preguntas por control, C1..C15 -> id 1..75)
-- ------------------------------------------------------------
UPDATE questions SET question='¿Existe una política de seguridad específica para la administración de bases de datos, documentada y aprobada por la dirección?' WHERE id=1;
UPDATE questions SET question='¿La política cubre los requisitos mínimos de configuración segura del motor de base de datos (hardening)?' WHERE id=2;
UPDATE questions SET question='¿La política se comunica y es de conocimiento de todo el personal que administra o accede a las bases de datos?' WHERE id=3;
UPDATE questions SET question='¿La política se revisa y actualiza periódicamente conforme cambian las versiones del motor de base de datos o la normativa aplicable?' WHERE id=4;
UPDATE questions SET question='¿Existen indicadores para verificar el cumplimiento de la política de seguridad de bases de datos?' WHERE id=5;
UPDATE questions SET question='¿Están formalmente definidas las funciones y responsabilidades del/los administrador(es) de base de datos (DBA)?' WHERE id=6;
UPDATE questions SET question='¿Existe segregación de funciones entre el rol de DBA, el de desarrollo y el de administración de aplicaciones?' WHERE id=7;
UPDATE questions SET question='¿Se documenta y aprueba formalmente quién puede actuar como DBA en cada base de datos?' WHERE id=8;
UPDATE questions SET question='¿Se capacita periódicamente al personal DBA en buenas prácticas de seguridad del motor de base de datos utilizado?' WHERE id=9;
UPDATE questions SET question='¿Existen procedimientos de respaldo de personal para que la administración de la base de datos no dependa de una sola persona?' WHERE id=10;
UPDATE questions SET question='¿Existe un inventario actualizado de todas las bases de datos, instancias y motores utilizados por la organización?' WHERE id=11;
UPDATE questions SET question='¿Se ha identificado y clasificado la información sensible almacenada en las bases de datos (datos personales, financieros, de salud, etc.)?' WHERE id=12;
UPDATE questions SET question='¿Se documentan los esquemas, tablas o columnas que contienen datos clasificados como críticos o sensibles?' WHERE id=13;
UPDATE questions SET question='¿Se revisa periódicamente el inventario de bases de datos para detectar instancias no autorizadas o "shadow IT"?' WHERE id=14;
UPDATE questions SET question='¿El inventario incluye la versión del motor, ubicación (servidor/nube) y responsable de cada base de datos?' WHERE id=15;
UPDATE questions SET question='¿Están asignados formalmente los propietarios (data owners) responsables de cada base de datos?' WHERE id=16;
UPDATE questions SET question='¿Existen políticas de retención de datos que definan por cuánto tiempo se conserva la información en cada base de datos?' WHERE id=17;
UPDATE questions SET question='¿Se aplican procedimientos de eliminación segura (borrado definitivo) de datos y bases de datos que ya no se requieren?' WHERE id=18;
UPDATE questions SET question='¿Se depuran o enmascaran las copias de datos de prueba/desarrollo que contienen información sensible real?' WHERE id=19;
UPDATE questions SET question='¿Se documentan las excepciones cuando datos que deberían eliminarse se conservan por requerimiento legal?' WHERE id=20;
UPDATE questions SET question='¿Existe un procedimiento formal para la creación, modificación y baja de cuentas de usuario en la base de datos?' WHERE id=21;
UPDATE questions SET question='¿Se eliminan o deshabilitan las cuentas por defecto del motor de base de datos (usuarios predeterminados, ejemplos)?' WHERE id=22;
UPDATE questions SET question='¿Se exige el uso de contraseñas robustas y/o autenticación multifactor para el acceso administrativo a la base de datos?' WHERE id=23;
UPDATE questions SET question='¿Las cuentas de servicio/aplicación que se conectan a la base de datos están identificadas y no se comparten entre sistemas?' WHERE id=24;
UPDATE questions SET question='¿Se realiza una revisión periódica de cuentas activas para detectar cuentas huérfanas o inactivas?' WHERE id=25;
UPDATE questions SET question='¿Los privilegios sobre la base de datos se otorgan siguiendo el principio de mínimo privilegio?' WHERE id=26;
UPDATE questions SET question='¿Existen roles definidos (lectura, escritura, administración) en lugar de otorgar privilegios individuales caso por caso?' WHERE id=27;
UPDATE questions SET question='¿Se revisan y aprueban formalmente las solicitudes de privilegios elevados (DBA, sysadmin) antes de otorgarlos?' WHERE id=28;
UPDATE questions SET question='¿Se realiza una recertificación periódica de los privilegios otorgados a usuarios y aplicaciones?' WHERE id=29;
UPDATE questions SET question='¿Se revocan oportunamente los privilegios cuando un usuario cambia de función o deja de trabajar en la organización?' WHERE id=30;
UPDATE questions SET question='¿Se registran (logging) las acciones realizadas por cuentas con privilegios administrativos sobre la base de datos?' WHERE id=31;
UPDATE questions SET question='¿Existen alertas automáticas ante actividades inusuales o no autorizadas sobre la base de datos (consultas masivas, cambios de esquema, exportaciones)?' WHERE id=32;
UPDATE questions SET question='¿Los registros de auditoría de la base de datos se protegen contra alteración o eliminación por parte de los propios DBA?' WHERE id=33;
UPDATE questions SET question='¿Se revisan periódicamente los registros de actividad de la base de datos por personal distinto al DBA auditado?' WHERE id=34;
UPDATE questions SET question='¿Se conservan los registros de auditoría de la base de datos durante un período definido acorde a la normativa aplicable?' WHERE id=35;
UPDATE questions SET question='¿Se utiliza cifrado de datos en reposo (TDE u otro mecanismo) en las bases de datos que almacenan información sensible?' WHERE id=36;
UPDATE questions SET question='¿Las llaves de cifrado se gestionan y protegen mediante un mecanismo independiente del propio motor de base de datos (KMS, HSM)?' WHERE id=37;
UPDATE questions SET question='¿Existen procedimientos definidos para la rotación periódica de llaves criptográficas?' WHERE id=38;
UPDATE questions SET question='¿Los respaldos (backups) de la base de datos se almacenan también cifrados?' WHERE id=39;
UPDATE questions SET question='¿Se revisa periódicamente que los algoritmos y parámetros criptográficos utilizados sigan siendo adecuados (no obsoletos)?' WHERE id=40;
UPDATE questions SET question='¿Las conexiones hacia la base de datos utilizan cifrado en tránsito (TLS/SSL) de forma obligatoria?' WHERE id=41;
UPDATE questions SET question='¿Se restringe o cifra el acceso remoto administrativo a la base de datos (VPN, túneles seguros)?' WHERE id=42;
UPDATE questions SET question='¿Se enmascaran o anonimizan los datos sensibles cuando se copian a ambientes de desarrollo, pruebas o capacitación?' WHERE id=43;
UPDATE questions SET question='¿Las cadenas de conexión y credenciales de acceso a la base de datos se almacenan de forma segura (no en texto plano en el código)?' WHERE id=44;
UPDATE questions SET question='¿Se valida que las herramientas de administración remota de la base de datos no transmitan credenciales sin cifrar?' WHERE id=45;
UPDATE questions SET question='¿Existe una política de respaldo (backup) de bases de datos que defina frecuencia, tipo y retención?' WHERE id=46;
UPDATE questions SET question='¿Los respaldos cubren todas las bases de datos críticas identificadas en el inventario?' WHERE id=47;
UPDATE questions SET question='¿Se realizan pruebas periódicas de restauración para verificar que los respaldos son utilizables?' WHERE id=48;
UPDATE questions SET question='¿Se documentan los objetivos de tiempo de recuperación (RTO) y punto de recuperación (RPO) para las bases de datos críticas?' WHERE id=49;
UPDATE questions SET question='¿Los respaldos se almacenan en una ubicación distinta (física o lógicamente) al servidor de producción?' WHERE id=50;
UPDATE questions SET question='¿Existe un procedimiento formal para identificar y aplicar parches de seguridad al motor de base de datos?' WHERE id=51;
UPDATE questions SET question='¿Se realizan escaneos o evaluaciones periódicas de vulnerabilidades sobre los servidores de base de datos?' WHERE id=52;
UPDATE questions SET question='¿Los parches críticos se aplican dentro de un plazo definido después de su publicación?' WHERE id=53;
UPDATE questions SET question='¿Se prueban los parches en un ambiente no productivo antes de aplicarlos en producción?' WHERE id=54;
UPDATE questions SET question='¿Se mantiene un registro de las versiones del motor de base de datos y su estado de soporte (fin de vida/EOL)?' WHERE id=55;
UPDATE questions SET question='¿Existe un procedimiento formal de gestión de cambios para modificaciones de esquema, configuración o parámetros de la base de datos?' WHERE id=56;
UPDATE questions SET question='¿Los cambios en producción requieren aprobación previa de una persona distinta a quien los ejecuta?' WHERE id=57;
UPDATE questions SET question='¿Se prueban los cambios de esquema o configuración en un ambiente de pruebas antes de aplicarlos en producción?' WHERE id=58;
UPDATE questions SET question='¿Existe un procedimiento de reversión (rollback) documentado ante cambios fallidos?' WHERE id=59;
UPDATE questions SET question='¿Se mantiene un historial de los cambios realizados sobre la estructura de las bases de datos?' WHERE id=60;
UPDATE questions SET question='¿El acceso físico a los servidores donde residen las bases de datos está restringido y controlado?' WHERE id=61;
UPDATE questions SET question='¿Se registra y audita el ingreso de personas al centro de datos o cuarto de servidores?' WHERE id=62;
UPDATE questions SET question='¿Existen controles adicionales (biométricos, tarjetas, doble factor) para el acceso a zonas donde están los servidores de base de datos?' WHERE id=63;
UPDATE questions SET question='¿Se gestionan de forma segura los accesos temporales de proveedores o personal externo a la infraestructura de base de datos?' WHERE id=64;
UPDATE questions SET question='¿Se realizan inspecciones periódicas de los controles físicos de seguridad del centro de datos?' WHERE id=65;
UPDATE questions SET question='¿Los servidores de base de datos cuentan con sistemas de respaldo eléctrico (UPS/planta) ante cortes de energía?' WHERE id=66;
UPDATE questions SET question='¿Existen sistemas de control de temperatura y humedad adecuados para el hardware que aloja las bases de datos?' WHERE id=67;
UPDATE questions SET question='¿Se dispone de sistemas de detección y supresión de incendios en el área donde operan los servidores de base de datos?' WHERE id=68;
UPDATE questions SET question='¿Existen planes de contingencia documentados ante fallas ambientales (energía, climatización, incendio)?' WHERE id=69;
UPDATE questions SET question='¿Se realizan pruebas periódicas de los sistemas de respaldo eléctrico y ambiental?' WHERE id=70;
UPDATE questions SET question='¿Se realiza una evaluación periódica de riesgos específica para la administración de las bases de datos?' WHERE id=71;
UPDATE questions SET question='¿Se identifican los requisitos legales y regulatorios aplicables al tratamiento de datos almacenados (ej. protección de datos personales)?' WHERE id=72;
UPDATE questions SET question='¿Se realizan auditorías internas periódicas sobre los controles de seguridad de las bases de datos?' WHERE id=73;
UPDATE questions SET question='¿Existen procedimientos definidos para responder ante incidentes de seguridad que involucren bases de datos (fuga, corrupción, acceso no autorizado)?' WHERE id=74;
UPDATE questions SET question='¿Se documentan y dan seguimiento a los hallazgos de auditorías o evaluaciones de riesgo anteriores sobre la base de datos?' WHERE id=75;

-- ------------------------------------------------------------
-- 4) Escala de madurez por pregunta: (question_id, level) es
--    UNIQUE KEY (uq_qms), asi que el upsert sobrescribe el texto
--    sin romper ninguna referencia ni afectar auditorias ya
--    guardadas (esta tabla no tiene FK entrante desde audits/
--    responses).
-- ------------------------------------------------------------
INSERT INTO question_maturity_scale (question_id, level, description) VALUES
(1, 0, 'No existe ninguna política de seguridad específica para la administración de bases de datos.'),
(1, 1, 'Hay lineamientos informales sobre seguridad de bases de datos, sin documento aprobado.'),
(1, 2, 'Existe un borrador de política de seguridad de bases de datos, pendiente de aprobación formal.'),
(1, 3, 'La política de seguridad de bases de datos está documentada, aprobada por la dirección y se aplica en la mayoría de los casos.'),
(1, 4, 'La política aprobada se supervisa periódicamente, con evidencia de su cumplimiento por parte de los DBA.'),
(1, 5, 'La política se revisa y mejora de forma continua, con indicadores de cumplimiento medidos en el tiempo.'),
(2, 0, 'La política no contempla requisitos de configuración segura (hardening) del motor de base de datos.'),
(2, 1, 'Existen recomendaciones de hardening informales, aplicadas según el criterio de cada DBA.'),
(2, 2, 'Hay lineamientos de configuración segura, pero se aplican de forma parcial entre los distintos motores.'),
(2, 3, 'La política define requisitos mínimos de hardening que se aplican en la mayoría de las bases de datos.'),
(2, 4, 'El cumplimiento del hardening se supervisa periódicamente, con evidencia de revisiones de configuración.'),
(2, 5, 'Los requisitos de hardening se revisan y actualizan continuamente conforme evolucionan las versiones del motor.'),
(3, 0, 'La política de seguridad de bases de datos no se comunica a nadie.'),
(3, 1, 'Se comunica de forma ocasional solo a algunos administradores, sin canal definido.'),
(3, 2, 'Existe un mecanismo de comunicación, pero no llega de forma consistente a todo el personal que administra o accede a las bases de datos.'),
(3, 3, 'La política se comunica formalmente a todo el personal DBA y de acceso relevante, al ingresar o en capacitaciones periódicas.'),
(3, 4, 'La comunicación se supervisa (acuses de recibido, registros de capacitación) y se verifica su alcance real.'),
(3, 5, 'Se mide la efectividad de la comunicación y el proceso de difusión mejora continuamente.'),
(4, 0, 'La política nunca se ha revisado desde que fue creada.'),
(4, 1, 'Se revisa de forma esporádica, sin calendario ni criterio definido.'),
(4, 2, 'Existe intención de revisión periódica, pero no se documenta ni se cumple de forma consistente.'),
(4, 3, 'Se revisa con una periodicidad definida y se actualiza cuando cambia la versión del motor o la normativa aplicable.'),
(4, 4, 'Las revisiones están documentadas y supervisadas, con evidencia formal de cada actualización.'),
(4, 5, 'El proceso de revisión está integrado en un ciclo de mejora continua, con indicadores de cuándo y por qué se actualiza.'),
(5, 0, 'No existen indicadores de ningún tipo para verificar el cumplimiento de la política de seguridad de bases de datos.'),
(5, 1, 'Se mencionan métricas de forma informal, sin registrarlas ni darles seguimiento.'),
(5, 2, 'Existen algunos indicadores definidos, pero no se recolectan de forma consistente.'),
(5, 3, 'Hay indicadores documentados que se recolectan periódicamente para la mayoría de las bases de datos.'),
(5, 4, 'Los indicadores se supervisan activamente y sirven de base para reportes formales de cumplimiento.'),
(5, 5, 'Los indicadores alimentan un proceso de mejora continua, con metas y revisión de tendencias en el tiempo.'),
(6, 0, 'No hay ninguna definición formal de las funciones y responsabilidades del DBA.'),
(6, 1, 'Las responsabilidades del DBA se asumen informalmente, sin documento que las respalde.'),
(6, 2, 'Existe una descripción de funciones del DBA, pero incompleta o desactualizada.'),
(6, 3, 'Las funciones y responsabilidades del DBA están documentadas y comunicadas para la mayoría de los casos.'),
(6, 4, 'La definición de funciones se supervisa periódicamente, con evidencia de su cumplimiento.'),
(6, 5, 'Las funciones del DBA se revisan y ajustan continuamente según la evolución de la organización.'),
(7, 0, 'No existe ninguna segregación entre las funciones de DBA, desarrollo y administración de aplicaciones.'),
(7, 1, 'La segregación se da de forma informal, dependiendo de quién esté disponible.'),
(7, 2, 'Existe un esquema de segregación de funciones, pero con excepciones no controladas (ej. desarrolladores con acceso de DBA).'),
(7, 3, 'Las funciones de DBA, desarrollo y aplicación están segregadas siguiendo un esquema documentado en la mayoría de los sistemas.'),
(7, 4, 'La segregación de funciones se supervisa, con evidencia de revisiones para detectar conflictos de interés.'),
(7, 5, 'El esquema de segregación se revisa y mejora continuamente conforme cambia la estructura organizacional.'),
(8, 0, 'No se documenta ni aprueba formalmente quién puede actuar como DBA de cada base de datos.'),
(8, 1, 'Se sabe informalmente quién administra cada base de datos, sin registro documentado.'),
(8, 2, 'Existe un registro parcial de DBA autorizados, incompleto o desactualizado.'),
(8, 3, 'Está formalmente documentado y aprobado quién puede actuar como DBA para la mayoría de las bases de datos.'),
(8, 4, 'La asignación de DBA se supervisa y se actualiza con evidencia cuando hay cambios de personal.'),
(8, 5, 'La gestión de quién actúa como DBA se revisa y mejora continuamente como parte del ciclo de vida del rol.'),
(9, 0, 'No se realiza ninguna capacitación en seguridad al personal DBA.'),
(9, 1, 'Hay capacitación informal u ocasional, sin plan ni periodicidad.'),
(9, 2, 'Existe un plan de capacitación para DBA, pero se ejecuta de forma parcial.'),
(9, 3, 'Se capacita periódicamente al personal DBA en buenas prácticas de seguridad del motor utilizado, con registro de asistencia.'),
(9, 4, 'La capacitación se supervisa y evalúa, con evidencia documentada de resultados.'),
(9, 5, 'El programa de capacitación de DBA se mide y mejora continuamente según los resultados obtenidos.'),
(10, 0, 'La administración de la base de datos depende por completo de una sola persona, sin ningún respaldo.'),
(10, 1, 'Existe algo de conocimiento compartido de forma informal, sin plan de respaldo de personal.'),
(10, 2, 'Hay un esquema de respaldo de personal esbozado, pero no se prueba ni se mantiene actualizado.'),
(10, 3, 'Existen procedimientos documentados de respaldo de personal DBA, aplicados en la mayoría de las bases de datos críticas.'),
(10, 4, 'El esquema de respaldo de personal se supervisa, con evidencia de que otras personas pueden asumir el rol si es necesario.'),
(10, 5, 'La continuidad de la administración de bases de datos se mide y mejora continuamente, minimizando la dependencia de una sola persona.'),
(11, 0, 'No existe ningún inventario de bases de datos, instancias o motores utilizados por la organización.'),
(11, 1, 'Hay listas parciales o informales de bases de datos, hechas de manera ocasional.'),
(11, 2, 'Existe un inventario, pero está incompleto o desactualizado respecto a las bases de datos reales.'),
(11, 3, 'El inventario de bases de datos está documentado, actualizado y cubre la mayoría de las instancias relevantes.'),
(11, 4, 'El inventario se supervisa y actualiza periódicamente, con evidencia de las revisiones realizadas.'),
(11, 5, 'El inventario se mantiene y mejora de forma continua, integrado a procesos automatizados de descubrimiento de bases de datos.'),
(12, 0, 'No se ha identificado ni clasificado la información sensible almacenada en las bases de datos.'),
(12, 1, 'Algunos datos sensibles se identifican de manera informal, sin criterio documentado.'),
(12, 2, 'Existe un esquema de clasificación, pero se aplica de forma inconsistente entre bases de datos.'),
(12, 3, 'La mayoría de los datos sensibles (personales, financieros, de salud) están identificados y clasificados siguiendo un esquema documentado.'),
(12, 4, 'La clasificación se supervisa periódicamente, con evidencia de revisiones para mantenerla vigente.'),
(12, 5, 'El esquema de clasificación se mide y mejora continuamente, ajustándose a nuevos tipos de datos o riesgos.'),
(13, 0, 'No se documenta qué esquemas, tablas o columnas contienen datos críticos o sensibles.'),
(13, 1, 'Se conoce informalmente dónde están los datos sensibles, sin documentación.'),
(13, 2, 'Existe documentación parcial de tablas/columnas sensibles, incompleta o desactualizada.'),
(13, 3, 'Los esquemas, tablas y columnas con datos críticos están documentados para la mayoría de las bases de datos.'),
(13, 4, 'Esta documentación se supervisa y se actualiza con evidencia cuando hay cambios de esquema.'),
(13, 5, 'La documentación de datos sensibles se revisa y mejora continuamente, idealmente con herramientas de descubrimiento automático.'),
(14, 0, 'El inventario de bases de datos nunca se revisa; pueden existir instancias no autorizadas sin que nadie lo sepa.'),
(14, 1, 'Se revisa de forma esporádica, sin calendario definido.'),
(14, 2, 'Existe intención de revisión periódica, pero no se cumple de forma consistente.'),
(14, 3, 'El inventario se revisa con una periodicidad definida para detectar instancias no autorizadas ("shadow IT").'),
(14, 4, 'Las revisiones se supervisan activamente y quedan evidencias formales de cada ciclo.'),
(14, 5, 'El proceso de revisión está integrado en un ciclo de mejora continua, con métricas de exactitud del inventario.'),
(15, 0, 'El inventario, si existe, no registra versión del motor, ubicación ni responsable de cada base de datos.'),
(15, 1, 'Esta información se conoce de forma informal, sin registrarla.'),
(15, 2, 'El inventario incluye parcialmente estos datos, de forma incompleta.'),
(15, 3, 'El inventario incluye versión del motor, ubicación y responsable para la mayoría de las bases de datos.'),
(15, 4, 'Esta información se supervisa y actualiza periódicamente, con evidencia de verificación.'),
(15, 5, 'El inventario detallado se mide y mejora continuamente, integrado a herramientas de gestión de configuración (CMDB).'),
(16, 0, 'No hay propietarios (data owners) asignados a ninguna base de datos.'),
(16, 1, 'Se asume informalmente quién es responsable de una base de datos, sin asignación documentada.'),
(16, 2, 'Existen propietarios asignados para algunas bases de datos, de forma incompleta.'),
(16, 3, 'Los propietarios de datos están formalmente asignados y documentados para la mayoría de las bases de datos.'),
(16, 4, 'La asignación de propietarios se supervisa y se evidencia su cumplimiento.'),
(16, 5, 'La gestión de propietarios de datos se revisa y mejora continuamente, integrada al ciclo de vida de cada base de datos.'),
(17, 0, 'No existe ninguna política de retención de datos.'),
(17, 1, 'La retención se decide de manera informal, caso por caso.'),
(17, 2, 'Existe una política de retención esbozada, pero no se aplica de forma consistente.'),
(17, 3, 'Hay políticas de retención documentadas que definen por cuánto tiempo se conserva la información en la mayoría de las bases de datos.'),
(17, 4, 'El cumplimiento de la retención se supervisa, con evidencia de revisiones periódicas.'),
(17, 5, 'La política de retención se mide y mejora continuamente, ajustada según requisitos legales y de negocio.'),
(18, 0, 'No se aplica ningún procedimiento de eliminación segura de datos o bases de datos que ya no se requieren.'),
(18, 1, 'La eliminación se hace de forma manual y ocasional, sin garantizar el borrado definitivo.'),
(18, 2, 'Existe un procedimiento de eliminación segura, pero se aplica de forma inconsistente.'),
(18, 3, 'Se aplican procedimientos de eliminación segura documentados para la mayoría de los datos y bases de datos dados de baja.'),
(18, 4, 'La eliminación segura se supervisa, con evidencia (certificados o registros) de cada eliminación realizada.'),
(18, 5, 'El proceso de eliminación segura se mide y mejora continuamente, alineado a la clasificación de la información.'),
(19, 0, 'Los ambientes de desarrollo o prueba mantienen copias de datos sensibles reales de forma indefinida, sin ningún control.'),
(19, 1, 'Se depuran datos de prueba de forma ocasional, sin criterio definido.'),
(19, 2, 'Existe intención de depurar los ambientes no productivos, pero no se cumple de forma consistente.'),
(19, 3, 'Se depuran o enmascaran los datos sensibles reales en ambientes de desarrollo y pruebas siguiendo un procedimiento documentado.'),
(19, 4, 'Esta depuración se supervisa periódicamente, con evidencia de verificación en los ambientes no productivos.'),
(19, 5, 'La gestión de datos en ambientes no productivos se mide y mejora continuamente, integrada a la política de datos.'),
(20, 0, 'No se documentan las excepciones cuando datos que deberían eliminarse se conservan por requerimiento legal.'),
(20, 1, 'Las excepciones se manejan de forma informal, sin registro.'),
(20, 2, 'Existe un registro parcial de excepciones, incompleto.'),
(20, 3, 'Las excepciones de conservación por requerimiento legal se documentan y justifican para la mayoría de los casos.'),
(20, 4, 'Estas excepciones se supervisan periódicamente, con evidencia de su vigencia y justificación legal.'),
(20, 5, 'La gestión de excepciones de retención se mide y mejora continuamente, revisada junto con asesoría legal.'),
(21, 0, 'No existe ningún procedimiento formal para crear, modificar o dar de baja cuentas de usuario en la base de datos.'),
(21, 1, 'Las cuentas se crean o eliminan de manera informal, sin procedimiento ni registro.'),
(21, 2, 'Existe un procedimiento esbozado, pero no se aplica de forma consistente (ej. cuentas huérfanas).'),
(21, 3, 'El ciclo de vida de las cuentas de base de datos sigue un procedimiento documentado en la mayoría de los casos.'),
(21, 4, 'El cumplimiento del procedimiento se supervisa, con evidencia de auditorías de cuentas activas e inactivas.'),
(21, 5, 'La gestión del ciclo de vida de cuentas se mide y mejora continuamente, idealmente automatizada e integrada con RR. HH.'),
(22, 0, 'Las cuentas por defecto del motor de base de datos permanecen activas, con sus credenciales originales.'),
(22, 1, 'Se han deshabilitado algunas cuentas por defecto de forma puntual, sin revisión sistemática.'),
(22, 2, 'Existe un lineamiento para deshabilitar cuentas por defecto, pero se aplica de forma inconsistente entre motores.'),
(22, 3, 'Las cuentas por defecto se eliminan o deshabilitan siguiendo un procedimiento documentado en la mayoría de las bases de datos.'),
(22, 4, 'Esta práctica se supervisa, con evidencia de verificaciones periódicas sobre cuentas predeterminadas.'),
(22, 5, 'El control de cuentas por defecto se mide y mejora continuamente, verificado en cada nueva instalación del motor.'),
(23, 0, 'No se exige ningún mecanismo de autenticación robusto para el acceso administrativo a la base de datos.'),
(23, 1, 'Se recomienda el uso de contraseñas fuertes o MFA de manera informal, sin exigirlo.'),
(23, 2, 'Existe una política de autenticación, pero se aplica solo en algunas bases de datos o de forma inconsistente.'),
(23, 3, 'Se exigen contraseñas robustas y/o MFA para el acceso administrativo en la mayoría de las bases de datos críticas.'),
(23, 4, 'El cumplimiento de la política de autenticación se supervisa, con evidencia de auditorías de configuración.'),
(23, 5, 'Los mecanismos de autenticación se revisan y mejoran continuamente según nuevas amenazas (ej. migración a MFA moderno).'),
(24, 0, 'Las cuentas de servicio/aplicación que se conectan a la base de datos no están identificadas ni controladas.'),
(24, 1, 'Se gestionan de forma informal, con credenciales compartidas entre varios sistemas.'),
(24, 2, 'Existen lineamientos para cuentas de servicio, pero se aplican de forma parcial.'),
(24, 3, 'Las cuentas de servicio están identificadas y no se comparten entre sistemas, siguiendo un procedimiento documentado.'),
(24, 4, 'La gestión de cuentas de servicio se supervisa periódicamente, con evidencia de revisiones y rotación de credenciales.'),
(24, 5, 'La gestión de cuentas de servicio se mide y mejora continuamente, idealmente con bóvedas de credenciales.'),
(25, 0, 'Los accesos otorgados nunca se revisan; pueden existir cuentas huérfanas sin que nadie lo detecte.'),
(25, 1, 'Se revisan cuentas de forma esporádica, sin calendario definido.'),
(25, 2, 'Existe intención de revisión periódica de cuentas, pero no se cumple de forma consistente.'),
(25, 3, 'Las cuentas activas se revisan con una periodicidad definida para detectar cuentas huérfanas o inactivas.'),
(25, 4, 'Las revisiones de cuentas se supervisan y generan evidencia formal (reportes firmados por responsables).'),
(25, 5, 'La revisión de cuentas se mide y mejora continuamente, con indicadores de cuentas huérfanas detectadas y corregidas.'),
(26, 0, 'No se aplica ningún criterio de mínimo privilegio; los accesos a la base de datos son amplios o sin control.'),
(26, 1, 'El principio de mínimo privilegio se aplica de forma esporádica, según criterio individual.'),
(26, 2, 'Existe un lineamiento de mínimo privilegio, pero se aplica de forma inconsistente entre bases de datos.'),
(26, 3, 'Los privilegios se otorgan siguiendo el principio de mínimo privilegio en la mayoría de las bases de datos.'),
(26, 4, 'La aplicación del mínimo privilegio se supervisa periódicamente, con evidencia de revisiones de accesos otorgados.'),
(26, 5, 'El modelo de privilegios se revisa y ajusta continuamente según el uso real y el riesgo (ej. accesos basados en roles).'),
(27, 0, 'No existen roles definidos; los privilegios se otorgan de forma individual y caso por caso.'),
(27, 1, 'Se usan roles de manera informal, sin una definición consistente.'),
(27, 2, 'Existen algunos roles definidos, pero conviven con privilegios individuales otorgados sin criterio.'),
(27, 3, 'Se utilizan roles definidos (lectura, escritura, administración) para otorgar privilegios en la mayoría de las bases de datos.'),
(27, 4, 'El uso de roles se supervisa, con evidencia de que los privilegios individuales son la excepción, no la regla.'),
(27, 5, 'El modelo de roles se revisa y mejora continuamente conforme cambian las necesidades de negocio.'),
(28, 0, 'No existe ningún procedimiento para aprobar o registrar privilegios elevados (DBA, sysadmin).'),
(28, 1, 'Los privilegios elevados se otorgan de manera informal, sin aprobación documentada.'),
(28, 2, 'Existe un procedimiento de aprobación, pero se aplica de forma inconsistente.'),
(28, 3, 'Los privilegios elevados se aprueban y registran siguiendo un procedimiento documentado en la mayoría de los casos.'),
(28, 4, 'El procedimiento se supervisa, con evidencia de aprobaciones formales para cada privilegio elevado otorgado.'),
(28, 5, 'El proceso de aprobación de privilegios elevados se mide y mejora continuamente (ej. accesos privilegiados por tiempo limitado).'),
(29, 0, 'Los privilegios otorgados nunca se recertifican una vez asignados.'),
(29, 1, 'La recertificación se hace de forma esporádica, sin calendario definido.'),
(29, 2, 'Existe intención de recertificar privilegios periódicamente, pero no se cumple de forma consistente.'),
(29, 3, 'Se realiza una recertificación periódica de privilegios de usuarios y aplicaciones, con una periodicidad definida.'),
(29, 4, 'La recertificación se supervisa, con evidencia formal de los resultados de cada ciclo.'),
(29, 5, 'La recertificación de privilegios se mide y mejora continuamente, idealmente apoyada en herramientas automatizadas.'),
(30, 0, 'Los privilegios nunca se revocan cuando un usuario cambia de función o deja la organización.'),
(30, 1, 'La revocación se hace de forma esporádica, cuando alguien lo solicita o lo nota.'),
(30, 2, 'Existe intención de revocar privilegios ante estos cambios, pero no se cumple de forma consistente.'),
(30, 3, 'Los privilegios se revocan oportunamente siguiendo un procedimiento documentado ante la mayoría de los cambios de personal.'),
(30, 4, 'La revocación oportuna se supervisa, con evidencia de que los cambios se reflejan a tiempo.'),
(30, 5, 'La gestión de revocación de privilegios se mide y mejora continuamente, idealmente integrada al proceso de RR. HH.'),
(31, 0, 'No se registra ninguna acción realizada por cuentas con privilegios administrativos sobre la base de datos.'),
(31, 1, 'El registro es manual y ocasional, sin herramientas dedicadas.'),
(31, 2, 'Existe algún registro de actividad privilegiada, pero no cubre todas las bases de datos relevantes.'),
(31, 3, 'Las acciones de cuentas privilegiadas se registran siguiendo un procedimiento documentado para la mayoría de las bases de datos.'),
(31, 4, 'El registro se supervisa activamente, generando alertas o reportes con evidencia de seguimiento.'),
(31, 5, 'El registro de actividad privilegiada se mide y mejora continuamente, idealmente con correlación automatizada de eventos (SIEM).'),
(32, 0, 'No existen alertas de ningún tipo ante actividades inusuales sobre la base de datos.'),
(32, 1, 'Las alertas, si existen, se revisan de forma manual y ocasional.'),
(32, 2, 'Existen algunas alertas configuradas, pero no cubren escenarios como exportaciones masivas o cambios de esquema.'),
(32, 3, 'Hay alertas automáticas documentadas ante actividades inusuales (consultas masivas, cambios de esquema, exportaciones) en la mayoría de las bases de datos.'),
(32, 4, 'Las alertas se supervisan activamente, con evidencia de seguimiento y resolución de cada una.'),
(32, 5, 'El sistema de alertas se mide y mejora continuamente, con analítica de comportamiento y respuesta automatizada.'),
(33, 0, 'Los registros de auditoría de la base de datos pueden ser alterados o eliminados libremente por los propios DBA.'),
(33, 1, 'Existe alguna restricción informal, pero sin control técnico que la respalde.'),
(33, 2, 'Hay un mecanismo de protección de logs, pero se aplica de forma inconsistente entre bases de datos.'),
(33, 3, 'Los registros de auditoría se protegen contra alteración o eliminación por los DBA en la mayoría de los sistemas.'),
(33, 4, 'Esta protección se supervisa, con evidencia de verificaciones de integridad de los registros.'),
(33, 5, 'La protección de los registros de auditoría se mide y mejora continuamente, idealmente con almacenamiento inmutable o centralizado (WORM/SIEM).'),
(34, 0, 'Los registros de actividad de la base de datos nunca se revisan.'),
(34, 1, 'Se revisan de forma esporádica, sin calendario ni responsable definido.'),
(34, 2, 'Existe intención de revisión periódica, pero la hace el mismo DBA cuya actividad se audita.'),
(34, 3, 'Los registros se revisan periódicamente por personal distinto al DBA auditado, siguiendo un procedimiento documentado.'),
(34, 4, 'Estas revisiones se supervisan, con evidencia formal (reportes firmados) de cada ciclo.'),
(34, 5, 'La revisión de registros se mide y mejora continuamente, con indicadores de hallazgos detectados y corregidos.'),
(35, 0, 'Los registros de auditoría de la base de datos no se conservan; se sobrescriben o eliminan sin control.'),
(35, 1, 'Se conservan por un período indefinido o informal, sin política de retención.'),
(35, 2, 'Existe un período de retención definido, pero no se cumple de forma consistente.'),
(35, 3, 'Los registros de auditoría se conservan durante un período definido acorde a la normativa aplicable, en la mayoría de los casos.'),
(35, 4, 'El cumplimiento de la retención de logs se supervisa, con evidencia de verificaciones periódicas.'),
(35, 5, 'La política de retención de registros se mide y mejora continuamente, ajustada a requisitos legales y forenses.'),
(36, 0, 'No se utiliza ningún mecanismo de cifrado de datos en reposo en las bases de datos con información sensible.'),
(36, 1, 'El cifrado en reposo se aplica de forma puntual y a criterio individual.'),
(36, 2, 'Existe un lineamiento sobre cifrado en reposo, pero se aplica de forma parcial entre bases de datos.'),
(36, 3, 'Se utiliza cifrado de datos en reposo (TDE u otro mecanismo) en la mayoría de las bases de datos con información sensible.'),
(36, 4, 'La aplicación del cifrado se supervisa, con evidencia de verificaciones sobre qué bases de datos están cifradas.'),
(36, 5, 'La protección de datos en reposo se revisa y mejora continuamente según la clasificación de la información.'),
(37, 0, 'Las llaves de cifrado no tienen ninguna gestión ni protección definida; suelen estar junto a los propios datos.'),
(37, 1, 'La gestión de llaves es informal e inconsistente entre bases de datos.'),
(37, 2, 'Existe algún lineamiento de gestión de llaves, pero sin un mecanismo centralizado independiente del motor.'),
(37, 3, 'Las llaves se gestionan y protegen mediante un mecanismo independiente del motor de base de datos (KMS/HSM) en la mayoría de los casos.'),
(37, 4, 'La gestión de llaves se supervisa periódicamente, con evidencia de control de acceso a ellas.'),
(37, 5, 'Las llaves se gestionan de forma centralizada con ciclo de vida completo, medida y mejorada continuamente.'),
(38, 0, 'Las llaves criptográficas nunca se rotan desde su creación.'),
(38, 1, 'La rotación se hace de forma esporádica, sin calendario definido.'),
(38, 2, 'Existe intención de rotar llaves periódicamente, pero no se cumple de forma consistente.'),
(38, 3, 'Existen procedimientos documentados de rotación periódica de llaves, aplicados en la mayoría de los casos.'),
(38, 4, 'La rotación de llaves se supervisa, con evidencia formal de cada ciclo realizado.'),
(38, 5, 'La rotación de llaves se mide y mejora continuamente, automatizada según el riesgo de exposición.'),
(39, 0, 'Los respaldos de la base de datos se almacenan sin ningún cifrado.'),
(39, 1, 'Algunos respaldos se cifran de forma puntual, sin criterio uniforme.'),
(39, 2, 'Existe un lineamiento de cifrado de respaldos, pero se aplica de forma inconsistente.'),
(39, 3, 'Los respaldos de bases de datos con información sensible se cifran siguiendo un procedimiento documentado en la mayoría de los casos.'),
(39, 4, 'El cifrado de respaldos se supervisa periódicamente, con evidencia de verificación.'),
(39, 5, 'La protección criptográfica de los respaldos se mide y mejora continuamente, integrada a la política de respaldo.'),
(40, 0, 'Nunca se revisa si los algoritmos y parámetros criptográficos utilizados siguen siendo adecuados.'),
(40, 1, 'La revisión se hace de forma esporádica, sin calendario definido.'),
(40, 2, 'Existe intención de revisión periódica, pero no se cumple de forma consistente.'),
(40, 3, 'Se revisa con una periodicidad definida que los algoritmos y parámetros criptográficos utilizados siguen siendo adecuados.'),
(40, 4, 'Estas revisiones se supervisan y quedan evidencias formales de cada actualización.'),
(40, 5, 'Los estándares criptográficos utilizados se revisan y actualizan continuamente conforme evolucionan las buenas prácticas del sector.'),
(41, 0, 'Las conexiones hacia la base de datos no utilizan cifrado en tránsito; viajan en texto plano.'),
(41, 1, 'El cifrado de conexiones se aplica de forma puntual, sin exigirlo de forma general.'),
(41, 2, 'Existe un lineamiento de cifrado en tránsito, pero se aplica de forma inconsistente entre bases de datos.'),
(41, 3, 'Las conexiones hacia la base de datos utilizan cifrado TLS/SSL de forma obligatoria en la mayoría de los casos.'),
(41, 4, 'El cumplimiento del cifrado en tránsito se supervisa, con evidencia de verificaciones periódicas.'),
(41, 5, 'La seguridad de las conexiones se mide y mejora continuamente conforme evolucionan las amenazas y estándares.'),
(42, 0, 'El acceso remoto administrativo a la base de datos no está restringido ni se cifra de ninguna forma.'),
(42, 1, 'Se usan canales seguros de forma ocasional, según criterio individual.'),
(42, 2, 'Existe un lineamiento de acceso remoto seguro, pero se aplica de forma inconsistente.'),
(42, 3, 'El acceso remoto administrativo se realiza mediante VPN o túneles seguros siguiendo un procedimiento documentado.'),
(42, 4, 'El uso de canales seguros se supervisa, con evidencia de auditorías de accesos remotos.'),
(42, 5, 'El acceso remoto seguro se mide y mejora continuamente (ej. modelo de confianza cero, monitoreo continuo de sesiones).'),
(43, 0, 'No se enmascaran ni anonimizan los datos sensibles cuando se copian a ambientes de desarrollo o pruebas.'),
(43, 1, 'El enmascaramiento se aplica de forma puntual, según criterio individual.'),
(43, 2, 'Existe un lineamiento de enmascaramiento, pero se aplica de forma parcial o incompleta.'),
(43, 3, 'Los datos sensibles se enmascaran o anonimizan siguiendo un procedimiento documentado en la mayoría de los ambientes no productivos.'),
(43, 4, 'El enmascaramiento se supervisa periódicamente, con evidencia de verificación en cada ambiente.'),
(43, 5, 'El proceso de enmascaramiento se mide y mejora continuamente, idealmente automatizado en cada copia de datos.'),
(44, 0, 'Las cadenas de conexión y credenciales de acceso a la base de datos se almacenan en texto plano en el código o configuración.'),
(44, 1, 'Se protegen de forma informal (ej. archivos separados), sin un mecanismo dedicado.'),
(44, 2, 'Existe un mecanismo de protección de credenciales, pero se aplica de forma inconsistente entre aplicaciones.'),
(44, 3, 'Las cadenas de conexión y credenciales se almacenan de forma segura (gestor de secretos, variables cifradas) en la mayoría de los sistemas.'),
(44, 4, 'Esta práctica se supervisa periódicamente, con evidencia de revisiones de código y configuración.'),
(44, 5, 'La gestión de credenciales de conexión se mide y mejora continuamente, integrada a una bóveda de secretos centralizada.'),
(45, 0, 'No se valida de ninguna forma si las herramientas de administración remota transmiten credenciales sin cifrar.'),
(45, 1, 'La validación se hace de forma esporádica y no sistemática.'),
(45, 2, 'Existe intención de validar las herramientas utilizadas, pero no se cumple de forma consistente.'),
(45, 3, 'Se valida que las herramientas de administración remota no transmitan credenciales sin cifrar, siguiendo un procedimiento documentado.'),
(45, 4, 'Esta validación se supervisa periódicamente, con evidencia de revisiones técnicas.'),
(45, 5, 'La validación de herramientas de administración remota se mide y mejora continuamente, incorporada a la evaluación de nuevas herramientas.'),
(46, 0, 'No existe ninguna política de respaldo de bases de datos.'),
(46, 1, 'Los respaldos se hacen de forma manual y ocasional, sin política ni calendario.'),
(46, 2, 'Existe una política de respaldo esbozada, pero no se cumple de forma consistente.'),
(46, 3, 'Existe una política de respaldo documentada que define frecuencia, tipo y retención, aplicada en la mayoría de los casos.'),
(46, 4, 'El cumplimiento de la política se supervisa, con evidencia de la ejecución de los respaldos programados.'),
(46, 5, 'La política de respaldo se mide y mejora continuamente según los resultados obtenidos y necesidades del negocio.'),
(47, 0, 'Los respaldos no cubren las bases de datos identificadas como críticas.'),
(47, 1, 'Algunas bases de datos críticas se respaldan de forma ocasional, sin garantía de cobertura total.'),
(47, 2, 'Existe cobertura parcial de respaldos sobre las bases de datos críticas del inventario.'),
(47, 3, 'Los respaldos cubren la mayoría de las bases de datos críticas identificadas en el inventario.'),
(47, 4, 'La cobertura de respaldos se supervisa periódicamente, con evidencia de verificación contra el inventario.'),
(47, 5, 'La cobertura de respaldos se mide y mejora continuamente, verificada automáticamente contra el inventario de bases de datos.'),
(48, 0, 'Nunca se realizan pruebas de restauración de los respaldos.'),
(48, 1, 'Se han hecho pruebas de restauración puntuales, sin planificación ni periodicidad.'),
(48, 2, 'Existe intención de probar los respaldos periódicamente, pero no se cumple de forma consistente.'),
(48, 3, 'Se realizan pruebas periódicas de restauración para verificar que los respaldos son utilizables, con una periodicidad definida.'),
(48, 4, 'Las pruebas de restauración se supervisan, con evidencia formal de los resultados obtenidos.'),
(48, 5, 'Las pruebas de restauración se miden y mejoran continuamente, con métricas de tiempo de recuperación real.'),
(49, 0, 'No se documentan objetivos de tiempo de recuperación (RTO) ni punto de recuperación (RPO) para ninguna base de datos.'),
(49, 1, 'Se mencionan de forma informal, sin documentarlos ni validarlos.'),
(49, 2, 'Existen RTO/RPO definidos para algunas bases de datos, de forma incompleta.'),
(49, 3, 'Los objetivos de RTO y RPO están documentados para las bases de datos críticas en la mayoría de los casos.'),
(49, 4, 'El cumplimiento de RTO/RPO se supervisa, con evidencia de mediciones en pruebas de restauración.'),
(49, 5, 'Los objetivos de RTO/RPO se miden y mejoran continuamente, ajustados según el impacto real al negocio.'),
(50, 0, 'Los respaldos se almacenan en el mismo servidor de producción, sin ninguna separación.'),
(50, 1, 'Existe alguna copia en otra ubicación, pero de forma informal y no sistemática.'),
(50, 2, 'Los respaldos se almacenan en otra ubicación, pero de forma inconsistente entre bases de datos.'),
(50, 3, 'Los respaldos se almacenan en una ubicación distinta (física o lógicamente) al servidor de producción, en la mayoría de los casos.'),
(50, 4, 'Esta separación se supervisa periódicamente, con evidencia de verificación de la ubicación de los respaldos.'),
(50, 5, 'La estrategia de ubicación de respaldos se mide y mejora continuamente (ej. copias en múltiples sitios o en la nube).'),
(51, 0, 'No existe ningún procedimiento para identificar ni aplicar parches de seguridad al motor de base de datos.'),
(51, 1, 'Los parches se aplican de forma esporádica, sin calendario ni evaluación previa.'),
(51, 2, 'Existe un proceso de gestión de parches esbozado, pero no se cumple de forma consistente.'),
(51, 3, 'Existe un procedimiento formal para identificar y aplicar parches de seguridad, aplicado en la mayoría de las bases de datos.'),
(51, 4, 'La gestión de parches se supervisa, con evidencia de los parches aplicados y pendientes.'),
(51, 5, 'La gestión de parches se mide y mejora continuamente, con métricas de tiempo de exposición y remediación.'),
(52, 0, 'No se realiza ningún escaneo o evaluación de vulnerabilidades sobre los servidores de base de datos.'),
(52, 1, 'Se han hecho evaluaciones puntuales, sin planificación ni periodicidad.'),
(52, 2, 'Existe intención de escanear periódicamente, pero no se cumple de forma consistente.'),
(52, 3, 'Se realizan escaneos periódicos de vulnerabilidades sobre los servidores de base de datos, con una periodicidad definida.'),
(52, 4, 'Los escaneos se supervisan, con evidencia formal de hallazgos y su remediación.'),
(52, 5, 'La gestión de vulnerabilidades se mide y mejora continuamente, con métricas de tiempo de detección y cierre.'),
(53, 0, 'Los parches críticos no se aplican, o se aplican sin ningún plazo definido.'),
(53, 1, 'Se aplican de forma esporádica, según disponibilidad del personal.'),
(53, 2, 'Existe un plazo objetivo para parches críticos, pero no se cumple de forma consistente.'),
(53, 3, 'Los parches críticos se aplican dentro de un plazo definido después de su publicación, en la mayoría de los casos.'),
(53, 4, 'El cumplimiento de este plazo se supervisa, con evidencia de los tiempos reales de aplicación.'),
(53, 5, 'El tiempo de aplicación de parches críticos se mide y mejora continuamente, con metas cada vez más exigentes.'),
(54, 0, 'Los parches se aplican directamente en producción, sin probarse antes en ningún ambiente.'),
(54, 1, 'Se prueban de forma ocasional, sin un ambiente dedicado.'),
(54, 2, 'Existe un ambiente de pruebas, pero no siempre se usa antes de aplicar parches.'),
(54, 3, 'Los parches se prueban en un ambiente no productivo antes de aplicarlos en producción, en la mayoría de los casos.'),
(54, 4, 'Este proceso se supervisa, con evidencia formal de las pruebas realizadas antes de cada despliegue.'),
(54, 5, 'Las pruebas de parches se miden y mejoran continuamente, con métricas de incidentes evitados.'),
(55, 0, 'No se mantiene ningún registro de las versiones del motor de base de datos ni de su estado de soporte.'),
(55, 1, 'Se conoce informalmente qué versiones se usan, sin registrarlo.'),
(55, 2, 'Existe un registro parcial de versiones, incompleto o desactualizado.'),
(55, 3, 'Se mantiene un registro de las versiones del motor y su estado de soporte (fin de vida/EOL) para la mayoría de las bases de datos.'),
(55, 4, 'Este registro se supervisa y actualiza periódicamente, con evidencia de verificación.'),
(55, 5, 'El registro de versiones y soporte se mide y mejora continuamente, con alertas anticipadas ante el fin de vida de una versión.'),
(56, 0, 'No existe ningún procedimiento de gestión de cambios para modificaciones de esquema o configuración de la base de datos.'),
(56, 1, 'Los cambios se realizan de forma ad hoc, sin aprobación ni registro.'),
(56, 2, 'Existe un proceso de gestión de cambios esbozado, pero no se sigue de forma consistente.'),
(56, 3, 'Los cambios de esquema, configuración o parámetros siguen un procedimiento documentado en la mayoría de los casos.'),
(56, 4, 'El proceso de cambios se supervisa, con evidencia de aprobaciones y pruebas antes del despliegue.'),
(56, 5, 'La gestión de cambios se mide y mejora continuamente (ej. tasa de cambios fallidos, tiempo de despliegue).'),
(57, 0, 'Los cambios en producción se ejecutan sin ninguna aprobación previa.'),
(57, 1, 'La aprobación, si existe, la da informalmente la misma persona que ejecuta el cambio.'),
(57, 2, 'Existe un esquema de aprobación, pero se aplica de forma inconsistente.'),
(57, 3, 'Los cambios en producción requieren aprobación previa de una persona distinta a quien los ejecuta, en la mayoría de los casos.'),
(57, 4, 'Esta separación de funciones se supervisa, con evidencia de aprobaciones documentadas para cada cambio.'),
(57, 5, 'El esquema de aprobación se mide y mejora continuamente, integrado a un flujo de trabajo formal (ticketing).'),
(58, 0, 'Los cambios de esquema o configuración se aplican directamente en producción, sin probarse antes.'),
(58, 1, 'Se prueban de forma ocasional, sin un ambiente dedicado y consistente.'),
(58, 2, 'Existe un ambiente de pruebas, pero no siempre se usa antes de aplicar cambios.'),
(58, 3, 'Los cambios se prueban en un ambiente de pruebas antes de aplicarlos en producción, en la mayoría de los casos.'),
(58, 4, 'Este proceso se supervisa, con evidencia formal de las pruebas realizadas antes de cada despliegue.'),
(58, 5, 'Las pruebas de cambios se miden y mejoran continuamente, con métricas de incidentes evitados.'),
(59, 0, 'No existe ningún procedimiento de reversión (rollback) ante cambios fallidos.'),
(59, 1, 'La reversión se improvisa caso por caso, sin plan documentado.'),
(59, 2, 'Existe un procedimiento de rollback esbozado, pero no probado ni actualizado.'),
(59, 3, 'Existe un procedimiento de reversión documentado para la mayoría de los cambios sobre la base de datos.'),
(59, 4, 'El procedimiento de rollback se supervisa y prueba periódicamente, con evidencia de los resultados.'),
(59, 5, 'El procedimiento de reversión se mide y mejora continuamente tras cada ejecución real o simulacro.'),
(60, 0, 'No se mantiene ningún historial de los cambios realizados sobre la estructura de las bases de datos.'),
(60, 1, 'Se guarda algo de información de forma informal y desorganizada.'),
(60, 2, 'Existe un historial parcial de cambios, incompleto o de difícil consulta.'),
(60, 3, 'Se mantiene un historial documentado de los cambios de estructura para la mayoría de las bases de datos.'),
(60, 4, 'Este historial se supervisa y se verifica periódicamente su completitud.'),
(60, 5, 'El historial de cambios se mide y mejora continuamente, integrado a herramientas de control de versiones de esquema.'),
(61, 0, 'No existe ningún control de acceso físico a los servidores donde residen las bases de datos.'),
(61, 1, 'Hay controles básicos e informales (ej. una llave compartida), sin autorización formal.'),
(61, 2, 'Existe un control de acceso físico, pero sin autorización formal documentada ni registro consistente.'),
(61, 3, 'El acceso a los servidores de base de datos requiere autorización formal documentada, en la mayoría de los casos.'),
(61, 4, 'El acceso físico se supervisa, con revisión periódica de quién tiene acceso autorizado.'),
(61, 5, 'El control de acceso físico se mide y mejora continuamente, integrado a sistemas de control electrónico y auditoría.'),
(62, 0, 'No se registra ni audita el ingreso de personas al centro de datos o cuarto de servidores.'),
(62, 1, 'El registro es manual e informal, sin control de consistencia.'),
(62, 2, 'Existe un registro de ingresos, pero incompleto o sin revisión periódica.'),
(62, 3, 'El ingreso al centro de datos se registra y audita siguiendo un procedimiento documentado en la mayoría de los casos.'),
(62, 4, 'Este registro se supervisa, con evidencia de revisiones periódicas de los accesos.'),
(62, 5, 'El registro de ingresos se mide y mejora continuamente, con control electrónico y alertas ante accesos no autorizados.'),
(63, 0, 'No existen controles adicionales para el acceso a las zonas donde están los servidores de base de datos.'),
(63, 1, 'Hay algún control adicional aislado, sin criterio uniforme.'),
(63, 2, 'Existen controles adicionales, pero se aplican de forma inconsistente entre zonas críticas.'),
(63, 3, 'Se cuenta con controles adicionales (biométricos, tarjetas, doble factor) para el acceso a zonas críticas, en la mayoría de los casos.'),
(63, 4, 'Estos controles se supervisan periódicamente, con evidencia de pruebas de funcionamiento.'),
(63, 5, 'Los controles de acceso adicionales se miden y mejoran continuamente según el nivel de riesgo de cada zona.'),
(64, 0, 'No se gestionan accesos temporales de proveedores o personal externo a la infraestructura de base de datos.'),
(64, 1, 'Los proveedores ingresan sin registro ni acompañamiento formal.'),
(64, 2, 'Existe un registro de accesos temporales, pero incompleto o sin control de acompañamiento.'),
(64, 3, 'Los accesos temporales de proveedores se gestionan siguiendo un procedimiento documentado (registro, acompañamiento, vigencia limitada).'),
(64, 4, 'La gestión de accesos temporales se supervisa, con evidencia de registros revisados periódicamente.'),
(64, 5, 'La gestión de accesos de proveedores se mide y mejora continuamente, idealmente con control electrónico de accesos temporales.'),
(65, 0, 'No se realiza ninguna inspección periódica de los controles físicos de seguridad del centro de datos.'),
(65, 1, 'Las inspecciones se hacen de forma esporádica, solo cuando algo falla.'),
(65, 2, 'Existe intención de inspeccionar periódicamente, pero no se cumple de forma consistente.'),
(65, 3, 'Los controles físicos se inspeccionan con una periodicidad definida y documentada.'),
(65, 4, 'Las inspecciones se supervisan activamente, con evidencia formal de cada una realizada.'),
(65, 5, 'Las inspecciones de controles físicos se miden y mejoran continuamente según el historial de hallazgos.'),
(66, 0, 'Los servidores de base de datos no cuentan con ningún sistema de respaldo eléctrico ante cortes de energía.'),
(66, 1, 'Existe un respaldo eléctrico básico (ej. UPS pequeño), sin plan formal ni cobertura garantizada.'),
(66, 2, 'Existen sistemas de respaldo eléctrico, pero sin redundancia completa ni pruebas periódicas.'),
(66, 3, 'Los servidores de base de datos cuentan con UPS y/o planta eléctrica documentados para la mayoría de los casos críticos.'),
(66, 4, 'Estos sistemas se supervisan periódicamente, con evidencia de pruebas de los respaldos eléctricos.'),
(66, 5, 'La infraestructura eléctrica se mide y mejora continuamente, con monitoreo en tiempo real de su disponibilidad.'),
(67, 0, 'No existen sistemas de control de temperatura ni humedad para el hardware que aloja las bases de datos.'),
(67, 1, 'Hay medidas informales y puntuales (ej. un aire acondicionado doméstico), sin monitoreo.'),
(67, 2, 'Existen sistemas de climatización, pero sin monitoreo ni umbrales definidos.'),
(67, 3, 'Se cuenta con sistemas de control de temperatura y humedad adecuados, con umbrales definidos y monitoreados.'),
(67, 4, 'Las condiciones ambientales se supervisan activamente, con alertas y evidencia de seguimiento.'),
(67, 5, 'El control ambiental se mide y mejora continuamente, integrado a sistemas de monitoreo automatizado.'),
(68, 0, 'No existen sistemas de detección ni supresión de incendios en el área de los servidores de base de datos.'),
(68, 1, 'Hay extintores básicos, sin sistema de detección ni mantenimiento programado.'),
(68, 2, 'Existen sistemas de detección/incendio, pero no se revisan ni prueban con regularidad.'),
(68, 3, 'Se cuenta con sistemas de detección y supresión de incendios documentados, con mantenimiento periódico.'),
(68, 4, 'Los sistemas se supervisan activamente, con evidencia de pruebas y certificaciones vigentes.'),
(68, 5, 'La protección contra incendios se mide y mejora continuamente, alineada a estándares de la industria.'),
(69, 0, 'No existen planes de contingencia documentados ante fallas ambientales (energía, climatización, incendio).'),
(69, 1, 'Hay respuestas improvisadas ante fallas ambientales, sin plan documentado.'),
(69, 2, 'Existe un plan esbozado, pero no probado ni actualizado.'),
(69, 3, 'Hay planes de contingencia documentados ante fallas ambientales, conocidos por el personal responsable.'),
(69, 4, 'Los planes se supervisan y prueban periódicamente, con evidencia de los resultados de las pruebas.'),
(69, 5, 'Los planes de contingencia ambiental se miden y mejoran continuamente tras cada prueba o evento real.'),
(70, 0, 'Nunca se prueban los sistemas de respaldo eléctrico ni ambiental de los servidores de base de datos.'),
(70, 1, 'Se han hecho pruebas puntuales, sin planificación ni periodicidad.'),
(70, 2, 'Existe intención de probar estos sistemas periódicamente, pero no se cumple de forma consistente.'),
(70, 3, 'Se realizan pruebas periódicas de los sistemas de respaldo eléctrico y ambiental, con una periodicidad definida.'),
(70, 4, 'Estas pruebas se supervisan, con evidencia formal de los resultados obtenidos.'),
(70, 5, 'Las pruebas de los sistemas de respaldo se miden y mejoran continuamente, reduciendo el tiempo de indisponibilidad ante fallas reales.'),
(71, 0, 'No se realiza ninguna evaluación de riesgos específica para la administración de las bases de datos.'),
(71, 1, 'Los riesgos se identifican de manera informal y ocasional.'),
(71, 2, 'Existe un proceso de evaluación de riesgos, pero no se aplica de forma periódica ni consistente a las bases de datos.'),
(71, 3, 'Se realiza una evaluación periódica de riesgos específica para las bases de datos, siguiendo un proceso documentado.'),
(71, 4, 'El proceso de evaluación de riesgos se supervisa, con evidencia formal de cada ciclo realizado.'),
(71, 5, 'La evaluación de riesgos de bases de datos se mide y mejora continuamente, integrada a la gestión de riesgos de la organización.'),
(72, 0, 'No se identifican los requisitos legales o regulatorios aplicables al tratamiento de datos almacenados.'),
(72, 1, 'Se conocen algunos requisitos de forma informal, sin documentarlos.'),
(72, 2, 'Existe una identificación parcial de requisitos legales (ej. protección de datos personales), incompleta o desactualizada.'),
(72, 3, 'Los requisitos legales y regulatorios aplicables al tratamiento de datos están identificados y documentados de forma actualizada.'),
(72, 4, 'La identificación de requisitos se supervisa periódicamente, con evidencia de revisiones legales.'),
(72, 5, 'La identificación de requisitos legales se mide y mejora continuamente, con vigilancia activa de cambios normativos.'),
(73, 0, 'No se realizan auditorías internas sobre los controles de seguridad de las bases de datos.'),
(73, 1, 'Se hacen revisiones informales y ocasionales, sin metodología definida.'),
(73, 2, 'Existe intención de auditar periódicamente los controles de bases de datos, pero no se cumple de forma consistente.'),
(73, 3, 'Se realizan auditorías internas sobre los controles de seguridad de las bases de datos con una periodicidad definida.'),
(73, 4, 'Las auditorías se supervisan activamente, con evidencia formal de su ejecución y alcance.'),
(73, 5, 'El programa de auditoría interna de bases de datos se mide y mejora continuamente, ampliando su alcance según el riesgo.'),
(74, 0, 'No existe ningún procedimiento para responder ante incidentes de seguridad que involucren bases de datos.'),
(74, 1, 'Los incidentes se atienden de manera informal, según quien esté disponible en el momento.'),
(74, 2, 'Existe un procedimiento esbozado de respuesta a incidentes de bases de datos, pero no se aplica de forma consistente.'),
(74, 3, 'Hay un procedimiento formal para responder ante incidentes que involucren bases de datos (fuga, corrupción, acceso no autorizado), documentado y aplicado en la mayoría de los casos.'),
(74, 4, 'El procedimiento se supervisa activamente, con evidencia de tiempos de respuesta medidos.'),
(74, 5, 'La respuesta a incidentes de bases de datos se mide y mejora continuamente, con lecciones aprendidas incorporadas al proceso.'),
(75, 0, 'Los hallazgos de auditorías o evaluaciones de riesgo anteriores sobre la base de datos no se documentan ni se les da seguimiento.'),
(75, 1, 'Se anotan hallazgos de forma informal, sin plan de acción.'),
(75, 2, 'Existen hallazgos documentados, pero el seguimiento de las acciones correctivas es incompleto.'),
(75, 3, 'Los hallazgos y sus acciones correctivas se documentan y se les da seguimiento siguiendo un procedimiento definido, en la mayoría de los casos.'),
(75, 4, 'El seguimiento de acciones correctivas se supervisa, con evidencia del estado de cada hallazgo.'),
(75, 5, 'El seguimiento de hallazgos se mide y mejora continuamente, con indicadores de tiempo de cierre y recurrencia.')
ON DUPLICATE KEY UPDATE description = VALUES(description);
