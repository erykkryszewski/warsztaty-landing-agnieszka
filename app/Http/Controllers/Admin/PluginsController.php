<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\PluginManager;

class PluginsController extends Controller
{
    public function index(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/plugins/index', [
            'plugins' => $this->app->make(PluginManager::class)->all(),
        ]);
    }

    public function toggle(Request $request, string $pluginKey): \App\Core\Response
    {
        $enabled = $request->input('enabled') === '1';
        $this->app->make(PluginManager::class)->setEnabled($pluginKey, $enabled);

        return $this->redirect('/admin/plugins/', 'Zapisano stan wtyczki.');
    }
}
