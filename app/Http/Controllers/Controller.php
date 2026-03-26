<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Application;
use App\Core\Response;
use App\Core\View;
use App\Services\AuthService;
use App\Services\PageService;
use App\Services\PluginManager;
use App\Services\SettingsService;

abstract class Controller
{
    public function __construct(protected readonly Application $app)
    {
    }

    protected function render(string $view, array $data = [], string $layout = 'layout/app'): Response
    {
        $payload = array_merge($this->publicSharedData(), $data);

        return Response::html($this->app->make(View::class)->render($view, $payload, $layout));
    }

    protected function renderAdmin(string $view, array $data = [], string $layout = 'layout/admin'): Response
    {
        $payload = array_merge($this->adminSharedData(), $data);

        return Response::html($this->app->make(View::class)->render($view, $payload, $layout));
    }

    protected function redirect(string $path, ?string $success = null, ?string $error = null): Response
    {
        if ($success !== null) {
            session()->flash('success', $success);
        }

        if ($error !== null) {
            session()->flash('error', $error);
        }

        return Response::redirect(url(ltrim($path, '/')));
    }

    protected function back(string $fallback = '/', ?string $error = null): Response
    {
        if ($error !== null) {
            session()->flash('error', $error);
        }

        $target = $this->safeBackUrl(request()?->server('HTTP_REFERER'), $fallback);

        return Response::redirect((string) $target);
    }

    protected function validationRedirect(array $errors, array $input, string $fallback): Response
    {
        session()->flashErrors($errors);
        session()->flashInput($input);

        return $this->back($fallback);
    }

    protected function pullOldInput(): array
    {
        $old = session()->old('*', []);

        return is_array($old) ? $old : [];
    }

    protected function publicSharedData(): array
    {
        return [
            'siteSettings' => $this->app->make(SettingsService::class)->values(),
            'navigation' => $this->app->make(PageService::class)->navigation(),
            'pluginManager' => $this->app->make(PluginManager::class),
        ];
    }

    protected function adminSharedData(): array
    {
        return [
            'siteSettings' => $this->app->make(SettingsService::class)->values(),
            'currentUser' => $this->app->make(AuthService::class)->user(),
            'adminNavigation' => $this->adminNavigation(),
        ];
    }

    protected function adminNavigation(): array
    {
        $items = [
            ['label' => 'Dashboard', 'url' => '/admin/', 'icon' => 'fa-solid fa-gauge'],
            ['label' => 'Ustawienia strony', 'url' => '/admin/settings/', 'icon' => 'fa-solid fa-sliders'],
            ['label' => 'Strony', 'url' => '/admin/pages/', 'icon' => 'fa-solid fa-file-lines'],
            ['label' => 'Wtyczki', 'url' => '/admin/plugins/', 'icon' => 'fa-solid fa-puzzle-piece'],
            ['label' => 'Użytkownicy', 'url' => '/admin/users/', 'icon' => 'fa-solid fa-users'],
        ];

        foreach ($this->app->make(PluginManager::class)->adminMenuItems() as $item) {
            $items[] = [
                'label' => $item['label'],
                'url' => $item['url'],
                'icon' => $item['icon'] ?? 'fa-solid fa-circle',
            ];
        }

        $items[] = [
            'label' => 'Wyloguj',
            'url' => '/admin/logout/',
            'method' => 'POST',
            'icon' => 'fa-solid fa-right-from-bracket',
        ];

        return $items;
    }

    private function safeBackUrl(mixed $referer, string $fallback): string
    {
        $fallbackUrl = url(ltrim($fallback, '/'));

        if (!is_string($referer) || trim($referer) === '') {
            return $fallbackUrl;
        }

        $refererParts = parse_url($referer);
        $appUrlParts = parse_url((string) config('app.url', ''));

        if ($refererParts === false || $appUrlParts === false) {
            return $fallbackUrl;
        }

        $refererHost = strtolower((string) ($refererParts['host'] ?? ''));
        $appHost = strtolower((string) ($appUrlParts['host'] ?? ''));

        if ($refererHost === '' || $appHost === '' || $refererHost !== $appHost) {
            return $fallbackUrl;
        }

        $path = (string) ($refererParts['path'] ?? '/');
        $query = isset($refererParts['query']) ? '?' . $refererParts['query'] : '';
        $fragment = isset($refererParts['fragment']) ? '#' . $refererParts['fragment'] : '';

        return url(ltrim($path, '/')) . $query . $fragment;
    }
}
