<?php

declare(strict_types=1);

use App\Database\SeedRunner;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';
(new SeedRunner())->run($app);

echo 'Seeding completed.' . PHP_EOL;
