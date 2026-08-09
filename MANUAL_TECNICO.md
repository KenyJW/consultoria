# Manual Técnico
## Sistema de Auditorías ISO/IEC 27002 — DataSolutions CR

---

## 1. Descripción general

Aplicación web MVC desarrollada en **PHP 8** con **MySQL** para gestionar auditorías de seguridad de bases de datos basadas en el estándar ISO/IEC 27002. Incluye cálculo automático de niveles de madurez y exposición al riesgo por dimensión (Confidencialidad, Integridad, Disponibilidad).

---

## 2. Requisitos del sistema

| Componente | Versión mínima |
|---|---|
| PHP | 8.1 |
| MySQL / MariaDB | 8.0 / 10.6 |
| Extensiones PHP | `pdo`, `pdo_mysql`, `mbstring`, `fileinfo` |
| Servidor web | Apache (con `mod_rewrite`) o PHP built-in server |

---

## 3. Instalación

### 3.1 Clonar o copiar el proyecto

Colocar la carpeta del proyecto en el directorio raíz del servidor web (ej. `C:\wamp64\www\consultora` en WAMP).

### 3.2 Crear la base de datos

Ejecutar el script SQL principal en MySQL:

```bash
mysql -u root -p < sql/schema.sql
```

O desde phpMyAdmin: importar el archivo `sql/schema.sql`.

Esto crea la base de datos `consultora_iso27002` con todas las tablas y datos iniciales (dominios, controles, preguntas y usuario administrador).

### 3.3 Configurar la conexión a la base de datos

Editar `config/database.php` o definir variables de entorno:

```php
// config/database.php
return [
    'host'     => getenv('DB_HOST')     ?: '127.0.0.1',
    'port'     => (int)(getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_DATABASE') ?: 'consultora_iso27002',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset'  => 'utf8mb4',
];
```

Variables de entorno disponibles:

| Variable | Descripción | Default |
|---|---|---|
| `DB_HOST` | Host de MySQL | `127.0.0.1` |
| `DB_PORT` | Puerto de MySQL | `3306` |
| `DB_DATABASE` | Nombre de la base de datos | `consultora_iso27002` |
| `DB_USERNAME` | Usuario de MySQL | `root` |
| `DB_PASSWORD` | Contraseña de MySQL | _(vacío)_ |
| `APP_URL` | URL base de la aplicación | _(auto-detectado)_ |
| `INITIAL_ADMIN_PASSWORD` | Contraseña inicial del admin | `Admin123*` |

### 3.4 Permisos de escritura

El directorio de evidencias debe tener permisos de escritura:

```bash
# Linux/macOS
chmod -R 775 public/uploads/evidences
```

En Windows con WAMP, verificar que el usuario del servicio Apache tenga permisos de escritura sobre esas carpetas.

### 3.5 Iniciar el servidor

**Opción A — WAMP/XAMPP (la que usa todo el equipo, sin configuración extra):**
Coloca la carpeta del proyecto dentro de `www/` (el nombre de la carpeta no
importa) e inicia WAMP normalmente. Entra directo a
`http://localhost/<nombre-de-tu-carpeta>/login` — **no hace falta escribir
`/public`** ni crear ningún VirtualHost: el `.htaccess` de la raíz y el
router de la aplicación (`app/core/Router.php`) calculan la ruta base solos
a partir de dónde vive el proyecto. Esto es justamente lo que hace portable
al proyecto entre las máquinas del equipo — cada quien lo puede tener en una
ruta distinta y funciona igual, sin tocar nada fuera del propio repositorio.

Si por alguna razón el rewrite no quedara activo, la ruta larga de
respaldo sigue funcionando: `http://localhost/<carpeta>/public/login`.

**Opción B — PHP built-in server (desarrollo rápido, sin Apache):**
```bash
php -S localhost:8000 -t public
```

