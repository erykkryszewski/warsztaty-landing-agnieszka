<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Env;
use App\Core\Request;
use App\Core\Session;

if (!function_exists('app')) {
    function app(?string $key = null): mixed
    {
        $application = Application::instance();

        if ($key === null) {
            return $application;
        }

        return $application->make($key);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return app()->config($key, $default);
    }
}

if (!function_exists('request')) {
    function request(): ?Request
    {
        return app()->request();
    }
}

if (!function_exists('session')) {
    function session(): Session
    {
        return app(Session::class);
    }
}

if (!function_exists('e')) {
    function string_value(mixed $value): string
    {
        if (!is_array($value)) {
            return is_scalar($value) ? (string) $value : '';
        }

        if ($value === []) {
            return '';
        }

        foreach (['text', 'title', 'quote', 'author', 'label', 'url', 'name', 'body', 'lead', 'role', 'alt'] as $preferredKey) {
            if (array_key_exists($preferredKey, $value)) {
                return string_value($value[$preferredKey]);
            }
        }

        if (array_is_list($value)) {
            return string_value($value[0] ?? '');
        }

        return string_value(reset($value));
    }

    function e(mixed $value): string
    {
        return htmlspecialchars(string_value($value), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');
        $path = ltrim($path, '/');

        return $path === '' ? $base : $base . '/' . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $clean = ltrim($path, '/');
        $filePath = BASE_PATH . '/public/' . $clean;
        $version = is_file($filePath) ? filemtime($filePath) : '';

        return url($clean) . ($version !== '' ? '?v=' . $version : '');
    }
}

if (!function_exists('content_image')) {
    function content_image(mixed $value): string
    {
        $candidate = trim(string_value($value));

        if ($candidate === '') {
            return '';
        }

        if (str_starts_with($candidate, 'http')) {
            return $candidate;
        }

        return asset(ltrim($candidate, '/'));
    }
}

if (!function_exists('content_image_dimensions')) {
    function content_image_dimensions(mixed $value, int $fallbackWidth = 1600, int $fallbackHeight = 900): array
    {
        $candidate = trim(string_value($value));

        if ($candidate === '' || str_starts_with($candidate, 'http')) {
            return ['width' => $fallbackWidth, 'height' => $fallbackHeight];
        }

        $path = app()->basePath('public/' . ltrim($candidate, '/'));

        if (!is_file($path)) {
            return ['width' => $fallbackWidth, 'height' => $fallbackHeight];
        }

        $size = @getimagesize($path);

        if (!is_array($size) || !isset($size[0], $size[1])) {
            return ['width' => $fallbackWidth, 'height' => $fallbackHeight];
        }

        return ['width' => (int) $size[0], 'height' => (int) $size[1]];
    }
}

if (!function_exists('current_url')) {
    function current_url(): string
    {
        $request = request();

        if ($request === null) {
            return url();
        }

        return url(ltrim($request->path(), '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return app(Csrf::class)->token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $default = null): mixed
    {
        return session()->pullFlash($key, $default);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return session()->old($key, $default);
    }
}

if (!function_exists('has_error')) {
    function has_error(string $key): bool
    {
        return session()->hasFlashErrors($key);
    }
}

if (!function_exists('error_message')) {
    function error_message(string $key): ?string
    {
        return session()->flashError($key);
    }
}

if (!function_exists('selected')) {
    function selected(mixed $value, mixed $expected): string
    {
        return (string) $value === (string) $expected ? 'selected' : '';
    }
}

if (!function_exists('checked')) {
    function checked(bool $condition): string
    {
        return $condition ? 'checked' : '';
    }
}

if (!function_exists('is_active_path')) {
    function is_active_path(string $path): bool
    {
        $request = request();

        if ($request === null) {
            return false;
        }

        return rtrim($request->path(), '/') === rtrim($path, '/');
    }
}

if (!function_exists('nl2p')) {
    function nl2p(mixed $text): string
    {
        $text = string_value($text);

        if ($text === '' || trim($text) === '') {
            return '';
        }

        $paragraphs = preg_split('/\R{2,}/', trim($text)) ?: [];
        $html = array_map(static fn (string $paragraph): string => '<p>' . nl2br(e(trim($paragraph))) . '</p>', $paragraphs);

        return implode('', $html);
    }
}

if (!function_exists('page_group')) {
    function page_group(array $page, string $groupKey): array
    {
        return $page['content'][$groupKey] ?? [];
    }
}

if (!function_exists('field_name')) {
    function field_name(string $prefix, string $name): string
    {
        return $prefix . '[' . $name . ']';
    }
}

if (!function_exists('field_error_key')) {
    function field_error_key(string $prefix, string $name): string
    {
        return trim($prefix . '.' . $name, '.');
    }
}

if (!function_exists('content_link')) {
    function content_link(mixed $value, string $fallback = ''): string
    {
        $candidate = trim(string_value($value));

        if ($candidate === '') {
            $candidate = trim($fallback);
        }

        if ($candidate === '') {
            return '#';
        }

        if (preg_match('/^(https?:|mailto:|tel:)/i', $candidate) === 1) {
            return $candidate;
        }

        if (str_starts_with($candidate, '#') || str_starts_with($candidate, '?')) {
            return $candidate;
        }

        return url(ltrim($candidate, '/'));
    }
}

if (!function_exists('opens_in_new_tab')) {
    function opens_in_new_tab(mixed $value): bool
    {
        return preg_match('/^https?:\/\//i', trim(string_value($value))) === 1;
    }
}

if (!function_exists('plugin_slot')) {
    function plugin_slot(string $pageKey, string $slot, array $context = []): string
    {
        return app(\App\Services\PluginManager::class)->renderSlot($pageKey, $slot, $context);
    }
}
