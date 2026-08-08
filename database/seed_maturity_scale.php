<?php
declare(strict_types=1);

/**
 * Genera las 6 descripciones de madurez (0-5) por pregunta, aplicando la
 * escala OFICIAL del enunciado EIF402 (0=no existe ... 5=optimizado) al
 * contenido especifico de cada una de las 75 preguntas.
 *
 * Fundamentado en practicas reales y documentadas de auditoria/gestion de
 * seguridad (no en texto inventado de la norma ISO/IEC 27002 en si, que es
 * de pago):
 *  - Revision periodica de accesos y principio de menor privilegio:
 *    ISO/IEC 27002 clausula 8.2 "Privileged access rights" / modelo de
 *    capacidad ISO/IEC 15504 (no-existente/inicial/repetible/definido/
 *    gestionado/optimizado), citado en literatura de auditoria de TI.
 *  - Respaldos y continuidad: controles ITGC de "Backup and Recovery"
 *    (objetivos de disponibilidad documentados, RTO/RPO, pruebas de
 *    restauracion periodicas, planes de continuidad con frecuencia de
 *    prueba definida).
 *  - Gestion de llaves criptograficas: modelo de madurez de key management
 *    (bajo = caotico/sin criterio; medio = organizado pero inconsistente
 *    entre equipos; alto = plataforma centralizada, ciclo de vida
 *    gestionado, HSM/almacenamiento seguro dedicado).
 *  - Gestion de incidentes: modelo de madurez de respuesta a incidentes
 *    (reactivo/no planificado -> estandarizado/monitoreado ->
 *    gestionado cuantitativamente/automatizado -> predictivo/mejora
 *    continua).
 *  - Seguridad fisica y ambiental: controles ITGC/HITRUST de autorizacion
 *    formal de acceso, revision periodica (trimestral) de accesos fisicos,
 *    y controles ambientales (incendio, energia, climatizacion).
 *  - Gestion de riesgos: ciclo PDCA de ISO/IEC 27005 (planificar, hacer,
 *    verificar, actuar) para identificacion, tratamiento, comunicacion y
 *    revision de riesgos.
 *
 * return array<int,array<int,string>> question_id => [0=>..,1=>..,...,5=>..]
 */