**Opción C — VirtualHost Apache (opcional, avanzado):**
Solo si de verdad quieres un dominio local propio (ej. `consultora.local`)
en lugar de `localhost/<carpeta>`. Esto **no es necesario** para correr el
proyecto y, a diferencia de la Opción A, cada persona del equipo tendría que
configurarlo por separado en su propia máquina (editar su `hosts` y su
`httpd-vhosts.conf`, con permisos de administrador) — esos archivos viven
fuera del repositorio, así que `git pull` nunca los trae. No lo uses a menos
que sepas por qué lo necesitas.
```apache
<VirtualHost *:80>
    ServerName consultora.local
    DocumentRoot "C:/wamp64/www/<carpeta-del-proyecto>/public"
    <Directory "C:/wamp64/www/<carpeta-del-proyecto>/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 4. Estructura del proyecto

```
consultora/
├── app/
│   ├── controllers/       # Controladores MVC
│   ├── core/              # Núcleo del framework (Router, Auth, DB, etc.)
│   ├── models/            # Modelos de datos (PDO)
│   └── views/             # Vistas PHP por módulo
├── config/
│   ├── app.php            # Configuración general (nombre, timezone)
│   └── database.php       # Credenciales de base de datos
├── public/
│   ├── assets/            # CSS, JS, imágenes estáticas
│   ├── uploads/evidences/ # Archivos subidos por usuarios
│   ├── .htaccess          # Rewrite rules para Apache
│   └── index.php          # Front controller (único punto de entrada)
├── routes/
│   └── web.php            # Definición de todas las rutas
├── sql/
│   ├── schema.sql         # Script UNICO de creacion de BD (esquema + semilla completos)
│   └── archive/           # schema_merged.sql historico, incompatible, no importar
└── database/
    └── archive/            # Migraciones por fase, ya incorporadas a sql/schema.sql
