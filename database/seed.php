<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';

$seeder = new \Database\Seeders\DatabaseSeeder();
$seeder->run($app);

echo 'Seeding completed.' . PHP_EOL;
