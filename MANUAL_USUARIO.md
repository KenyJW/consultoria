# Manual de Usuario
## Sistema de Auditorías ISO/IEC 27002 — DataSolutions CR

---

## 1. Introducción

Este sistema permite gestionar auditorías de seguridad de bases de datos basadas en el estándar ISO/IEC 27002. Está dirigido a auditores y administradores que necesitan evaluar el nivel de madurez y riesgo de seguridad en organizaciones.

---

## 2. Acceso al sistema

1. Abrir el navegador e ingresar la URL del sistema (ej. `http://localhost:8000`).
2. Ingresar las credenciales:
   - **Correo:** `admin@datasolutionscr.net`
   - **Contraseña:** `Admin123*`
3. Hacer clic en **Iniciar sesión**.

> La sesión expira por inactividad. Si el sistema redirige al login, volver a autenticarse.

---

## 3. Roles de usuario

| Rol | Permisos |
|---|---|
| `admin` | Acceso total: usuarios, organizaciones, controles, auditorías |
| `auditor` | Crear y ejecutar auditorías, registrar respuestas y evidencias |
| `viewer` | Solo lectura: ver reportes y resultados |

---

## 4. Panel de control (Dashboard)

Al iniciar sesión se muestra el dashboard con:

- Totales de auditorías (en borrador, en progreso, cerradas)
- Promedio global de madurez y riesgo
- **Mapa de calor de madurez por dominio**, para identificar de un vistazo qué dominios están más rezagados
- Gráfico de madurez promedio por dominio ISO
- Últimas auditorías cerradas con sus puntajes

---

## 5. Gestión de usuarios

**Ruta:** Menú → Usuarios

### Crear usuario
1. Clic en **Nuevo usuario**.
2. Completar: nombre, correo, contraseña, rol (`admin` / `auditor` / `viewer`) y estado.
3. Clic en **Guardar**.

### Editar usuario
1. En la lista, clic en **Editar** junto al usuario.
2. Modificar los campos necesarios. La contraseña solo se cambia si se escribe una nueva.
3. Clic en **Guardar**.

### Desactivar usuario
- Clic en **Eliminar**. El usuario queda con estado `inactive` (no se borra de la base de datos).

---

## 6. Gestión de organizaciones

**Ruta:** Menú → Organizaciones

Permite registrar las empresas o entidades que serán auditadas.

### Crear organización
1. Clic en **Nueva organización**.
2. Completar: nombre (único), dirección, correo y estado.
3. Clic en **Guardar**.

### Activar / Desactivar
- Usar el botón de toggle en la lista para cambiar el estado entre `active` e `inactive`.

---

## 7. Gestión de áreas

**Ruta:** Menú → Áreas

Las áreas son departamentos o unidades dentro de una organización que se auditan.

### Crear área
1. Clic en **Nueva área**.
2. Seleccionar la organización a la que pertenece.
3. Completar nombre y descripción.
4. Clic en **Guardar**.

> El nombre del área debe ser único dentro de la misma organización.

---

## 8. Gestión de dominios ISO

**Ruta:** Menú → Dominios

Los dominios agrupan los controles ISO/IEC 27002 (ej. "Control de accesos", "Criptografía").

### Crear dominio
1. Clic en **Nuevo dominio**.
2. Ingresar código único (ej. `D1`), nombre y descripción.
3. Clic en **Guardar**.

---

## 9. Gestión de controles ISO

**Ruta:** Menú → Controles

Los controles son los requisitos específicos de seguridad dentro de cada dominio.

### Crear control
1. Clic en **Nuevo control**.
2. Seleccionar el dominio al que pertenece.
3. Completar: código único, título, descripción, objetivo.
4. Asignar pesos de impacto: **Confidencialidad**, **Integridad**, **Disponibilidad** (valores enteros).
5. Asignar **Peso** general del control.
6. Clic en **Guardar**.

---

## 10. Gestión de preguntas

**Ruta:** Menú → Preguntas

Cada control tiene preguntas de evaluación que el auditor responde durante la auditoría.

### Crear pregunta
1. Clic en **Nueva pregunta**.
2. Seleccionar el control al que pertenece.
3. Escribir el texto de la pregunta.
4. Asignar un peso (por defecto `1.00`).
5. Clic en **Guardar**.

---

## 11. Gestión de auditorías

**Ruta:** Menú → Auditorías

Este es el módulo principal del sistema.

### Crear auditoría
1. Clic en **Nueva auditoría**.
2. Completar:
   - **Organización** y **Área** auditada
   - **Auditor** responsable
   - **Nombre del DBA** (administrador de base de datos evaluado)
   - **Nombre** de la auditoría
   - **Fecha de inicio**
3. Clic en **Guardar**. La auditoría queda en estado `draft`.

### Estados de una auditoría

