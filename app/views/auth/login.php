<?php use App\Core\Csrf; ?>
<main class="auth-shell">
    <section class="auth-card">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="brand-mark">DS</span>
            <div>
                <h1 class="h4 mb-0">DataSolutions CR</h1>
                <div class="text-muted">Sistema de auditoria ISO 27002</div>
            </div>
        </div>

        <form method="post" action="<?= BASE_URL ?>/login" autocomplete="on">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <div class="mb-3">
                <label class="form-label" for="email">Correo electronico</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= old('email') ?>" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Contrasena</label>
                <input class="form-control" id="password" name="password" type="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Iniciar sesion</button>
        </form>
        <p class="text-muted small mb-0 mt-4">Usuario inicial: admin@datasolutionscr.net / Admin123*</p>
    </section>
</main>
