<?php

declare(strict_types=1);

use App\Core\Request;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);

$response->send();
