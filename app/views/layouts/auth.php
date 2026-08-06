<?php use App\Core\Flash; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Sistema de auditoria') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/app.css" rel="stylesheet">
</head>
<body>
    <?php $flashMessages = Flash::all(); ?>
    <?php foreach ($flashMessages as $type => $messages): ?>
        <div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1080; width: min(92%, 430px);">
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= e($type) ?> shadow-sm mb-2"><?= e($message) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <?= $content ?>
</body>
</html>
