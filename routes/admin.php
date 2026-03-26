<?php

declare(strict_types=1);

use App\Core\Router;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\PluginsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\GuestMiddleware;
use App\Http\Middleware\VerifyCsrfMiddleware;

$router = $app->make(Router::class);

$router->get('/admin/', [DashboardController::class, 'index'], [AuthMiddleware::class]);

$router->get('/admin/login/', [AuthController::class, 'loginForm'], [GuestMiddleware::class]);
$router->post('/admin/login/', [AuthController::class, 'login'], [GuestMiddleware::class, VerifyCsrfMiddleware::class]);
$router->post('/admin/logout/', [AuthController::class, 'logout'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);

$router->get('/admin/settings/', [SettingsController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/settings/', [SettingsController::class, 'update'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);

$router->get('/admin/pages/', [PagesController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/pages/{pageKey}/', [PagesController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/pages/{pageKey}/', [PagesController::class, 'update'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);

$router->get('/admin/plugins/', [PluginsController::class, 'index'], [AuthMiddleware::class]);
$router->post('/admin/plugins/{pluginKey}/toggle/', [PluginsController::class, 'toggle'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);

$router->get('/admin/users/', [UsersController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/users/create/', [UsersController::class, 'create'], [AuthMiddleware::class]);
$router->post('/admin/users/create/', [UsersController::class, 'store'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);
$router->get('/admin/users/{id}/edit/', [UsersController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/users/{id}/edit/', [UsersController::class, 'update'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);
