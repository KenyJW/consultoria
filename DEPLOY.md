# Guía de despliegue en un hosting real

Este documento explica cómo llevar el sistema de `http://localhost/consultoria`
(WAMP) a un hosting público real, sin tocar código. La aplicación ya está
preparada para esto: usa rutas relativas calculadas en tiempo de ejecución
(`public/index.php`) y lee la configuración sensible por variables de entorno
con valores por defecto (`config/database.php`, `config/app.php`).

---

## 1. Requisitos del hosting

| Requisito | Detalle |
|---|---|
| PHP | 8.1 o superior (el proyecto se probó en 8.2/8.3) |
| Extensiones PHP | `pdo_mysql`, `mbstring`, `fileinfo` (todas vienen por defecto en cPanel/Plesk) |
| Base de datos | MySQL 8.x o MariaDB 10.6+ |
| Servidor web | Apache o LiteSpeed **con `mod_rewrite` habilitado** y soporte de `.htaccess` (`AllowOverride All`) |
| Almacenamiento | Disco persistente y escribible en `public/uploads/evidences/` (evidencias subidas por los auditores) |
| HTTPS | Recomendado/obligatorio antes de aceptar registros públicos reales (ver sección 6) |

**Por qué no Vercel/Netlify/similares:** son plataformas *serverless* sin disco
persistente ni soporte nativo de PHP+MySQL — no sirven para esta app sin
reescribirla. Necesitas un hosting "tipo LAMP" clásico.

### Proveedores recomendados (económicos, compatibles sin cambios de código)

1. **Hosting compartido con cPanel** (Hostinger, DonWeb, Namecheap, etc.) —
   la opción más simple: subes archivos por FTP/Administrador de archivos,
   creas la base de datos desde cPanel, importas `sql/schema.sql` desde
   phpMyAdmin. Costo típico: US$2–5/mes.
2. **Railway / Render** — más "moderno" (Docker + MySQL administrado), pero
   requiere escribir un `Dockerfile` propio; solo si ya tienes experiencia
   con contenedores y quieres esa ruta.

Para un proyecto de curso con fecha de defensa, la opción 1 es la más rápida
de dejar funcionando.

---

## 2. Subir el proyecto

Sube **todo** el contenido de este repositorio (no solo `public/`) a una
carpeta del hosting, por ejemplo `~/consultoria/` (fuera del `public_html`
si tu panel lo permite, para que `app/`, `config/`, `database/` y `sql/`
**no** queden accesibles desde el navegador).

### Apuntar el dominio a `public/`

El punto de entrada de la aplicación es `public/index.php`; todo lo demás
(`app/`, `config/`, `sql/`, `database/`) debe quedar **fuera** del alcance
directo del navegador.

- **Si tu panel permite elegir el "Document Root" del dominio** (cPanel con
  "Domains", cuentas VPS, etc.): apúntalo directamente a la carpeta `public/`
  del proyecto. Es la forma más segura — el resto de carpetas ni siquiera
  son alcanzables por URL.
- **Si tu hosting obliga a usar `public_html/` como raíz fija** (muchos
  planes compartidos básicos): sube el proyecto completo dentro de
  `public_html/` (o una subcarpeta) — el `.htaccess` de la raíz del proyecto
  ya redirige todo hacia `public/` automáticamente (es el mismo mecanismo
  que usas hoy en WAMP para entrar sin escribir `/public` en la URL). Solo
  asegúrate de que `mod_rewrite` esté activo (suele estarlo por defecto en
  cPanel).

---

## 3. Crear la base de datos e importar el esquema

1. Desde el panel del hosting, crea una base de datos MySQL y un usuario con
   todos los privilegios sobre ella (los hosts compartidos casi siempre
   prefijan el nombre, ej. `usuario_consultora`).
2. Abre phpMyAdmin (o el cliente MySQL que ofrezca tu panel) e importa
   **un solo archivo**: `sql/schema.sql`. Ya incluye tablas, columnas y
   datos semilla completos (dominios, controles, preguntas, escala de
   madurez). Los archivos en `database/archive/` y `sql/archive/` son solo
   referencia histórica — no los importes.

