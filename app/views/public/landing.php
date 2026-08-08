<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Serif:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
</head>
<body>

<a class="saltar" href="#contenido">Saltar al contenido</a>

<header class="barra">
  <div class="contenedor barra__interior">
    <a class="marca" href="#contenido">
      <span class="marca__glifo" aria-hidden="true"></span>
      <span class="marca__texto">DataSolutions <span style="color:var(--ambar)">CR</span></span>
    </a>
    <nav class="nav" aria-label="Navegación principal">
      <a href="#servicios">Servicios</a>
      <a href="#como-funciona">Cómo funciona</a>
      <a href="#equipo">Equipo</a>
      <a href="#preguntas">Preguntas</a>
      <a href="#contacto">Contacto</a>
      <a href="<?= BASE_URL ?>/login">Iniciar sesión</a>
      <a class="boton boton--primario" href="<?= BASE_URL ?>/register">Crear cuenta</a>
    </nav>
  </div>
</header>

<main id="contenido">

  <section class="hero">
    <div class="contenedor hero__interior">

      <div class="hero__texto">
        <p class="eyebrow">Consultoría en administración de bases de datos</p>
        <h1 class="hero__titulo">Toda base de datos<br>lleva su bitácora.<br><em>Nosotros la leemos.</em></h1>
        <p class="hero__bajada">
          Cada transacción deja registro antes de confirmarse. Ese registro dice quién tocó
          qué, cuándo, y qué pasaría si hubiera que volver atrás. Trabajamos ahí: en el punto
          donde el rendimiento, el respaldo y el riesgo son el mismo problema.
        </p>
        <div class="hero__acciones">
          <a class="boton boton--primario" href="<?= BASE_URL ?>/register">Autoevaluar mi organización</a>
          <a class="boton boton--fantasma" href="#servicios">Ver servicios</a>
        </div>
      </div>

      <div class="log" role="img" aria-label="Ejemplo de bitácora de transacciones de una base de datos, mostrando operaciones registradas y confirmadas.">
        <div class="log__barra">
          <span class="log__punto"></span>
          <span class="log__archivo">transaction.log</span>
        </div>
        <ol class="log__lineas">
          <li><span class="lsn">00A1</span><span class="op op--begin">BEGIN</span><span class="txt">tx 4417</span></li>
          <li><span class="lsn">00A2</span><span class="op">UPDATE</span><span class="txt">clientes · saldo</span></li>
          <li><span class="lsn">00A3</span><span class="op">INSERT</span><span class="txt">auditoria · 1 fila</span></li>
          <li><span class="lsn">00A4</span><span class="op op--commit">COMMIT</span><span class="txt">tx 4417 · 12&nbsp;ms</span></li>
          <li><span class="lsn">00A5</span><span class="op op--check">CHECKPOINT</span><span class="txt">respaldo íntegro</span></li>
          <li class="log__cursor"><span class="lsn">00A6</span><span class="op op--wait">ESPERANDO</span><span class="txt">próxima transacción<span class="caret"></span></span></li>
        </ol>
      </div>

    </div>
  </section>

  <section class="cinta">
    <div class="contenedor cinta__interior">
      <p><strong>Disponibilidad</strong><span>Continuidad del servicio ante fallo</span></p>
      <p><strong>Integridad</strong><span>El dato dice lo que ocurrió</span></p>
      <p><strong>Confidencialidad</strong><span>Acceso solo para quien corresponde</span></p>
    </div>
  </section>

  <section id="servicios" class="seccion">
    <div class="contenedor">
      <p class="eyebrow">Servicios</p>
      <h2 class="seccion__titulo">Cuatro frentes de trabajo</h2>
      <p class="seccion__intro">
        Intervenimos sobre bases de datos en producción sin detener la operación. Cada
        servicio parte de un levantamiento del estado actual y termina con documentación
        que el equipo interno puede sostener.
      </p>

      <div class="rejilla">

        <article class="tarjeta">
          <h3>Administración y afinamiento</h3>
          <p>
            Revisión de índices, consultas lentas y planes de ejecución. Ajustamos la
            configuración del motor al uso real, no al manual.
          </p>
          <ul class="lista">
            <li>Diagnóstico de rendimiento</li>
            <li>Gestión de usuarios y privilegios</li>
            <li>Monitoreo y alertas</li>
          </ul>
        </article>

        <article class="tarjeta">
          <h3>Respaldo y recuperación</h3>
          <p>
            Un respaldo sin restauración probada no es un respaldo. Diseñamos la política
            y la ensayamos hasta que el tiempo de recuperación sea un número conocido.
          </p>
          <ul class="lista">
            <li>Política de respaldo por criticidad</li>
            <li>Pruebas de restauración</li>
            <li>Plan de continuidad</li>
          </ul>
        </article>

        <article class="tarjeta">
          <h3>Seguridad y auditoría</h3>
          <p>
            Evaluamos controles de acceso, cifrado y registro de actividad con referencia
            a ISO/IEC 27001 y 27002, y entregamos el plan de tratamiento de riesgos.
            <strong>Esta plataforma automatiza esa evaluación.</strong>
          </p>
          <ul class="lista">
            <li>Análisis de riesgos</li>
            <li>Revisión de controles</li>
            <li>Trazabilidad de accesos</li>
          </ul>
        </article>

        <article class="tarjeta">
          <h3>Migración de datos</h3>
          <p>
            Cambio de motor o de versión con ventana acotada. Validamos que el conteo,
            los tipos y las relaciones lleguen intactos al destino.
          </p>
          <ul class="lista">
            <li>Mapeo de esquemas</li>
            <li>Migración por fases</li>
            <li>Verificación posterior</li>
          </ul>
        </article>

      </div>
    </div>
  </section>

  <section id="como-funciona" class="seccion seccion--alterna aparece">
    <div class="contenedor">
      <p class="eyebrow">Cómo funciona</p>
      <h2 class="seccion__titulo">De la política impresa al reporte en un clic</h2>
      <p class="seccion__intro">
        La plataforma automatiza el ciclo completo de evaluación de riesgo basado en
        ISO/IEC 27002, siguiendo el modelo descrito en el proyecto: catálogo de controles,
        cuestionario, cálculo de madurez y riesgo, y reporte ejecutivo.
      </p>

      <div class="pasos">
        <div class="paso">
          <span class="paso__num">01</span>
          <h3>Cree su cuenta</h3>
          <p>Regístrese con los datos de su organización — sin costo, en menos de un minuto.</p>
        </div>
        <div class="paso">
          <span class="paso__num">02</span>
          <h3>Configure sus áreas</h3>
          <p>Defina las áreas o departamentos que quiere evaluar dentro de su organización.</p>
        </div>
        <div class="paso">
          <span class="paso__num">03</span>
          <h3>Responda el cuestionario</h3>
          <p>75 preguntas sobre los 15 controles ISO/IEC 27002 aplicables a bases de datos, con evidencia y justificación por pregunta.</p>
        </div>
        <div class="paso">
          <span class="paso__num">04</span>
          <h3>Reciba su reporte</h3>
          <p>Madurez, riesgo por dimensión CID, mapa de calor y recomendaciones — listos para exportar.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="equipo" class="seccion">
    <div class="contenedor">
      <p class="eyebrow">Equipo</p>
      <h2 class="seccion__titulo">Quiénes escriben la bitácora</h2>
      <p class="seccion__intro">
        Somos estudiantes del curso de Administración de Bases de Datos de la Universidad
        Nacional de Costa Rica. Este sitio es el entregable del proyecto de consultoría
        del curso.
      </p>

      <div class="equipo">

        <article class="persona">
          <p class="persona__id">01</p>
          <h3>Sebastián Monge Salas</h3>
          <p class="persona__rol">Administración y afinamiento</p>
        </article>

        <article class="persona">
          <p class="persona__id">02</p>
          <h3>Kenny Jiménez Wang</h3>
          <p class="persona__rol">Respaldo y recuperación</p>
        </article>

        <article class="persona">
          <p class="persona__id">03</p>
          <h3>Adrián Fernández Fernández</h3>
          <p class="persona__rol">Seguridad y auditoría</p>
        </article>

        <article class="persona">
          <p class="persona__id">04</p>
          <h3>José Pérez Jiménez</h3>
          <p class="persona__rol">Migración de datos</p>
        </article>

      </div>
    </div>
  </section>

  <section id="preguntas" class="seccion seccion--alterna aparece">
    <div class="contenedor">
      <p class="eyebrow">Preguntas frecuentes</p>
      <h2 class="seccion__titulo">Antes de empezar</h2>

      <div class="faq">
        <details class="faq__item" open>
          <summary>¿Cuánto cuesta registrarme y evaluar mi organización?<span class="faq__signo" aria-hidden="true"></span></summary>
          <p>Nada. Crear su cuenta y ejecutar auditorías dentro de la plataforma es gratuito.</p>
        </details>
        <details class="faq__item">
          <summary>¿En qué norma se basa el cuestionario?<span class="faq__signo" aria-hidden="true"></span></summary>
          <p>En 15 controles de ISO/IEC 27002:2022 seleccionados por su relación directa con la administración de bases de datos (control de accesos, criptografía, respaldos, continuidad, gestión de incidentes, cumplimiento), agrupados en 7 dominios.</p>
        </details>
        <details class="faq__item">
          <summary>¿Otras organizaciones pueden ver mis resultados?<span class="faq__signo" aria-hidden="true"></span></summary>
          <p>No. Cada organización que se registra solo ve sus propias auditorías, áreas y reportes.</p>
        </details>
        <details class="faq__item">
          <summary>¿Cómo se calcula el nivel de riesgo?<span class="faq__signo" aria-hidden="true"></span></summary>
          <p>A partir del nivel de madurez (0–5) que usted asigna a cada pregunta con su justificación, ponderado por el peso de cada control y su relación con Confidencialidad, Integridad y Disponibilidad.</p>
        </details>
      </div>
    </div>
  </section>

  <section id="contacto" class="cierre aparece">
    <div class="contenedor cierre__interior">
      <h2>¿Hay algo en su base de datos que nadie ha revisado en meses?</h2>
      <p>
        Cree su cuenta gratis y evalúe sus controles de seguridad ahora mismo, o
        escríbanos por el canal que prefiera y coordinamos una revisión inicial sin costo.
      </p>
      <div class="hero__acciones mb-3" style="justify-content:center;">
        <a class="boton boton--primario boton--grande" href="<?= BASE_URL ?>/register">Crear cuenta gratis</a>
      </div>
      <div class="contacto-directo">
        <a class="enlace-social enlace-social--whatsapp" href="https://wa.me/50689249251?text=Hola%2C%20quisiera%20informaci%C3%B3n%20sobre%20la%20evaluaci%C3%B3n%20de%20riesgo%20ISO%2FIEC%2027002" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.6.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5s-.6-1.5-.9-2c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.3-.1-.2-.3-.2-.6-.4z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.9.5 3.6 1.4 5.1L2 22l5-1.3c1.4.8 3.1 1.3 4.9 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3C4.4 15 4 13.5 4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8.2-8 8.2z"/></svg>
          WhatsApp
        </a>
        <a class="enlace-social enlace-social--telegram" href="https://t.me/+50689249251" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.9 4.6 18.6 20c-.2.9-.9 1.1-1.6.7l-4.6-3.4-2.2 2.1c-.2.2-.4.4-.9.4l.3-4.6L18 7.5c.4-.3-.1-.5-.6-.2L6.7 13.9l-4.5-1.4c-1-.3-1-1 .2-1.4L20.6 3.6c.8-.3 1.5.2 1.3 1z"/></svg>
          Telegram
        </a>
        <a class="enlace-social" href="mailto:contacto@bitacora.cr">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/></svg>
          contacto@bitacora.cr
        </a>
      </div>
    </div>
  </section>

