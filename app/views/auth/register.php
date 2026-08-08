<?php use App\Core\Csrf; ?>
<main class="auth-shell">
    <section class="auth-card">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="brand-mark">DS</span>
            <div>
                <h1 class="h4 mb-0">Crear cuenta</h1>
                <div class="text-muted">Autoevalúe su organización con la metodología ISO/IEC 27002</div>
            </div>
        </div>

        <form method="post" action="<?= BASE_URL ?>/register" autocomplete="on">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <div class="mb-3">
                <label class="form-label" for="organization_name">Nombre de su organización</label>
                <input class="form-control" id="organization_name" name="organization_name"
                       value="<?= old('organization_name') ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="name">Su nombre</label>
                <input class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Correo electrónico</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Contraseña</label>
                <input class="form-control" id="password" name="password" type="password" minlength="8" required>
                <div class="form-text">Mínimo 8 caracteres.</div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Crear cuenta</button>
        </form>
        <p class="text-muted small mb-0 mt-4">
            ¿Ya tiene cuenta? <a href="<?= BASE_URL ?>/login">Inicie sesión</a>
        </p>
        <p class="text-muted small mb-0 mt-1">
            Al registrarse queda como auditor de su propia organización: usted responde el
            cuestionario de forma independiente, sin ver información de otras organizaciones.
        </p>
    </section>
</main>
