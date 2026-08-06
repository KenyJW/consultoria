# Guia de instalacion — Sistema de Evaluacion de Riesgo ISO/IEC 27002

Esta guia es para que cualquier integrante del equipo pueda descargar el proyecto y
ejecutarlo en su maquina con WAMP, sin importar en que carpeta lo coloque.

> El proyecto ya esta preparado para ser portable: las rutas de enlaces, CSS, JS y
> redirecciones se calculan solas. Solo hay que seguir estos pasos.

---

## Requisitos

- **WAMP** (o XAMPP) con **PHP 8.1 o superior** y **MySQL/MariaDB**.
- Un navegador.

Para verificar la version de PHP en WAMP: click en el icono de WAMP → PHP → Version.
Debe ser 8.1 o mayor (el proyecto usa sintaxis moderna).

---

## Paso 1 — Colocar el proyecto

1. Descargar/descomprimir el proyecto.
2. Copiar la carpeta completa dentro de `C:\wamp64\www\`.

- Puede llamarse como quieras: `consultora`, `proyecto-bd`, lo que sea.
- La estructura debe verse asi:

```
C:\wamp64\www\consultora\├── .htaccess        <-- (archivo de la raiz, ver Paso 4)├── app\├── config\├── public\│   ├── .htaccess│   ├── index.php│   └── assets\├── routes\└── database\
```

---

## Paso 2 — Crear e importar la base de datos

1. Abrir **phpMyAdmin** (icono WAMP → phpMyAdmin, o `http://localhost/phpmyadmin`).
2. Crear una base de datos llamada exactamente:

```
consultora_iso27002
```
(con cotejamiento `utf8mb4_general_ci`).
3. Seleccionarla y en la pestaña **Importar**, cargar los scripts EN ESTE ORDEN:

1. `database/schema.sql`
2. `database/instrumento_iso_seed.sql`      (dominios + controles)
3. `database/instrumento_iso_75preguntas.sql`  (las 75 preguntas)
4. `database/update_pesos_cid_controles.sql`  (actualiza `weight` y flags `confidentiality|integrity|availability` para los controles C1–C15)

> Si usas el nombre de base `consultora_iso27002` no necesitas configurar nada mas:
> el proyecto ya apunta a esa base con usuario `root` y contrasena vacia (lo estandar
> en WAMP). Ver Paso 3 solo si tu MySQL usa otros datos.

---

## Paso 3 — Configuracion de la base de datos (solo si hace falta)

El archivo `config/database.php` ya trae valores por defecto para WAMP:

Dato
Valor por defecto

host
127.0.0.1

puerto
3306

base
consultora_iso27002

usuario
root

contrasena
(vacia)

**Si tu WAMP usa esos valores (lo normal), NO toques nada.**

Si tu MySQL tiene contrasena u otro usuario, edita `config/database.php` y cambia
solo los valores que correspondan. Por ejemplo, si tu root tiene contrasena:

```
'password' => getenv('DB_PASSWORD') ?: 'tu_password_aqui',
```

---

## Paso 4 — Habilitar el acceso sin /public (rewrite)

El proyecto usa un `.htaccess` para que puedas entrar sin escribir `/public` en la
URL. Para que funcione, Apache necesita el modulo **rewrite** activado.

1. Click en el icono de WAMP → **Apache** → **Apache modules**.
2. Buscar **rewrite_module** en la lista y asegurarse de que este tildado.
3. Si lo acabas de activar, reiniciar WAMP (icono → Restart All Services).

Con esto, el archivo `.htaccess` de la raiz reenvia todo hacia `public/`
automaticamente.

---

## Paso 5 — Entrar al sistema

Abrir en el navegador (reemplazando `consultora` por el nombre de tu carpeta):

```
http://localhost/consultora/login
```

> Fijate que NO hace falta escribir `/public`. Si por algo el rewrite no quedo
> activo, siempre podes entrar por la ruta larga como respaldo:
> `http://localhost/consultora/public/login`

**Usuario inicial:**

- Correo: `admin@datasolutionscr.net`
- Contrasena: `Admin123*`

---

## Verificacion rapida (que todo quedo bien)

1. Entra al login y accede con el usuario admin.
2. En el menu lateral deberias ver: Dashboard, Organizaciones, Areas, Usuarios,
Dominios ISO, Controles, Preguntas, Auditorias.
3. Entra a **Preguntas**: deberian aparecer las 75 preguntas (5 por control).
4. Crea una Organizacion → un Area → una Auditoria → responde el cuestionario →
Guardar progreso → Finalizar. Si todo fluye, la instalacion quedo correcta.

---

## Problemas comunes

Sintoma
Causa probable
Solucion

"Not Found" al entrar sin /public
rewrite_module desactivado
Paso 4

Pantalla en blanco / Error 500
credenciales de BD incorrectas
Paso 3

"Unknown database consultora_iso27002"
no importaste la BD o usaste otro nombre
Paso 2

El CSS no carga (se ve sin estilos)
rewrite ok pero falta la carpeta assets en public/
verificar que exista `public/assets/`

Las preguntas aparecen duplicadas
importaste dos veces el seed
volver a importar limpio (TRUNCATE questions)

---

## Nota tecnica (para la defensa del proyecto)

El sistema no depende de una ruta fija. La constante `BASE_URL` se calcula en tiempo
de ejecucion a partir de la ubicacion real de `index.php`, quitando el segmento
`/public`. Por eso el proyecto funciona igual en `localhost/consultora/`,
`localhost/proyecto-bd/` o en un dominio real, sin cambiar codigo. El `.htaccess` de
la raiz solo agrega la comodidad de omitir `/public` en la URL, manteniendo `app/` y
`config/` fuera del alcance directo del navegador por seguridad.
