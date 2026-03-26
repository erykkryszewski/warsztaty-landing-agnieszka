<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class PageRegistry
{
    private array $pages = [];

    private array $slotPartials = [];

    public function __construct(Application $app)
    {
        foreach ($app->config('pages', []) as $page) {
            $this->pages[$page['key']] = $page;
        }
    }

    public function all(): array
    {
        return array_values($this->pages);
    }

    public function find(string $key): ?array
    {
        return $this->pages[$key] ?? null;
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->pages as $page) {
            if (rtrim($page['slug'], '/') === rtrim($slug, '/')) {
                return $page;
            }
        }

        return null;
    }

    public function registerGroup(string $pageKey, array $group, ?string $slot = null, ?string $view = null): void
    {
        if (!isset($this->pages[$pageKey])) {
            throw new \RuntimeException(sprintf('Page [%s] is not defined.', $pageKey));
        }

        $this->pages[$pageKey]['groups'][] = $group;

        if ($slot !== null && $view !== null) {
            $this->slotPartials[$pageKey][$slot][] = [
                'group_key' => $group['key'],
                'view' => $view,
            ];
        }
    }

    public function slotPartials(string $pageKey, string $slot): array
    {
        return $this->slotPartials[$pageKey][$slot] ?? [];
    }
}
