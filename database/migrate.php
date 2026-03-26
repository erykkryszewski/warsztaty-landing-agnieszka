<?php

declare(strict_types=1);

use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';
$database = $app->make(Database::class);
$pdo = $database->pdo();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        batch INT NOT NULL,
        migrated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = $database->fetchAll('SELECT migration FROM migrations');
$appliedNames = array_column($applied, 'migration');
$files = glob(BASE_PATH . '/database/migrations/*.php') ?: [];
sort($files);

$batch = ((int) ($database->fetch('SELECT MAX(batch) AS batch FROM migrations')['batch'] ?? 0)) + 1;

foreach ($files as $file) {
    $migration = require $file;
    $name = $migration['name'];

    if (in_array($name, $appliedNames, true)) {
        continue;
    }

    echo "Running {$name}..." . PHP_EOL;

    try {
        foreach ($migration['sql'] as $statement) {
            $pdo->exec($statement);
        }

        $database->statement(
            'INSERT INTO migrations (migration, batch, migrated_at) VALUES (:migration, :batch, NOW())',
            ['migration' => $name, 'batch' => $batch]
        );
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}

echo 'Migrations completed.' . PHP_EOL;
