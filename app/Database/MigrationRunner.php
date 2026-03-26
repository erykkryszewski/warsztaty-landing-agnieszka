<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Application;
use App\Core\Database;
use Throwable;

class MigrationRunner
{
    public function run(Application $app): void
    {
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
        $files = glob($app->basePath('database/migrations/*.php')) ?: [];
        sort($files);

        $batch = ((int) ($database->fetch('SELECT MAX(batch) AS batch FROM migrations')['batch'] ?? 0)) + 1;

        foreach ($files as $file) {
            $migration = require $file;
            $name = $migration['name'];

            if (in_array($name, $appliedNames, true)) {
                continue;
            }

            foreach ($migration['sql'] as $statement) {
                try {
                    $pdo->exec($statement);
                } catch (Throwable $exception) {
                    throw $exception;
                }
            }

            $database->statement(
                'INSERT INTO migrations (migration, batch, migrated_at) VALUES (:migration, :batch, NOW())',
                ['migration' => $name, 'batch' => $batch]
            );
        }
    }
}
