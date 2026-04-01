<?php

declare(strict_types=1);

use App\Deployment\DeploymentSynchronizer;
use App\Deployment\DeployPackageManager;
use App\Deployment\WebInstaller;
use App\Core\Database;
use App\Core\Request;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$installer = new WebInstaller(BASE_PATH);

if ($installer->shouldHandleRequest()) {
    $installer->handle();

    return;
}

(new DeploymentSynchronizer(BASE_PATH))->runIfNeeded();

$app = require BASE_PATH . '/bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);

$isAdminWrite = str_starts_with($request->path(), '/admin/') && $request->method() !== 'GET';
$database = $app->make(Database::class);

if ($isAdminWrite && $database->hasPendingMutations()) {
    try {
        $app->make(DeployPackageManager::class)->build('admin-request');
    } catch (\Throwable) {
        // CMS save should continue even if deploy packaging needs attention.
    } finally {
        $database->clearPendingMutations();
    }
}

$response->send();
