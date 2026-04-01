<?php

declare(strict_types=1);

use App\Database\SeedRunner;
use App\Core\Database;
use App\Deployment\DeployPackageManager;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';
(new SeedRunner())->run($app);

$database = $app->make(Database::class);

if ($database->hasPendingMutations()) {
    try {
        $app->make(DeployPackageManager::class)->build('cli-seed');
    } finally {
        $database->clearPendingMutations();
    }
}

echo 'Seeding completed.' . PHP_EOL;
