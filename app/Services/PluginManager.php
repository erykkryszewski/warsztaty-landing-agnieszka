<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;
use App\Core\Router;
use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;
use PDOException;

class PluginManager
{
    private array $definitions = [];

    private array $adminMenuItems = [];

    public function __construct(private readonly Application $app)
    {
    }

    public function boot(): void
    {
        $states = $this->loadStates();
        $api = new PluginApi($this, $this->app->make(SettingsRegistry::class), $this->app->make(PageRegistry::class));

        foreach ($this->configuredPlugins() as $className) {
            /** @var PluginInterface $plugin */
            $plugin = new $className();
            $definition = $plugin->definition();
            $key = $definition['key'];
            $enabled = (bool) ($definition['required'] ?? false);

            if (!$enabled) {
                $enabled = array_key_exists($key, $states)
                    ? (bool) $states[$key]['is_enabled']
                    : (bool) ($definition['enabled_by_default'] ?? false);
            }

            $definition['enabled'] = $enabled;
            $this->definitions[$key] = $definition;

            if ($enabled) {
                $plugin->register($api);
            }
        }
    }

    public function all(): array
    {
        $plugins = array_values($this->definitions);
        usort($plugins, static fn (array $left, array $right): int => strcmp($left['name'], $right['name']));

        return $plugins;
    }

    public function setEnabled(string $pluginKey, bool $enabled): void
    {
        $plugin = $this->definitions[$pluginKey] ?? null;

        if ($plugin === null) {
            throw new \RuntimeException('Plugin not found.');
        }

        if ((bool) ($plugin['required'] ?? false)) {
            return;
        }

        $this->app->make(\App\Models\PluginStateModel::class)->upsert($pluginKey, $enabled);
    }

    public function registerAdminMenu(array $item): void
    {
        $this->adminMenuItems[] = $item;
    }

    public function adminMenuItems(): array
    {
        $items = $this->adminMenuItems;
        usort($items, static fn (array $left, array $right): int => ($left['order'] ?? 50) <=> ($right['order'] ?? 50));

        return $items;
    }

    public function renderSlot(string $pageKey, string $slot, array $context = []): string
    {
        $partials = $this->app->make(PageRegistry::class)->slotPartials($pageKey, $slot);

        if ($partials === []) {
            return '';
        }

        $html = '';

        foreach ($partials as $partial) {
            $page = $context['page'] ?? [];
            $groupKey = $partial['group_key'];
            $html .= $this->app->make(\App\Core\View::class)->partial($partial['view'], array_merge($context, [
                'pluginContent' => $page['content'][$groupKey] ?? [],
            ]));
        }

        return $html;
    }

    public function router(): Router
    {
        return $this->app->make(Router::class);
    }

    private function configuredPlugins(): array
    {
        return $this->app->config('plugins', []);
    }

    private function loadStates(): array
    {
        try {
            return $this->app->make(\App\Models\PluginStateModel::class)->allIndexed();
        } catch (PDOException|\RuntimeException) {
            return [];
        }
    }
}