> Nota: `sql/schema.sql` empieza con `CREATE DATABASE IF NOT EXISTS
> consultora_iso27002`. Si tu hosting ya te obligó a crear la base con un
> nombre distinto (común en compartido), quita esa primera línea y la
> línea `USE consultora_iso27002;` antes de importar, o simplemente
> selecciona tu base ya creada como destino en phpMyAdmin antes de importar
> (phpMyAdmin ejecuta el script dentro de la base seleccionada aunque el
> script tenga su propio `CREATE DATABASE`/`USE`).

---

## 4. Configurar la conexión a la base de datos

Tienes dos formas — usa la que tu hosting soporte mejor:

### Opción A — Variables de entorno (recomendada si tu panel las permite)

Casi todo cPanel moderno tiene una sección "Setup Node.js/PHP App" o similar
donde puedes definir variables de entorno para el dominio. Si no, puedes
definirlas en el `.htaccess` de `public/`:

```apache
SetEnv DB_HOST 127.0.0.1
SetEnv DB_PORT 3306
SetEnv DB_DATABASE usuario_consultora
SetEnv DB_USERNAME usuario_consultora
SetEnv DB_PASSWORD "tu_password_real"
SetEnv APP_URL https://tu-dominio.com
SetEnv INITIAL_ADMIN_PASSWORD "otra_password_fuerte"
```

### Opción B — Editar `config/database.php` directamente (más simple)

Si tu hosting no soporta variables de entorno fácilmente, edita
`config/database.php` y reemplaza los valores por defecto por los reales:

```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'usuario_consultora',
    'username' => 'usuario_consultora',
    'password' => 'tu_password_real',
    'charset' => 'utf8mb4',
];
```

**No subas nunca este archivo con la password real a un repositorio
público.** Si usas git para desplegar, considera la Opción A, o agrega
`config/database.local.php` (ya contemplado en `.gitignore`) y cárgalo
condicionalmente si decides extender `config/database.php` para eso.

### `APP_URL`

Define `APP_URL` (variable de entorno) con la URL pública completa, ej.
`https://consultoria.tudominio.com`. Si no la defines, la aplicación calcula
la ruta base sola a partir de dónde vive `public/index.php` (funciona igual,
como ya viste en WAMP), pero definirla explícitamente es más robusto en
producción.

---

## 5. Verificación post-despliegue

1. Entra a `https://tu-dominio.com/login` con
   `admin@datasolutionscr.net` / `Admin123*` (o `INITIAL_ADMIN_PASSWORD`
   si la definiste distinta) — **cámbiala de inmediato** desde
   Usuarios → Editar.
2. Entra a `https://tu-dominio.com/` (sin sesión) y confirma que carga la
   landing pública, no un error.
3. Prueba `/register`: crea una organización de prueba, confirma que solo
   ves tu propia organización en los filtros (no las de otras), y bórrala
   luego desde phpMyAdmin si era solo de prueba.
4. Sube una evidencia de prueba en una auditoría y confirma que el archivo
   aparece en `public/uploads/evidences/` con permisos de escritura
   correctos (si falla, ajusta permisos de esa carpeta a 755/775 desde el
   administrador de archivos del panel).
5. Verifica que `public/uploads/`, `app/`, `config/`, `database/`, `sql/`
   **no sean accesibles directamente por URL** (ej. probar
   `https://tu-dominio.com/config/database.php` debe dar 403/404, nunca
   mostrar el contenido del archivo).

---

## 6. Checklist de seguridad antes de aceptar usuarios reales

- [ ] HTTPS activo (la mayoría de hostings dan un certificado Let's Encrypt
  gratis desde el panel) — las cookies de sesión ya están marcadas `secure`
  automáticamente cuando detectan HTTPS (`app/core/Auth.php`), así que solo
  falta activar el certificado del lado del servidor.
- [ ] Contraseña del admin inicial cambiada.
- [ ] `display_errors` desactivado en el PHP de producción (`display_errors
  = Off` en el `php.ini` del hosting o vía panel) para que ningún error
  inesperado muestre detalles internos a un visitante.
- [ ] Confirmar que `config/database.php` con la password real **no** quedó
  en un repositorio git público (si usas git para desplegar).
- [ ] Backup periódico de la base de datos configurado desde el panel del
  hosting (la mayoría de cPanel lo ofrece con un clic).