function maturity_scale_seed(): array
{
    return [
        // ===================== C1 - Politica de seguridad =====================
        1 => [
            0 => 'No existe ninguna política de seguridad, ni siquiera en borrador.',
            1 => 'Hay lineamientos de seguridad informales (correos, acuerdos verbales), sin un documento formal.',
            2 => 'Existe un borrador de política, pero no ha sido aprobado formalmente por la dirección.',
            3 => 'La política está documentada, aprobada por la dirección y se aplica en la mayoría de los procesos.',
            4 => 'La política aprobada se supervisa periódicamente y hay evidencia de su cumplimiento (actas, firmas, revisiones).',
            5 => 'La política se revisa y mejora de forma continua, con indicadores de cumplimiento medidos en el tiempo.',
        ],
        2 => [
            0 => 'La política de seguridad no se comunica a nadie.',
            1 => 'Se comunica de manera ocasional o solo a algunos empleados, sin un canal definido.',
            2 => 'Existe un mecanismo de comunicación, pero no llega de forma consistente a todo el personal ni a terceros.',
            3 => 'La política se comunica formalmente a todo el personal y terceros relevantes al ingresar o en capacitaciones periódicas.',
            4 => 'La comunicación se supervisa (acuses de recibido, registros de asistencia) y se verifica su alcance real.',
            5 => 'Se mide la efectividad de la comunicación (encuestas, evaluaciones) y el proceso de difusión mejora continuamente.',
        ],
        3 => [
            0 => 'La política nunca se ha revisado desde que fue creada.',
            1 => 'Se revisa de forma esporádica, sin calendario ni criterio definido.',
            2 => 'Existe intención de revisión periódica, pero no se documenta ni se cumple de forma consistente.',
            3 => 'Se revisa con una periodicidad definida (por ejemplo, anual) y se actualiza cuando hay cambios relevantes.',
            4 => 'Las revisiones están documentadas y supervisadas, y quedan evidencias formales de cada actualización.',
            5 => 'El proceso de revisión está integrado en un ciclo de mejora continua, con indicadores de cuándo y por qué se actualiza.',
        ],
        4 => [
            0 => 'No existen indicadores de ningún tipo para verificar el cumplimiento de la política.',
            1 => 'Se mencionan métricas de forma informal, sin registrarlas ni darles seguimiento.',
            2 => 'Existen algunos indicadores definidos, pero no se recolectan de forma consistente.',
            3 => 'Hay indicadores documentados que se recolectan periódicamente para la mayoría de los procesos.',
            4 => 'Los indicadores se supervisan activamente y sirven de base para reportes formales de cumplimiento.',
            5 => 'Los indicadores alimentan un proceso de mejora continua, con metas y revisión de tendencias en el tiempo.',
        ],
        5 => [
            0 => 'La política, si existe, no menciona alcance ni objetivos de seguridad.',
            1 => 'El alcance o los objetivos se mencionan de forma vaga o solo verbalmente.',
            2 => 'Existe una definición parcial de alcance/objetivos, incompleta o desactualizada.',
            3 => 'La política define claramente su alcance y objetivos, y son conocidos por la organización.',
            4 => 'El alcance y los objetivos se revisan y validan periódicamente, con evidencia de dicha revisión.',
            5 => 'El alcance y los objetivos se ajustan de forma continua según el contexto de riesgo, con mejora documentada.',
        ],

        // ===================== C2 - Organizacion de la seguridad =====================
        6 => [
            0 => 'No hay ninguna asignación de responsabilidades o roles de seguridad en la organización.',
            1 => 'Algunas personas asumen responsabilidades de seguridad de manera informal, sin nombramiento oficial.',
            2 => 'Existen roles definidos en algún documento, pero no se aplican de forma consistente en la práctica.',
            3 => 'Los roles y responsabilidades de seguridad están documentados, asignados y comunicados a la mayoría de la organización.',
            4 => 'Las responsabilidades se supervisan periódicamente y hay evidencia de que se cumplen (reportes, bitácoras).',
            5 => 'Los roles se revisan y ajustan continuamente según la evolución de la organización, con métricas de desempeño.',
        ],
        7 => [
            0 => 'No existe ningún comité ni responsable asignado para la gobernanza de seguridad.',
            1 => 'Alguien atiende temas de seguridad de forma ocasional, sin mandato ni reuniones formales.',
            2 => 'Existe un responsable nombrado, pero sin comité formal ni reuniones periódicas documentadas.',
            3 => 'Hay un comité o responsable formal de gobernanza que se reúne con una periodicidad definida.',
            4 => 'El comité supervisa activamente los temas de seguridad y deja actas o minutas como evidencia.',
            5 => 'La gobernanza de seguridad se mide y mejora continuamente, con indicadores de gestión reportados a la alta dirección.',
        ],
        8 => [
            0 => 'No se realiza ninguna actividad de concienciación o formación en seguridad.',
            1 => 'Hay charlas o correos aislados sobre seguridad, sin plan ni periodicidad.',
            2 => 'Existe un plan de concienciación, pero se ejecuta de forma parcial o incompleta.',
            3 => 'Se imparte formación en seguridad de forma periódica a la mayoría del personal, con registro de asistencia.',
            4 => 'La formación se supervisa y se evalúa (pruebas, encuestas) con evidencia documentada de resultados.',
            5 => 'El programa de concienciación se mide y mejora continuamente según los resultados obtenidos (ej. simulacros de phishing).',
        ],
        9 => [
            0 => 'No se gestionan acuerdos ni responsabilidades de seguridad con terceros.',
            1 => 'Se mencionan responsabilidades de seguridad a terceros de forma verbal, sin contrato específico.',
            2 => 'Existen cláusulas de seguridad en algunos contratos, pero no de forma sistemática.',
            3 => 'Los acuerdos con terceros incluyen cláusulas de seguridad documentadas para la mayoría de los proveedores relevantes.',
            4 => 'El cumplimiento de las cláusulas se supervisa periódicamente, con evidencia de revisiones o auditorías a terceros.',
            5 => 'La gestión de terceros se mide y mejora continuamente, con criterios de riesgo actualizados por proveedor.',
        ],
        10 => [
            0 => 'No existen criterios definidos para aceptación o gestión de riesgos.',
            1 => 'Los riesgos se aceptan o rechazan de forma informal, según criterio individual.',
            2 => 'Existen criterios de aceptación de riesgo esbozados, pero no se aplican de forma consistente.',
            3 => 'Hay criterios documentados de aceptación de riesgo que se aplican en la mayoría de las decisiones.',
            4 => 'Los criterios se supervisan y las decisiones de riesgo quedan formalmente evidenciadas y aprobadas.',
            5 => 'Los criterios de aceptación de riesgo se revisan y ajustan continuamente según el apetito de riesgo de la organización.',
        ],

        // ===================== C3 - Inventario de activos =====================
        11 => [
            0 => 'No existe ningún inventario de activos de información.',
            1 => 'Hay listas parciales o informales de activos, hechas de manera ocasional.',
            2 => 'Existe un inventario, pero está incompleto o desactualizado respecto a los activos reales.',
            3 => 'El inventario está documentado, actualizado y cubre la mayoría de los activos de información relevantes.',
            4 => 'El inventario se supervisa y actualiza periódicamente, con evidencia de las revisiones realizadas.',
            5 => 'El inventario se mantiene y mejora de forma continua, integrado a procesos automatizados de descubrimiento de activos.',
        ],
        12 => [
            0 => 'Los activos no tienen ningún tipo de clasificación por criticidad o sensibilidad.',
            1 => 'Algunos activos se clasifican de manera informal, sin criterio documentado.',
            2 => 'Existe un esquema de clasificación, pero se aplica de forma inconsistente a los activos.',
            3 => 'La mayoría de los activos están clasificados según criticidad/sensibilidad, siguiendo un esquema documentado.',
            4 => 'La clasificación se supervisa periódicamente y hay evidencia de revisiones para mantenerla vigente.',
            5 => 'El esquema de clasificación se mide y mejora continuamente, ajustándose a nuevos tipos de activos o riesgos.',
        ],
        13 => [
            0 => 'No se registran propietarios ni custodios de los activos.',
            1 => 'Se conoce informalmente quién usa cada activo, pero no está documentado.',
            2 => 'Existen propietarios asignados para algunos activos, de forma incompleta.',
            3 => 'La mayoría de los activos tienen propietario y custodio formalmente documentados.',
            4 => 'La asignación de propietarios se supervisa y se actualiza con evidencia cuando hay cambios de personal.',
            5 => 'La gestión de propietarios/custodios se revisa y mejora continuamente como parte del ciclo de vida del activo.',
        ],
        14 => [
            0 => 'El inventario de activos nunca se revisa.',
            1 => 'Se revisa de forma esporádica, sin calendario definido.',
            2 => 'Existe intención de revisión periódica, pero no se cumple de forma consistente.',
            3 => 'El inventario se revisa con una periodicidad definida y documentada.',
            4 => 'Las revisiones se supervisan activamente y quedan evidencias formales de cada ciclo.',
            5 => 'El proceso de revisión está integrado en un ciclo de mejora continua, con métricas de exactitud del inventario.',
        ],
        15 => [
            0 => 'No existe ningún control para el manejo o etiquetado de soportes y activos.',
            1 => 'Algunos soportes se etiquetan de manera informal, sin criterio uniforme.',
            2 => 'Existen lineamientos de etiquetado/manejo, pero se aplican de forma parcial.',
            3 => 'Los soportes y activos se etiquetan y manejan siguiendo procedimientos documentados en la mayoría de los casos.',
            4 => 'El cumplimiento del etiquetado/manejo se supervisa periódicamente con evidencia de verificación.',
            5 => 'Los controles de manejo y etiquetado se revisan y mejoran continuamente según la experiencia operativa.',
        ],

        // ===================== C4 - Propiedad de activos =====================
        16 => [
            0 => 'No hay propietarios asignados a los activos ni responsabilidades definidas sobre ellos.',
            1 => 'Se asume informalmente quién es responsable de un activo, sin asignación documentada.',
            2 => 'Existen propietarios asignados para algunos activos, de forma incompleta o desactualizada.',
            3 => 'Los propietarios de activos y sus responsabilidades están claramente documentados para la mayoría de los casos.',
            4 => 'La asignación de propietarios se supervisa y se evidencia su cumplimiento (ej. firmas de aceptación).',
            5 => 'La gestión de propietarios se revisa y mejora continuamente, integrada al ciclo de vida completo del activo.',
        ],
        17 => [
            0 => 'No existe ningún proceso para la adquisición o baja de activos.',
            1 => 'La adquisición o baja de activos se realiza de manera ad hoc, sin procedimiento.',
            2 => 'Existe un procedimiento esbozado, pero no se sigue de forma consistente.',
            3 => 'Hay un proceso documentado de adquisición y baja de activos que se aplica en la mayoría de los casos.',
            4 => 'El proceso se supervisa y cada adquisición/baja queda registrada como evidencia.',
            5 => 'El proceso de adquisición y baja se mide y mejora continuamente, integrado a la gestión de inventario.',
        ],
        18 => [
            0 => 'No hay ningún control de acceso físico ni lógico sobre los activos críticos.',
            1 => 'Existen controles básicos e informales (ej. una llave compartida), sin política definida.',
            2 => 'Hay controles de acceso definidos, pero se aplican de forma inconsistente entre activos críticos.',
            3 => 'El acceso físico y lógico a los activos críticos está controlado siguiendo procedimientos documentados.',
            4 => 'Los controles de acceso se supervisan periódicamente, con registros de quién accede y cuándo.',
            5 => 'El control de acceso a activos críticos se revisa y mejora continuamente según el riesgo evaluado.',
        ],
        19 => [
            0 => 'No existe ningún registro de ubicación para activos móviles o portátiles.',
            1 => 'La ubicación se conoce de forma informal, sin registro documentado.',
            2 => 'Existe un registro parcial, que no cubre todos los activos móviles.',
            3 => 'Hay un registro documentado de ubicación que cubre la mayoría de los activos móviles y portátiles.',
            4 => 'El registro se supervisa y actualiza periódicamente, con evidencia de verificaciones físicas.',
            5 => 'El seguimiento de activos móviles se mejora continuamente, apoyado en herramientas de rastreo o gestión de dispositivos.',
        ],
        20 => [
            0 => 'No se realizan copias de respaldo de los activos críticos.',
            1 => 'Se hacen respaldos de forma manual y ocasional, sin política ni calendario.',
            2 => 'Existe una política de respaldo, pero no se cumple de forma consistente ni se prueba la restauración.',
            3 => 'Los respaldos se realizan según una política documentada, cubriendo la mayoría de los activos críticos.',
            4 => 'Los respaldos se supervisan, se prueban periódicamente y hay evidencia de restauraciones exitosas.',
            5 => 'La estrategia de respaldo se mide (tiempos de recuperación, cobertura) y mejora continuamente.',
        ],

        // ===================== C5 - Control de accesos logicos =====================
        21 => [
            0 => 'No se aplica ningún criterio de menor privilegio; los accesos son amplios o sin control.',
            1 => 'El principio de menor privilegio se aplica de forma esporádica, según criterio individual.',
            2 => 'Existe un lineamiento de menor privilegio, pero se aplica de forma inconsistente entre sistemas.',
            3 => 'Los accesos se otorgan siguiendo el principio de menor privilegio en la mayoría de los sistemas.',
            4 => 'La aplicación del menor privilegio se supervisa periódicamente, con evidencia de revisiones de accesos otorgados.',
            5 => 'El modelo de privilegios se revisa y ajusta continuamente según el uso real y el riesgo (ej. accesos basados en roles).',
        ],
        22 => [
            0 => 'No existe ningún procedimiento para gestionar el ciclo de vida de las cuentas de usuario.',
            1 => 'Las cuentas se crean o eliminan de manera informal, sin procedimiento ni registro.',
            2 => 'Existe un procedimiento esbozado, pero no se aplica de forma consistente (ej. cuentas huérfanas).',
            3 => 'El ciclo de vida de las cuentas (alta, cambio, baja) sigue un procedimiento documentado en la mayoría de los casos.',
            4 => 'El cumplimiento del procedimiento se supervisa, con evidencia de auditorías de cuentas activas e inactivas.',
            5 => 'La gestión del ciclo de vida se mide y mejora continuamente, idealmente automatizada e integrada con RR. HH.',
        ],
        23 => [
            0 => 'Los accesos otorgados nunca se auditan ni se revisan.',
            1 => 'Se revisan accesos de forma esporádica, sin calendario definido.',
            2 => 'Existe intención de revisión periódica de accesos, pero no se cumple de forma consistente.',
            3 => 'Los accesos se auditan y revisan con una periodicidad definida y documentada.',
            4 => 'Las revisiones de acceso se supervisan y generan evidencia formal (reportes firmados por responsables).',
            5 => 'La revisión de accesos se mide y mejora continuamente, con indicadores de accesos excesivos detectados y corregidos.',
        ],
        24 => [
            0 => 'No se exige ningún mecanismo de autenticación robusto; se usan contraseñas débiles o compartidas.',
            1 => 'Se recomienda el uso de contraseñas fuertes o 2FA de manera informal, sin exigirlo.',
            2 => 'Existe una política de autenticación, pero se aplica solo en algunos sistemas o de forma inconsistente.',
            3 => 'Los mecanismos de autenticación adecuados (2FA, contraseñas robustas) están implementados en la mayoría de los sistemas críticos.',
            4 => 'El cumplimiento de la política de autenticación se supervisa, con evidencia de auditorías de configuración.',
            5 => 'Los mecanismos de autenticación se revisan y mejoran continuamente según nuevas amenazas (ej. migración a MFA moderno).',
        ],
        25 => [
            0 => 'Las cuentas de servicio y accesos compartidos no tienen ningún control especial.',
            1 => 'Se gestionan de forma informal, con credenciales compartidas sin registro.',
            2 => 'Existen lineamientos para cuentas de servicio, pero se aplican de forma parcial.',
            3 => 'Las cuentas de servicio y accesos compartidos se gestionan siguiendo procedimientos documentados en la mayoría de los casos.',
            4 => 'La gestión de estas cuentas se supervisa periódicamente, con evidencia de revisiones y rotación de credenciales.',
            5 => 'La gestión de cuentas de servicio se mide y mejora continuamente, idealmente con bóvedas de credenciales.',
        ],

        // ===================== C6 - Gestion de privilegios =====================
        26 => [
            0 => 'Los privilegios nunca se actualizan cuando cambian roles o personas.',
            1 => 'Se ajustan de forma esporádica, cuando alguien lo solicita o lo nota.',
            2 => 'Existe intención de actualizar privilegios ante cambios, pero no se cumple de forma consistente.',
            3 => 'Los privilegios se revisan y actualizan siguiendo un procedimiento documentado ante la mayoría de los cambios de rol.',
            4 => 'La actualización de privilegios se supervisa, con evidencia de que los cambios se reflejan oportunamente.',
            5 => 'La gestión de privilegios ante cambios se mide y mejora continuamente, idealmente integrada al proceso de RR. HH.',
        ],
        27 => [
            0 => 'No existe ningún procedimiento para aprobar o registrar privilegios elevados.',
            1 => 'Los privilegios elevados se otorgan de manera informal, sin aprobación documentada.',
            2 => 'Existe un procedimiento de aprobación, pero se aplica de forma inconsistente.',
            3 => 'Los privilegios elevados se aprueban y registran siguiendo un procedimiento documentado en la mayoría de los casos.',
            4 => 'El procedimiento se supervisa, con evidencia de aprobaciones formales para cada privilegio elevado otorgado.',
            5 => 'El proceso de aprobación de privilegios elevados se mide y mejora continuamente (ej. accesos privilegiados por tiempo limitado).',
        ],
        28 => [
            0 => 'No se monitorean las actividades de cuentas con privilegios altos.',
            1 => 'El monitoreo es ocasional y manual, sin herramientas ni registro sistemático.',
            2 => 'Existe algún monitoreo, pero no cubre todas las cuentas privilegiadas ni se revisa con regularidad.',
            3 => 'Las actividades de cuentas privilegiadas se monitorean siguiendo un procedimiento documentado para la mayoría de los sistemas.',
            4 => 'El monitoreo se supervisa activamente y genera alertas o reportes con evidencia de seguimiento.',
            5 => 'El monitoreo de privilegios se mide y mejora continuamente, idealmente con analítica de comportamiento y alertas automatizadas.',
        ],
        29 => [
            0 => 'No existen controles para prevenir el abuso de privilegios.',
            1 => 'Se confía en la buena fe del personal, sin controles técnicos definidos.',
            2 => 'Existen controles esbozados, pero se aplican de forma parcial o inconsistente.',
            3 => 'Hay controles documentados para prevenir el abuso de privilegios, aplicados en la mayoría de los sistemas.',
            4 => 'Los controles se supervisan periódicamente, con evidencia de incidentes detectados y gestionados.',
            5 => 'Los controles contra el abuso de privilegios se revisan y mejoran continuamente según el riesgo observado.',
        ],
        30 => [
            0 => 'No existe separación de funciones críticas; una sola persona puede realizar todo el proceso.',
            1 => 'La separación de funciones se aplica de forma informal, dependiendo de quién esté disponible.',
            2 => 'Existe un esquema de separación de funciones, pero con excepciones no controladas.',
            3 => 'Las funciones críticas están separadas siguiendo un esquema documentado en la mayoría de los procesos.',
            4 => 'La separación de funciones se supervisa, con evidencia de revisiones para detectar conflictos de interés.',
            5 => 'El esquema de separación de funciones se revisa y mejora continuamente conforme cambia la estructura organizacional.',
        ],

        // ===================== C7 - Criptografia =====================
        31 => [
            0 => 'No se usan algoritmos criptográficos aprobados; se usan por defecto o sin criterio.',
            1 => 'Se eligen algoritmos de forma informal, sin lineamientos de la organización.',
            2 => 'Existe un lineamiento sobre algoritmos aprobados, pero se aplica de forma inconsistente.',
            3 => 'Los sistemas usan algoritmos y parámetros criptográficos aprobados por la organización en la mayoría de los casos.',
            4 => 'El uso de algoritmos aprobados se supervisa, con evidencia de revisiones de configuración criptográfica.',
            5 => 'Los estándares criptográficos se revisan y actualizan continuamente conforme evolucionan las buenas prácticas del sector.',
        ],
        32 => [
            0 => 'Las claves criptográficas no tienen ninguna gestión ni protección definida.',
            1 => 'La gestión de claves es informal e inconsistente entre equipos o sistemas.',
            2 => 'Existe algún lineamiento de gestión de claves, pero sin un proceso centralizado ni ciclo de vida definido.',
            3 => 'Las claves se gestionan y protegen siguiendo un proceso documentado para la mayoría de los sistemas.',
            4 => 'La gestión de claves se supervisa periódicamente, con evidencia de rotación y control de acceso a ellas.',
            5 => 'Las claves se gestionan de forma centralizada con ciclo de vida completo (ej. HSM), medida y mejorada continuamente.',
        ],
        33 => [
            0 => 'No se aplica cifrado a datos sensibles en reposo, ni siquiera cuando sería necesario.',
            1 => 'El cifrado en reposo se aplica de forma puntual y a criterio individual.',
            2 => 'Existe un lineamiento sobre cifrado en reposo, pero se aplica de forma parcial.',
            3 => 'Los datos sensibles en reposo se cifran siguiendo un procedimiento documentado en la mayoría de los sistemas.',
            4 => 'La aplicación del cifrado se supervisa, con evidencia de verificaciones sobre qué datos están cifrados.',
            5 => 'La protección de datos en reposo se revisa y mejora continuamente según la clasificación de la información.',
        ],
        34 => [
            0 => 'La información confidencial en los backups no se protege con criptografía.',
            1 => 'Algunos backups se cifran de forma puntual, sin criterio uniforme.',
            2 => 'Existe un lineamiento de cifrado de backups, pero se aplica de forma inconsistente.',
            3 => 'Los backups con información confidencial se cifran siguiendo un procedimiento documentado en la mayoría de los casos.',
            4 => 'El cifrado de backups se supervisa periódicamente, con evidencia de verificación.',
            5 => 'La protección criptográfica de los backups se mide y mejora continuamente, integrada a la política de respaldo.',
        ],
        35 => [
            0 => 'Las políticas de uso de criptografía nunca se revisan.',
            1 => 'Se revisan de forma esporádica, sin calendario definido.',
            2 => 'Existe intención de revisión periódica, pero no se cumple de forma consistente.',
            3 => 'Las políticas de criptografía se revisan con una periodicidad definida y documentada.',
            4 => 'Las revisiones se supervisan y quedan evidencias formales de cada actualización.',
            5 => 'Las políticas de criptografía se revisan y mejoran de forma continua conforme evolucionan los estándares del sector.',
        ],

        // ===================== C8 - Seguridad en comunicaciones =====================
        36 => [
            0 => 'Los canales que transportan información sensible no se cifran ni se aseguran.',
            1 => 'El cifrado de canales se aplica de forma puntual, sin criterio definido.',
            2 => 'Existe un lineamiento de cifrado de comunicaciones, pero se aplica de forma inconsistente.',
            3 => 'Los canales que transportan información sensible se cifran o aseguran siguiendo un procedimiento documentado.',
            4 => 'La aplicación del cifrado en comunicaciones se supervisa, con evidencia de verificaciones periódicas.',
            5 => 'La seguridad de las comunicaciones se mide y mejora continuamente conforme evolucionan las amenazas.',
        ],
        37 => [
            0 => 'No se usan VPN ni canales seguros para accesos remotos.',
            1 => 'Se usan canales seguros de forma ocasional, según criterio individual.',
            2 => 'Existe un lineamiento de acceso remoto seguro, pero se aplica de forma inconsistente.',
            3 => 'Los accesos remotos se realizan mediante VPN o canales seguros siguiendo un procedimiento documentado.',
            4 => 'El uso de canales seguros se supervisa, con evidencia de auditorías de accesos remotos.',
            5 => 'El acceso remoto seguro se mide y mejora continuamente (ej. zero trust, monitoreo continuo de sesiones).',
        ],
        38 => [
            0 => 'No se controlan ni registran las conexiones entrantes y salientes críticas.',
            1 => 'El control de conexiones es informal, sin registro sistemático.',
            2 => 'Existen controles básicos (ej. firewall), pero sin revisión periódica de los registros.',
            3 => 'Las conexiones críticas se controlan y registran siguiendo un procedimiento documentado en la mayoría de los casos.',
            4 => 'El control de conexiones se supervisa activamente, con evidencia de revisión de bitácoras y alertas.',
            5 => 'El monitoreo de conexiones se mide y mejora continuamente, con analítica y respuesta automatizada ante anomalías.',
        ],
        39 => [
            0 => 'No existen políticas para el uso seguro de correo o transferencia de archivos.',
            1 => 'Se dan recomendaciones informales sobre correo o transferencia de archivos, sin política formal.',
            2 => 'Existe una política, pero se aplica de forma parcial o desconocida por el personal.',
            3 => 'Hay políticas documentadas para uso seguro de correo y transferencia de archivos, aplicadas en la mayoría de los casos.',
            4 => 'El cumplimiento de estas políticas se supervisa, con evidencia de controles técnicos (ej. filtros, DLP).',
            5 => 'Las políticas de uso seguro se revisan y mejoran continuamente según incidentes o nuevas amenazas detectadas.',
        ],
        40 => [
            0 => 'No se gestionan acuerdos de seguridad con proveedores de servicios de comunicaciones.',
            1 => 'Se confía en el proveedor sin ningún acuerdo de seguridad específico.',
            2 => 'Existen acuerdos generales, pero sin cláusulas de seguridad específicas ni seguimiento.',
            3 => 'Los acuerdos con proveedores de comunicaciones incluyen cláusulas de seguridad documentadas.',
            4 => 'El cumplimiento de los acuerdos se supervisa periódicamente, con evidencia de revisiones al proveedor.',
            5 => 'La gestión de proveedores de comunicaciones se mide y mejora continuamente según el riesgo asociado.',
        ],

        // ===================== C9 - Seguridad en operaciones =====================
        41 => [
            0 => 'No existen procedimientos operativos documentados.',
            1 => 'Los procedimientos existen solo en el conocimiento informal del personal.',
            2 => 'Existen algunos procedimientos documentados, pero desactualizados o poco conocidos.',
            3 => 'Los procedimientos operativos están documentados, actualizados y son conocidos por la mayoría del personal involucrado.',
            4 => 'El cumplimiento de los procedimientos se supervisa, con evidencia de auditorías o revisiones internas.',
            5 => 'Los procedimientos operativos se revisan y mejoran de forma continua según el desempeño observado.',
        ],
        42 => [
            0 => 'No existe ningún control para cambios en producción.',
            1 => 'Los cambios se realizan de forma ad hoc, sin aprobación ni registro.',
            2 => 'Existe un proceso de change management esbozado, pero no se sigue de forma consistente.',
            3 => 'Los cambios en producción siguen un proceso documentado de solicitud, prueba y aprobación en la mayoría de los casos.',
            4 => 'El proceso de cambios se supervisa, con evidencia de aprobaciones y pruebas antes del despliegue.',
            5 => 'La gestión de cambios se mide y mejora continuamente (ej. tasa de cambios fallidos, tiempo de despliegue).',
        ],
        43 => [
            0 => 'No se realiza ningún monitoreo ni registro de eventos operativos.',
            1 => 'El monitoreo es manual y ocasional, sin herramientas dedicadas.',
            2 => 'Existe monitoreo parcial, que no cubre todos los sistemas relevantes.',
            3 => 'Los eventos operativos relevantes se monitorean y registran siguiendo un procedimiento documentado.',
            4 => 'El monitoreo se supervisa activamente, generando alertas y reportes con evidencia de seguimiento.',
            5 => 'El monitoreo de eventos se mide y mejora continuamente, idealmente con correlación automatizada de eventos (SIEM).',
        ],
        44 => [
            0 => 'No se gestionan parches ni vulnerabilidades en los sistemas críticos.',
            1 => 'Los parches se aplican de forma esporádica, sin calendario ni evaluación de vulnerabilidades.',
            2 => 'Existe un proceso de gestión de parches, pero no se cumple de forma consistente en todos los sistemas.',
            3 => 'Los parches y vulnerabilidades se gestionan siguiendo un procedimiento documentado para la mayoría de los sistemas críticos.',
            4 => 'La gestión de parches se supervisa, con evidencia de escaneos de vulnerabilidades y tiempos de remediación.',
            5 => 'La gestión de vulnerabilidades se mide y mejora continuamente, con métricas de tiempo de exposición y remediación.',
        ],
        45 => [
            0 => 'No se realizan revisiones de configuración ni de seguridad.',
            1 => 'Las revisiones son esporádicas y sin criterio definido.',
            2 => 'Existe intención de revisar configuraciones periódicamente, pero no se cumple de forma consistente.',
            3 => 'Las revisiones de configuración y seguridad se realizan con una periodicidad definida y documentada.',
            4 => 'Las revisiones se supervisan activamente y generan evidencia formal de hallazgos y correcciones.',
            5 => 'Las revisiones de configuración se miden y mejoran continuamente, idealmente con verificación automatizada de líneas base.',
        ],

        // ===================== C10 - Gestion de incidentes =====================
        46 => [
            0 => 'No existe ningún proceso para gestionar incidentes de seguridad; la respuesta es reactiva y no planificada.',
            1 => 'Los incidentes se atienden de manera informal, según quien esté disponible en el momento.',
            2 => 'Existe un proceso esbozado de gestión de incidentes, pero no se aplica de forma consistente.',
            3 => 'Hay un proceso formal de gestión de incidentes, documentado y aplicado en la mayoría de los casos.',
            4 => 'El proceso se supervisa activamente, con monitoreo y evidencia de tiempos de respuesta medidos.',
            5 => 'La gestión de incidentes está automatizada y en mejora continua, con capacidades predictivas y de respuesta orquestada.',
        ],
        47 => [
            0 => 'Los incidentes no se registran ni se clasifican de ninguna forma.',
            1 => 'Se anotan de manera informal (correo, chat), sin clasificación ni seguimiento estructurado.',
            2 => 'Existe un registro de incidentes, pero incompleto o sin clasificación consistente.',
            3 => 'Los incidentes se registran, clasifican y se les da seguimiento siguiendo un procedimiento documentado.',
            4 => 'El registro y clasificación se supervisan, con evidencia de reportes periódicos de estado.',
            5 => 'El registro de incidentes se mide y mejora continuamente, con métricas de tiempo de detección, respuesta y cierre.',
        ],
        48 => [
            0 => 'No se realizan pruebas ni ejercicios de respuesta a incidentes.',
            1 => 'Se han hecho pruebas puntuales, sin planificación ni periodicidad.',
            2 => 'Existe intención de realizar ejercicios periódicos, pero no se cumple de forma consistente.',
            3 => 'Se realizan pruebas o simulacros de respuesta a incidentes con una periodicidad definida y documentada.',
            4 => 'Los ejercicios se supervisan y generan evidencia de lecciones aprendidas incorporadas al plan.',
            5 => 'Los ejercicios de respuesta se miden y mejoran continuamente, escalando en complejidad y realismo con el tiempo.',
        ],
        49 => [
            0 => 'No existen canales definidos para reportar incidentes de seguridad.',
            1 => 'Los incidentes se reportan de manera informal, a quien se tenga a mano.',
            2 => 'Existe un canal de reporte, pero es poco conocido o usado de forma inconsistente.',
            3 => 'Hay canales formales y conocidos para reportar incidentes, usados en la mayoría de los casos.',
            4 => 'El uso de los canales se supervisa, con evidencia de trazabilidad de reportes recibidos y atendidos.',
            5 => 'Los canales de reporte se miden y mejoran continuamente (ej. tiempos de respuesta, satisfacción de quien reporta).',
        ],
        50 => [
            0 => 'No hay ninguna retroalimentación a la organización tras los incidentes.',
            1 => 'Se comentan lecciones aprendidas de manera informal, entre pocas personas.',
            2 => 'Existen informes posteriores a incidentes, pero no se comunican ni aplican de forma consistente.',
            3 => 'Se documentan lecciones aprendidas y se comunican a las áreas relevantes tras la mayoría de los incidentes.',
            4 => 'La aplicación de lecciones aprendidas se supervisa, con evidencia de mejoras implementadas a partir de ellas.',
            5 => 'La retroalimentación post-incidente alimenta un ciclo de mejora continua medido (reducción de incidentes recurrentes).',
        ],

        // ===================== C11 - Control fisico =====================
        51 => [
            0 => 'No existe ningún control de acceso físico a instalaciones críticas.',
            1 => 'Hay controles básicos e informales (ej. una llave compartida), sin autorización formal.',
            2 => 'Existe un control de acceso físico, pero sin autorización formal documentada ni registro consistente.',
            3 => 'El acceso a instalaciones críticas requiere autorización formal documentada, siguiendo el procedimiento en la mayoría de los casos.',
            4 => 'El acceso físico se supervisa, con revisión periódica (ej. trimestral) de quién tiene acceso autorizado.',
            5 => 'El control de acceso físico se mide y mejora continuamente, integrado a sistemas de control electrónico y auditoría.',
        ],
        52 => [
            0 => 'No existen medidas de protección física para equipos ni servidores.',
            1 => 'Hay medidas mínimas e informales (ej. puerta con llave), sin criterio definido.',
            2 => 'Existen medidas de protección, pero incompletas o aplicadas de forma inconsistente.',
            3 => 'Los equipos y servidores cuentan con medidas de protección física documentadas, aplicadas en la mayoría de los casos.',
            4 => 'Las medidas de protección se supervisan periódicamente, con evidencia de inspecciones.',
            5 => 'La protección física de equipos se revisa y mejora continuamente según amenazas o incidentes detectados.',
        ],
        53 => [
            0 => 'No se gestionan visitantes ni accesos temporales de ninguna forma.',
            1 => 'Los visitantes ingresan sin registro ni acompañamiento formal.',
            2 => 'Existe un registro de visitantes, pero incompleto o sin control de acompañamiento.',
            3 => 'Los visitantes y accesos temporales se gestionan siguiendo un procedimiento documentado (registro, acompañamiento, gafete).',
            4 => 'La gestión de visitantes se supervisa, con evidencia de registros revisados periódicamente.',
            5 => 'La gestión de visitantes se mide y mejora continuamente, idealmente con control electrónico de accesos temporales.',
        ],
        54 => [
            0 => 'No se realiza ninguna inspección ni mantenimiento de los controles físicos.',
            1 => 'Las inspecciones se hacen de forma esporádica, solo cuando algo falla.',
            2 => 'Existe intención de inspeccionar periódicamente, pero no se cumple de forma consistente.',
            3 => 'Los controles físicos se inspeccionan y mantienen con una periodicidad definida y documentada.',
            4 => 'Las inspecciones se supervisan activamente, con evidencia formal de cada mantenimiento realizado.',
            5 => 'El mantenimiento de controles físicos se mide y mejora continuamente según el historial de fallas.',
        ],
        55 => [
            0 => 'No existen controles de redundancia ni recuperación física para equipos críticos.',
            1 => 'Hay alguna medida de respaldo física (ej. UPS básico), sin plan formal.',
            2 => 'Existen medidas de redundancia parciales, no documentadas ni probadas.',
            3 => 'Los equipos críticos cuentan con controles de redundancia y recuperación física documentados.',
            4 => 'La redundancia se supervisa y se prueba periódicamente, con evidencia de los resultados.',
            5 => 'Los controles de redundancia física se miden y mejoran continuamente, integrados al plan de continuidad del negocio.',
        ],

        // ===================== C12 - Proteccion ambiental =====================
        56 => [
            0 => 'No se adoptan medidas ambientales (temperatura, humedad) para proteger los activos.',
            1 => 'Hay medidas informales y puntuales (ej. un ventilador), sin monitoreo.',
            2 => 'Existen medidas ambientales básicas, pero sin monitoreo ni umbrales definidos.',
            3 => 'Se aplican medidas ambientales documentadas, con umbrales de temperatura/humedad definidos y monitoreados.',
            4 => 'Las condiciones ambientales se supervisan activamente, con alertas y evidencia de seguimiento.',
            5 => 'El control ambiental se mide y mejora continuamente, integrado a sistemas de monitoreo automatizado.',
        ],
        57 => [
            0 => 'No existen sistemas contra incendios ni de detección adecuados.',
            1 => 'Hay extintores básicos, sin sistema de detección ni mantenimiento programado.',
            2 => 'Existen sistemas de detección/incendio, pero no se revisan ni prueban con regularidad.',
            3 => 'Se cuenta con sistemas contra incendios y detección documentados, con mantenimiento periódico.',
            4 => 'Los sistemas se supervisan activamente, con evidencia de pruebas y certificaciones vigentes.',
            5 => 'La protección contra incendios se mide y mejora continuamente, alineada a estándares de la industria.',
        ],
        58 => [
            0 => 'No se protege la infraestructura eléctrica ni los suministros críticos.',
            1 => 'Hay protección eléctrica básica (ej. regulador), sin redundancia ni plan.',
            2 => 'Existen medidas de protección eléctrica, pero sin redundancia completa ni pruebas periódicas.',
            3 => 'La infraestructura eléctrica cuenta con protección documentada (UPS, planta eléctrica) para los suministros críticos.',
            4 => 'La protección eléctrica se supervisa periódicamente, con evidencia de pruebas de los sistemas de respaldo.',
            5 => 'La infraestructura eléctrica se mide y mejora continuamente, con monitoreo en tiempo real de su disponibilidad.',
        ],
        59 => [
            0 => 'No se realizan evaluaciones de riesgo físico ni ambiental.',
            1 => 'Se identifican riesgos de forma informal, sin evaluación documentada.',
            2 => 'Existe intención de evaluar riesgos físicos/ambientales periódicamente, pero no se cumple de forma consistente.',
            3 => 'Las evaluaciones de riesgo físico y ambiental se realizan con una periodicidad definida y documentada.',
            4 => 'Las evaluaciones se supervisan activamente, con evidencia formal de hallazgos y planes de acción.',
            5 => 'Las evaluaciones de riesgo físico/ambiental se miden y mejoran continuamente, integradas a la gestión de riesgos general.',
        ],
        60 => [
            0 => 'No existen planes de contingencia ante fallas ambientales.',
            1 => 'Hay respuestas improvisadas ante fallas ambientales, sin plan documentado.',
            2 => 'Existe un plan esbozado, pero no probado ni actualizado.',
            3 => 'Hay planes de contingencia documentados ante fallas ambientales, conocidos por el personal responsable.',
            4 => 'Los planes se supervisan y prueban periódicamente, con evidencia de los resultados de las pruebas.',
            5 => 'Los planes de contingencia ambiental se miden y mejoran continuamente tras cada prueba o evento real.',
        ],

        // ===================== C13 - Evaluacion de riesgos =====================
        61 => [
            0 => 'No se realiza ninguna identificación ni evaluación de riesgos.',
            1 => 'Los riesgos se identifican de manera informal y ocasional.',
            2 => 'Existe un proceso de identificación de riesgos, pero no se aplica de forma periódica ni consistente.',
            3 => 'Se realiza identificación y evaluación de riesgos con una periodicidad definida, siguiendo un proceso documentado (ej. ciclo PDCA).',
            4 => 'El proceso de evaluación de riesgos se supervisa, con evidencia formal de cada ciclo realizado.',
            5 => 'La identificación y evaluación de riesgos se mide y mejora continuamente, integrada a la gestión estratégica.',
        ],
        62 => [
            0 => 'No se documentan tratamientos ni planes de mitigación de riesgos.',
            1 => 'Se atienden riesgos de forma reactiva, sin plan documentado.',
            2 => 'Existen algunos planes de tratamiento, pero incompletos o sin seguimiento.',
            3 => 'Los tratamientos y planes de mitigación de riesgos están documentados y se aplican en la mayoría de los casos.',
            4 => 'La ejecución de los planes se supervisa, con evidencia del avance de cada medida de mitigación.',
            5 => 'Los planes de tratamiento se miden y mejoran continuamente según su efectividad real en reducir el riesgo.',
        ],
        63 => [
            0 => 'Los riesgos no se priorizan de ninguna forma.',
            1 => 'La priorización es informal, basada en percepción individual.',
            2 => 'Existe un criterio de priorización esbozado (impacto/probabilidad), pero se aplica de forma inconsistente.',
            3 => 'Los riesgos se priorizan según impacto y probabilidad siguiendo una metodología documentada.',
            4 => 'La priorización se supervisa periódicamente, con evidencia de matrices de riesgo actualizadas.',
            5 => 'La metodología de priorización se revisa y mejora continuamente según la experiencia y nuevos escenarios de riesgo.',
        ],
        64 => [
            0 => 'Los riesgos nunca se revisan tras cambios significativos en la organización.',
            1 => 'Se revisan de forma esporádica, solo si alguien lo recuerda.',
            2 => 'Existe intención de revisar riesgos tras cambios, pero no se cumple de forma consistente.',
            3 => 'Los riesgos se revisan siguiendo un procedimiento documentado cada vez que ocurre un cambio significativo.',
            4 => 'Estas revisiones se supervisan, con evidencia formal de que se realizaron tras cada cambio relevante.',
            5 => 'La revisión de riesgos ante cambios se mide y mejora continuamente, integrada a la gestión del cambio organizacional.',
        ],
        65 => [
            0 => 'Los riesgos no se comunican a la alta dirección ni a partes interesadas.',
            1 => 'Se mencionan riesgos de forma informal en conversaciones puntuales.',
            2 => 'Existe algún reporte de riesgos, pero no llega de forma consistente a la alta dirección.',
            3 => 'Los riesgos se comunican formalmente a la alta dirección y partes interesadas con una periodicidad definida.',
            4 => 'La comunicación de riesgos se supervisa, con evidencia de actas o reportes revisados por la dirección.',
            5 => 'La comunicación de riesgos se mide y mejora continuamente, con retroalimentación de la dirección incorporada al proceso.',
        ],

        // ===================== C14 - Cumplimiento legal =====================
        66 => [
            0 => 'No se identifican los requisitos legales o regulatorios aplicables a la organización.',
            1 => 'Se conocen algunos requisitos de forma informal, sin documentarlos.',
            2 => 'Existe una identificación parcial de requisitos legales, incompleta o desactualizada.',
            3 => 'Los requisitos legales y regulatorios aplicables están identificados y documentados de forma actualizada.',
            4 => 'La identificación de requisitos se supervisa periódicamente, con evidencia de revisiones legales.',
            5 => 'La identificación de requisitos legales se mide y mejora continuamente, con vigilancia activa de cambios normativos.',
        ],
        67 => [
            0 => 'No se gestionan contratos ni obligaciones de seguridad con proveedores.',
            1 => 'Se mencionan obligaciones de forma verbal, sin cláusulas contractuales.',
            2 => 'Existen cláusulas de seguridad en algunos contratos, sin revisión sistemática.',
            3 => 'Los contratos con proveedores incluyen cláusulas de seguridad documentadas en la mayoría de los casos.',
            4 => 'El cumplimiento de las obligaciones contractuales se supervisa, con evidencia de revisiones periódicas.',
            5 => 'La gestión contractual de proveedores se mide y mejora continuamente según el riesgo legal asociado.',
        ],
        68 => [
            0 => 'No se mantiene ninguna evidencia de cumplimiento ni registros necesarios.',
            1 => 'Se guardan algunos documentos de forma desorganizada, sin criterio de retención.',
            2 => 'Existe un archivo de evidencias, pero incompleto respecto a lo requerido.',
            3 => 'Se mantiene evidencia de cumplimiento y registros necesarios de forma organizada para la mayoría de los requisitos.',
            4 => 'La gestión de evidencias se supervisa, con revisiones periódicas de completitud y vigencia.',
            5 => 'La gestión de evidencias de cumplimiento se mide y mejora continuamente, con repositorios auditables y trazables.',
        ],
        69 => [
            0 => 'No se realizan auditorías para verificar el cumplimiento legal.',
            1 => 'Se hacen revisiones informales y ocasionales, sin metodología definida.',
            2 => 'Existe intención de auditar el cumplimiento legal, pero no se cumple de forma consistente.',
            3 => 'Se realizan auditorías de cumplimiento legal con una periodicidad definida y documentada.',
            4 => 'Las auditorías se supervisan, con evidencia formal de hallazgos y planes de corrección.',
            5 => 'Las auditorías de cumplimiento legal se miden y mejoran continuamente, con seguimiento de la resolución de hallazgos.',
        ],
        70 => [
            0 => 'No se evalúa el impacto de cambios regulatorios en la organización.',
            1 => 'El impacto se analiza de forma informal cuando alguien se entera de un cambio.',
            2 => 'Existe algún proceso de evaluación, pero no se aplica de forma sistemática a todos los cambios.',
            3 => 'El impacto de los cambios regulatorios se evalúa siguiendo un procedimiento documentado en la mayoría de los casos.',
            4 => 'La evaluación de impacto se supervisa, con evidencia de planes de adecuación ante cada cambio relevante.',
            5 => 'La evaluación de impacto regulatorio se mide y mejora continuamente, con vigilancia proactiva de cambios normativos.',
        ],

        // ===================== C15 - Auditoria y revision =====================
        71 => [
            0 => 'No se realizan auditorías internas de seguridad de la información.',
            1 => 'Se hacen revisiones informales y ocasionales, sin metodología ni calendario.',
            2 => 'Existe intención de auditar periódicamente, pero no se cumple de forma consistente.',
            3 => 'Se realizan auditorías internas de seguridad con una periodicidad definida y documentada.',
            4 => 'Las auditorías se supervisan activamente, con evidencia formal de su ejecución y alcance.',
            5 => 'El programa de auditoría interna se mide y mejora continuamente, ampliando su alcance según el riesgo.',
        ],
        72 => [
            0 => 'Los hallazgos de auditoría no se documentan ni se les da seguimiento.',
            1 => 'Se anotan hallazgos de forma informal, sin plan de acción.',
            2 => 'Existen hallazgos documentados, pero el seguimiento de las acciones correctivas es incompleto.',
            3 => 'Los hallazgos y sus acciones correctivas se documentan y se les da seguimiento siguiendo un procedimiento definido.',
            4 => 'El seguimiento de acciones correctivas se supervisa, con evidencia del estado de cada hallazgo.',
            5 => 'El seguimiento de hallazgos se mide y mejora continuamente, con indicadores de tiempo de cierre y recurrencia.',
        ],
        73 => [
            0 => 'Los controles y resultados de auditoría nunca se revisan con la dirección.',
            1 => 'Se comentan resultados de forma informal, sin reunión ni acta.',
            2 => 'Existen reuniones de revisión, pero sin periodicidad definida ni actas formales.',
            3 => 'Los controles y resultados de auditoría se revisan con la dirección siguiendo una periodicidad definida.',
            4 => 'Estas revisiones se supervisan y quedan formalmente documentadas (actas, acuerdos, responsables).',
            5 => 'La revisión con la dirección se mide y mejora continuamente, integrada a la toma de decisiones estratégicas.',
        ],
        74 => [
            0 => 'No se mantienen registros de auditoría ni evidencias suficientes.',
            1 => 'Se guardan algunos registros de forma desorganizada, sin criterio de retención.',
            2 => 'Existen registros de auditoría, pero incompletos o de difícil acceso.',
            3 => 'Se mantienen registros de auditoría y evidencias suficientes de forma organizada para la mayoría de los ciclos.',
            4 => 'La gestión de registros se supervisa, con revisiones periódicas de completitud y trazabilidad.',
            5 => 'La gestión de registros de auditoría se mide y mejora continuamente, con repositorios auditables a largo plazo.',
        ],
        75 => [
            0 => 'No se evalúa la eficacia de las acciones tomadas tras las auditorías.',
            1 => 'Se asume informalmente que las acciones funcionaron, sin verificación.',
            2 => 'Existe intención de evaluar la eficacia, pero no se hace de forma consistente.',
            3 => 'La eficacia de las acciones correctivas se evalúa siguiendo un procedimiento documentado en la mayoría de los casos.',
            4 => 'La evaluación de eficacia se supervisa, con evidencia formal de los resultados obtenidos.',
            5 => 'La eficacia de las acciones se mide y mejora continuamente, retroalimentando el ciclo de auditoría y gestión de riesgos.',
        ],
    ];
}
