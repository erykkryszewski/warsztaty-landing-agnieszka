<?php

declare(strict_types=1);

use App\Deployment\WebInstaller;
use App\Core\Request;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$installer = new WebInstaller(BASE_PATH);

if ($installer->shouldHandleRequest()) {
    $installer->handle();

    return;
}

$app = require BASE_PATH . '/bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);

$response->send();
