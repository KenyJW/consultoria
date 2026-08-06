<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;

$currentUser   = Auth::user();
$flashMessages = Flash::all();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Sistema de auditoría') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-12 col-lg-2 sidebar p-3">
            <div class="d-flex align-items-center gap-2 text-white mb-4">
                <span class="brand-mark">DS</span>
                <strong>Auditoría BD</strong>
            </div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link" href="<?= BASE_URL ?>/dashboard">Dashboard</a>
                <a class="nav-link" href="<?= BASE_URL ?>/organizations">Organizaciones</a>
                <a class="nav-link" href="<?= BASE_URL ?>/areas">Áreas</a>
                <a class="nav-link" href="<?= BASE_URL ?>/users">Usuarios</a>
                <a class="nav-link" href="<?= BASE_URL ?>/domains">Dominios ISO</a>
                <a class="nav-link" href="<?= BASE_URL ?>/controls">Controles</a>
                <a class="nav-link" href="<?= BASE_URL ?>/questions">Preguntas</a>
                <a class="nav-link" href="<?= BASE_URL ?>/audits">Auditorías</a>
                <a class="nav-link" href="<?= BASE_URL ?>/recommendations">Recomendaciones</a>
                <a class="nav-link" href="<?= BASE_URL ?>/comparison">Comparación histórica</a>
            </nav>
        </aside>
        <main class="col-12 col-lg-10 p-4">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1"><?= e($title ?? '') ?></h1>
                    <div class="text-muted">ISO/IEC 27002 · Seguridad de bases de datos</div>
                </div>
                <form method="post" action="<?= BASE_URL ?>/logout">
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                    <span class="me-3 text-muted"><?= e($currentUser['name'] ?? '') ?></span>
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Salir</button>
                </form>
            </header>
            <?php foreach ($flashMessages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