</main>

<a class="whatsapp-flotante" href="https://wa.me/50689249251?text=Hola%2C%20quisiera%20informaci%C3%B3n%20sobre%20la%20evaluaci%C3%B3n%20de%20riesgo%20ISO%2FIEC%2027002" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.6.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5s-.6-1.5-.9-2c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.3-.1-.2-.3-.2-.6-.4z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.9.5 3.6 1.4 5.1L2 22l5-1.3c1.4.8 3.1 1.3 4.9 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3C4.4 15 4 13.5 4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8.2-8 8.2z"/></svg>
</a>

<footer class="pie">
  <div class="contenedor pie__interior">
    <p class="pie__marca">DataSolutions CR — Consultoría en bases de datos</p>
    <p>Universidad Nacional, Costa Rica · Administración de Bases de Datos · 2026</p>
    <div class="pie__social">
      <a href="https://wa.me/50689249251?text=Hola%2C%20quisiera%20informaci%C3%B3n%20sobre%20la%20evaluaci%C3%B3n%20de%20riesgo%20ISO%2FIEC%2027002" target="_blank" rel="noopener" aria-label="WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.6.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5s-.6-1.5-.9-2c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.3-.1-.2-.3-.2-.6-.4z"/><path d="M12 2C6.5 2 2 6.5 2 12c0 1.9.5 3.6 1.4 5.1L2 22l5-1.3c1.4.8 3.1 1.3 4.9 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3C4.4 15 4 13.5 4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8.2-8 8.2z"/></svg>
      </a>
      <a href="https://t.me/+50689249251" target="_blank" rel="noopener" aria-label="Telegram">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.9 4.6 18.6 20c-.2.9-.9 1.1-1.6.7l-4.6-3.4-2.2 2.1c-.2.2-.4.4-.9.4l.3-4.6L18 7.5c.4-.3-.1-.5-.6-.2L6.7 13.9l-4.5-1.4c-1-.3-1-1 .2-1.4L20.6 3.6c.8-.3 1.5.2 1.3 1z"/></svg>
      </a>
      <a href="mailto:contacto@bitacora.cr" aria-label="Correo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/></svg>
      </a>
    </div>
  </div>
</footer>

<script>
(function () {
  var reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var lineas = document.querySelectorAll('.log__lineas li');
  if (reducido) {
    lineas.forEach(function (l) { l.classList.add('visible'); });
    return;
  }
  lineas.forEach(function (linea, i) {
    setTimeout(function () { linea.classList.add('visible'); }, 350 + i * 420);
  });
})();

// Aparición suave de secciones al hacer scroll.
(function () {
  var secciones = document.querySelectorAll('.aparece');
  if (! secciones.length) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || ! ('IntersectionObserver' in window)) {
    secciones.forEach(function (s) { s.classList.add('visible'); });
    return;
  }
  var observador = new IntersectionObserver(function (entradas) {
    entradas.forEach(function (entrada) {
      if (entrada.isIntersecting) {
        entrada.target.classList.add('visible');
        observador.unobserve(entrada.target);
      }
    });
  }, { threshold: 0.12 });
  secciones.forEach(function (s) { observador.observe(s); });
})();
</script>

</body>
</html>