```

---

## 5. Arquitectura

El sistema sigue el patrón **MVC (Model-View-Controller)** sin framework externo.

### Flujo de una petición

```
Navegador → public/index.php → Router → Controller → Model → View → Respuesta HTML
```

### Componentes del núcleo (`app/core/`)

| Clase | Responsabilidad |
|---|---|
| `Router` | Registra rutas GET/POST y despacha al controlador correcto |
| `Database` | Singleton PDO con conexión a MySQL |
| `BaseModel` | Clase base con método `paginate()` para todos los modelos |
| `BaseController` | Clase base con método `render()` para cargar vistas |
| `Auth` | Gestión de sesiones seguras (inicio, cierre, verificación de rol) |
| `Csrf` | Generación y validación de tokens CSRF en formularios POST |
| `Middleware` | Verificación de autenticación y roles antes de ejecutar controladores |
| `MaturityCalculator` | Servicio estático para calcular madurez y riesgo |
| `Validator` | Validación de datos de entrada |
| `Flash` | Mensajes de éxito/error entre redirecciones |

---

## 6. Base de datos

**Nombre:** `consultora_iso27002`  
**Charset:** `utf8mb4` / `utf8mb4_unicode_ci`  
**Motor:** InnoDB (todas las tablas)

### Tablas

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (admin, auditor, viewer) |
| `organizations` | Organizaciones auditadas |
| `areas` | Áreas o departamentos dentro de una organización |
| `iso_domains` | Dominios del estándar ISO/IEC 27002 |
| `iso_controls` | Controles de seguridad por dominio |
| `questions` | Preguntas de evaluación por control |
| `audits` | Auditorías realizadas |
| `responses` | Respuestas (sí/no/na) por pregunta en cada auditoría |
| `evidences` | Archivos adjuntos vinculados a respuestas |
| `audit_control_maturity` | Nivel de madurez por control en cada auditoría (recalculado automáticamente desde `responses.maturity_level`, no editado directamente por el auditor) |
| `recommendations` | Recomendaciones de mejora derivadas de auditorías |
| `question_maturity_scale` | Explicación por pregunta de qué representa cada nivel de madurez (0-5) |
| `activity_log` | Bitácora de cambios: quién cerró/reabrió/canceló una auditoría, cambió el estado de una recomendación o subió/eliminó una evidencia, y cuándo |
| `auditor_organizations` | Qué organizaciones cliente puede trabajar cada auditor de la consultora (`users.role = 'auditor'` sin organización propia) |

### Usuario inicial

```sql
email:    admin@datasolutionscr.net
password: Admin123*  (se hashea en el primer login)
role:     admin
```

---

## 7. Seguridad implementada

- **Contraseñas:** hasheadas con `password_hash()` usando `PASSWORD_DEFAULT` (bcrypt).
- **CSRF:** todos los formularios POST incluyen un token CSRF validado en el servidor.
- **Sesiones:** iniciadas con `session_regenerate_id(true)` al autenticarse.
- **Consultas SQL:** 100% con PDO y sentencias preparadas (sin concatenación de entrada de usuario).
- **Control de acceso:** middleware verifica autenticación y rol antes de cada ruta protegida.
- **Aislamiento por organización:** un usuario autoregistrado (`users.organization_id` no nulo) solo puede ver/gestionar auditorías, áreas, recomendaciones y evidencias de su propia organización (`Middleware::ownsOrganization()`). El personal de la consultora (`organization_id = NULL`) se divide en dos casos: un `admin` (o un `viewer` global) no tiene restricción; un `auditor` global solo puede ver/tocar las organizaciones que un admin le haya asignado en `auditor_organizations` (`Middleware::assignedOrganizationIds()` / `Middleware::visibleOrganizations()`) — si no tiene ninguna asignada, no ve ninguna organización, auditoría, ni comparación histórica de terceros. Esto evita que cualquier auditor de la firma tenga acceso a todos los clientes por defecto, replicando la barrera de confidencialidad entre cuentas que se espera de una consultora de auditoría real.
- **Subida de archivos:** se valida tipo MIME y se almacena con nombre generado (no el original). El acceso HTTP directo a `public/uploads/evidences/` está bloqueado por `.htaccess` (`Require all denied`); las evidencias solo se descargan a través de `GET /audits/evidence?id=`, que valida la organización dueña antes de entregar el archivo.

---

## 8. Cálculo de madurez y riesgo

Implementado en `app/core/MaturityCalculator.php`.

### Nivel de madurez por control
Asignado manualmente por el auditor en escala 0–5 y almacenado en `audit_control_maturity`.

### Madurez ponderada por dominio
```
maturity_domain = SUM(maturity_control × weight) / SUM(weight)
```

### Madurez global
```
maturity_global = SUM(maturity_domain) / COUNT(dominios)
```

### Exposición al riesgo por dimensión (C, I, D)
```
gap_control = 5 - maturity_level
risk_C = SUM(gap × confidentiality × weight) / SUM(confidentiality × weight × 5) × 100
risk_I = SUM(gap × integrity      × weight) / SUM(integrity      × weight × 5) × 100
risk_D = SUM(gap × availability   × weight) / SUM(availability   × weight × 5) × 100
```

### Índice general de riesgo
```
risk_global = (risk_C + risk_I + risk_D) / 3
```

---

## 9. Rutas principales

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/login` | Formulario de login |
| POST | `/login` | Autenticación |
| POST | `/logout` | Cierre de sesión |
| GET | `/dashboard` | Panel principal |
| GET/POST | `/users/*` | CRUD de usuarios |
| GET/POST | `/organizations/*` | CRUD de organizaciones |
| GET/POST | `/areas/*` | CRUD de áreas |
| GET/POST | `/domains/*` | CRUD de dominios ISO |
| GET/POST | `/controls/*` | CRUD de controles ISO |
| GET/POST | `/questions/*` | CRUD de preguntas |
| GET/POST | `/audits/*` | CRUD y ejecución de auditorías |
| GET | `/audits/run` | Ejecutar cuestionario |
| POST | `/audits/save` | Guardar respuestas del cuestionario |
| POST | `/audits/upload-evidence` | Subir archivo de evidencia |
| GET | `/audits/evidence` | Descargar una evidencia (valida organización dueña) |
| GET | `/audits/report` | Ver reporte de auditoría |
| GET/POST | `/recommendations/*` | Gestión de recomendaciones |
| GET | `/comparison` | Comparación entre auditorías |
| GET/POST | `/register` | Autoregistro público (crea organización + usuario auditor) |

---

## 10. Migraciones adicionales

Para instalaciones **nuevas**, `sql/schema.sql` es el único script necesario: ya
incluye todas las columnas y tablas (`objective`/`weight`/`confidentiality`/
`integrity`/`availability` en `iso_controls`, `dba_name` y `risk_c`/`risk_i`/
`risk_d` en `audits`, las tablas `audit_control_maturity`, `recommendations`,
`question_maturity_scale`, `activity_log` y `auditor_organizations`), y el
catálogo semilla (7 dominios, 15 controles y 75 preguntas) **específico de
administración de bases de datos**, con pesos, relación CID y objetivo ya
definidos.