| Estado | Descripción |
|---|---|
| `draft` | Borrador, aún no iniciada |
| `in_progress` | En ejecución, se están registrando respuestas |
| `closed` | Cerrada, con puntajes calculados |

### Ejecutar el cuestionario
1. Desde la lista de auditorías, clic en **Ejecutar** (o **Continuar**).
2. El sistema muestra todos los dominios, controles y preguntas activas.
3. Para cada pregunta, seleccionar:
   - **Sí** — el control se cumple
   - **No** — el control no se cumple
   - **No aplica** — excluida del cálculo
4. Opcionalmente agregar **observación** y **recomendación** por pregunta.
5. Para cada control, asignar el **Nivel de madurez** (escala 0–5):

| Nivel | Etiqueta |
|---|---|
| 0 | No existe |
| 1 | Informal |
| 2 | Parcial |
| 3 | Definido |
| 4 | Gestionado |
| 5 | Optimizado |

6. Clic en **Guardar respuestas** para registrar el avance.

### Subir evidencias
- En cada pregunta respondida aparece el botón **Subir evidencia**.
- Se pueden adjuntar archivos (imágenes, PDFs, documentos) como respaldo.
- Las evidencias se pueden eliminar con el botón **Eliminar**.

### Cerrar auditoría
1. Una vez completado el cuestionario, clic en **Cerrar auditoría**.
2. El sistema calcula automáticamente:
   - **Puntaje de madurez global** (promedio ponderado por dominio)
   - **Índice de riesgo global** y por dimensión (C, I, D)
3. La auditoría pasa a estado `closed` y se registra la fecha de cierre.

> Una auditoría cerrada puede **reabrirse** con el botón **Reabrir** si se necesitan correcciones.

### Ver reporte
Desde la auditoría cerrada, clic en **Ver reporte** para ver el informe ejecutivo completo, que incluye:

1. **Datos generales** de la auditoría (organización, área, auditor, DBA, fechas).
2. **Resultados generales**: madurez global y cumplimiento general.
3. **Exposición al riesgo por dimensión** (Confidencialidad, Integridad, Disponibilidad).
4. **Resultados por dominio**, incluyendo un **mapa de calor** de madurez por dominio.
5. **Controles con menor nivel de madurez**, para priorizar la acción correctiva.
6. **Controles con mayor exposición al riesgo**.
7. **Indicadores estadísticos** del cuestionario aplicado.
8. **Resultados por control**, con su nivel de madurez y cumplimiento individual.
9. **Detalle del cuestionario**: cada pregunta con su respuesta, observación y recomendación.
10. **Gráfico de madurez por dominio** (visualización comparativa con Chart.js).

Este reporte corresponde al reporte ejecutivo de resultados solicitado en el proyecto, e incluye todos los indicadores y reportes mínimos exigidos (resultados generales, por dominio, por control, controles críticos, indicadores, gráficos y mapas de calor).

---

## 12. Recomendaciones

**Ruta:** Menú → Recomendaciones

Permite registrar y dar seguimiento a las acciones correctivas derivadas de las auditorías.

### Crear recomendación
1. Desde una auditoría, clic en **Recomendaciones** → **Nueva recomendación**.
2. Seleccionar el control al que aplica.
3. Completar: descripción, responsable, fecha límite, estado y notas.
4. Clic en **Guardar**.

### Estados de recomendación

| Estado | Descripción |
|---|---|
| `pending` | Pendiente de atención |
| `in_progress` | En proceso de implementación |
| `done` | Completada |

---

## 13. Comparación de auditorías

**Ruta:** Menú → Comparación

Permite comparar dos auditorías (por ejemplo, de la misma organización y área en distintos períodos) para visualizar su evolución. Incluye:

- **Evolución de indicadores generales** entre ambas auditorías.
- **Evolución de la madurez global**.
- **Evolución del riesgo** por dimensión (Confidencialidad, Integridad, Disponibilidad).
- **Comparación de madurez por dominio** entre ambas auditorías, para identificar en qué dominios hubo mejora o retroceso.

---

## 14. Cierre de sesión

- Clic en el nombre de usuario (esquina superior) → **Cerrar sesión**, o usar el botón de logout en el menú.

---

## 15. Preguntas frecuentes

**¿Puedo editar una auditoría cerrada?**
Sí, usando el botón **Reabrir**. Esto la devuelve a estado `in_progress`.

**¿Qué pasa si desactivo un usuario?**
No puede iniciar sesión, pero sus auditorías y datos históricos se conservan.

**¿Puedo eliminar una organización?**
No se elimina, solo se desactiva. Esto evita perder el historial de auditorías asociadas.

**¿Qué significa "No aplica" en una pregunta?**
Esa pregunta se excluye del cálculo de cumplimiento del control, útil cuando el requisito no es relevante para la organización auditada.
