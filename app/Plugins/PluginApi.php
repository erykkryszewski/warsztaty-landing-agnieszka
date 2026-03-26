<?php

declare(strict_types=1);

namespace App\Plugins;

use App\Services\PageRegistry;
use App\Services\PluginManager;
use App\Services\SettingsRegistry;

class PluginApi
{
    public function __construct(
        private readonly PluginManager $manager,
        private readonly SettingsRegistry $settings,
        private readonly PageRegistry $pages
    ) {
    }

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->manager->router()->get($path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->manager->router()->post($path, $handler, $middleware);
    }

    public function adminMenu(string $label, string $url, int $order = 50): void
    {
        $this->manager->registerAdminMenu([
            'label' => $label,
            'url' => $url,
            'order' => $order,
        ]);
    }

    public function settingsSection(array $section): void
    {
        $this->settings->registerSection($section);
    }

    public function extendPage(string $pageKey, array $group, ?string $slot = null, ?string $view = null): void
    {
        $this->pages->registerGroup($pageKey, $group, $slot, $view);
    }
}
