<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class SettingsRegistry
{
    private array $sections = [];

    public function __construct(Application $app)
    {
        foreach ($app->config('settings', []) as $section) {
            $this->sections[$section['key']] = $section;
        }
    }

    public function all(): array
    {
        return array_values($this->sections);
    }

    public function find(string $key): ?array
    {
        return $this->sections[$key] ?? null;
    }

    public function registerSection(array $section): void
    {
        $this->sections[$section['key']] = $section;
    }
}
