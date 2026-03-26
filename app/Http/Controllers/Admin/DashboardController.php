<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\PageService;
use App\Services\PluginManager;
use App\Services\PostService;
use App\Services\UserService;

class DashboardController extends Controller
{
    public function index(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/dashboard', [
            'stats' => [
                'pages' => count($this->app->make(PageService::class)->all()),
                'posts' => count($this->app->make(PostService::class)->adminList()),
                'plugins' => count($this->app->make(PluginManager::class)->all()),
                'users' => count($this->app->make(UserService::class)->all()),
            ],
        ]);
    }
}
