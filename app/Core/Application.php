<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class Application
{
    private static ?self $instance = null;

    private array $bindings = [];

    private array $resolved = [];

    private array $config = [];

    private ?Request $request = null;

    public function __construct(private readonly string $basePath)
    {
        self::$instance = $this;
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Application has not been bootstrapped.');
        }

        return self::$instance;
    }

    public function basePath(string $path = ''): string
    {
        if ($path === '') {
            return $this->basePath;
        }

        return $this->basePath . '/' . ltrim($path, '/');
    }

    public function loadConfig(array $files): void
    {
        foreach ($files as $name => $path) {
            $this->config[$name] = require $path;
        }
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function singleton(string $key, callable $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    public function instanceBind(string $key, mixed $value): void
    {
        $this->resolved[$key] = $value;
    }

    public function make(string $key): mixed
    {
        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }

        if (!array_key_exists($key, $this->bindings)) {
            throw new \RuntimeException(sprintf('Service [%s] is not registered.', $key));
        }

        $this->resolved[$key] = $this->bindings[$key]($this);

        return $this->resolved[$key];
    }

    public function request(): ?Request
    {
        return $this->request;
    }

    public function handle(Request $request): Response
    {
        $this->request = $request;

        try {
            return $this->make(Router::class)->dispatch($request);
        } catch (HttpException $exception) {
            return Response::html(
                $this->make(View::class)->render('pages/errors/' . $exception->statusCode(), array_merge($this->errorSharedData(), [
                    'title' => $exception->getMessage(),
                    'message' => $exception->getMessage(),
                ]), 'layout/app'),
                $exception->statusCode()
            );
        } catch (Throwable $exception) {
            if ((bool) $this->config('app.debug', false)) {
                $content = '<h1>Application error</h1><pre>' . e($exception) . '</pre>';
            } else {
                $content = $this->make(View::class)->render('pages/errors/500', array_merge($this->errorSharedData(), [
                    'title' => 'Wystąpił błąd',
                    'message' => 'Wystąpił nieoczekiwany błąd aplikacji.',
                ]), 'layout/app');
            }

            return Response::html($content, 500);
        }
    }

    private function errorSharedData(): array
    {
        try {
            return [
                'siteSettings' => $this->make(\App\Services\SettingsService::class)->values(),
                'navigation' => $this->make(\App\Services\PageService::class)->navigation(),
                'pluginManager' => $this->make(\App\Services\PluginManager::class),
            ];
        } catch (Throwable) {
            return [
                'siteSettings' => [],
                'navigation' => [],
                'pluginManager' => null,
            ];
        }
    }
}
