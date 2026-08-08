<?php
declare(strict_types=1);

require __DIR__ . '/seed_maturity_scale.php';

$config = require __DIR__ . '/../config/database.php';
$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']);
$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$statement = $pdo->prepare(
    'UPDATE question_maturity_scale SET description = :description WHERE question_id = :question_id AND level = :level'
);

$data = maturity_scale_seed();
$updated = 0;
foreach ($data as $questionId => $levels) {
    foreach ($levels as $level => $description) {
        $statement->execute([
            'description' => $description,
            'question_id' => $questionId,
            'level'       => $level,
        ]);
        $updated += $statement->rowCount();
    }
}

echo "Filas actualizadas: {$updated}\n";
