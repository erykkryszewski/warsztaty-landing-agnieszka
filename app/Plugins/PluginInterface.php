<?php

declare(strict_types=1);

namespace App\Plugins;

interface PluginInterface
{
    public function definition(): array;

    public function register(PluginApi $api): void;
}