Si ya tenías una base de datos creada con una versión anterior del catálogo
(controles genéricos de seguridad organizacional) y quieres conservar tus
organizaciones/auditorías/usuarios, hay dos scripts opcionales en `sql/` que
actualizan en sitio sin perder datos:

- `sql/actualizar_catalogo_administracion_bd.sql` — reemplaza el texto de
  dominios, controles (incluye el `objective`, antes vacío), preguntas y
  escala de madurez por el catálogo de administración de bases de datos,
  sin tocar auditorías/respuestas/recomendaciones ya guardadas.
- `sql/agregar_bitacora_cambios.sql` — crea únicamente la tabla `activity_log`
  si tu base de datos es anterior a la incorporación de la bitácora de cambios.
- `sql/agregar_asignacion_auditores.sql` — crea únicamente la tabla
  `auditor_organizations` si tu base de datos es anterior a la restricción de
  auditores por organización asignada. Los auditores globales que ya existan
  quedan sin ninguna organización asignada hasta que un admin se la asigne
  desde Usuarios > Editar.

Los scripts antiguos se conservan solo como referencia histórica en
`database/archive/` y `sql/archive/` — **no deben importarse**, su contenido ya
está incorporado en `sql/schema.sql`.

---

## 11. Credenciales iniciales

| Campo | Valor |
|---|---|
| Correo | `admin@datasolutionscr.net` |
| Contraseña | `Admin123*` |

> Cambiar la contraseña del administrador inmediatamente después del primer acceso en producción.

---

## 12. Trazabilidad de reportes e indicadores con el enunciado del proyecto

El reporte ejecutivo (`GET /audits/report`) y el dashboard (`GET /dashboard`) en conjunto cubren la lista mínima de reportes e indicadores exigida en el enunciado del Proyecto Integrador:

| Requerido en el enunciado | Dónde se implementa |
|---|---|
| Resultados generales de la auditoría | Reporte — sección 2 |
| Resultados por dominio | Reporte — sección 4 |
| Resultados por control | Reporte — sección 8 |
| Controles con menor nivel de madurez | Reporte — sección 5 |
| Controles con mayor exposición al riesgo | Reporte — sección 6 |
| Indicadores estadísticos | Reporte — sección 7 |
| Gráficos | Reporte — sección 10 (Chart.js) y Dashboard |
| Mapas de calor | Reporte — sección 4, y Dashboard |
| Reporte ejecutivo de resultados | `app/views/audits/report.php` (10 secciones) |
| Exposición al riesgo (C, I, D) | Reporte — sección 3, calculada en `MaturityCalculator::calculate()` |

Adicionalmente, `ComparisonController` (`GET /comparison`) implementa la evolución de indicadores, madurez global, riesgo por dimensión y madurez por dominio entre dos auditorías, permitiendo el seguimiento histórico mencionado en la introducción del enunciado.

### Valor agregado (funcionalidades no exigidas por el enunciado)

- **Recomendaciones de mejora con seguimiento** (`RecommendationController`): responsable, fecha límite y estado por control, con un botón "Generar recomendación" en las secciones 5 y 6 del reporte que precarga el formulario a partir del control con menor madurez o mayor riesgo, en vez de obligar al auditor a volver a escribirlo todo desde cero.
- **Comparación histórica** entre auditorías de una misma organización (`ComparisonController`).
- **Autoregistro y autoevaluación** de organizaciones (`RegisterController`), con aislamiento multi-tenant por `organization_id` (`Middleware::ownsOrganization`).
- **Bitácora de cambios** (`activity_log` / `ActivityLog`): registra quién cerró/reabrió/canceló una auditoría, cambió el estado de una recomendación o subió/eliminó una evidencia, visible en el detalle de cada auditoría.
- **Distinción entre auditoría de consultora y autoevaluación**: el listado de auditorías, el detalle y el reporte ejecutivo etiquetan si la auditoría la realizó personal de la consultora (`users.organization_id IS NULL`) o es una autoevaluación de la propia empresa, para no diluir el valor probatorio del reporte frente a terceros (`audit_kind_label()` en `app/core/helpers.php`).
