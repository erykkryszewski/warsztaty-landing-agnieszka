<?php

declare(strict_types=1);

namespace App\Plugins\Blog;

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\BlogPostController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\VerifyCsrfMiddleware;
use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;

class Plugin implements PluginInterface
{
    public function definition(): array
    {
        return [
            'key' => 'blog',
            'name' => 'Blog',
            'description' => 'Prosty moduł bloga z widokiem publicznym i CRUD-em w panelu.',
            'required' => true,
            'enabled_by_default' => true,
        ];
    }

    public function register(PluginApi $api): void
    {
        $api->get('/blog/{slug}/', [BlogPostController::class, 'show']);

        $api->adminMenu('Blog', '/admin/blog/', 40);

        $api->get('/admin/blog/', [BlogController::class, 'index'], [AuthMiddleware::class]);
        $api->get('/admin/blog/create/', [BlogController::class, 'create'], [AuthMiddleware::class]);
        $api->post('/admin/blog/create/', [BlogController::class, 'store'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);
        $api->get('/admin/blog/{id}/edit/', [BlogController::class, 'edit'], [AuthMiddleware::class]);
        $api->post('/admin/blog/{id}/edit/', [BlogController::class, 'update'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);
        $api->post('/admin/blog/{id}/delete/', [BlogController::class, 'delete'], [AuthMiddleware::class, VerifyCsrfMiddleware::class]);
    }
}
