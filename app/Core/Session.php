<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function invalidate(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public function flashErrors(array $errors): void
    {
        $_SESSION['_flash']['errors'] = $errors;
    }

    public function flashError(string $key): ?string
    {
        $errors = $_SESSION['_flash']['errors'] ?? [];

        if (!array_key_exists($key, $errors)) {
            return null;
        }

        $message = $errors[$key];
        unset($_SESSION['_flash']['errors'][$key]);

        if ($_SESSION['_flash']['errors'] === []) {
            unset($_SESSION['_flash']['errors']);
        }

        return $message;
    }

    public function hasFlashErrors(string $key): bool
    {
        $errors = $_SESSION['_flash']['errors'] ?? [];

        return array_key_exists($key, $errors);
    }

    public function flashInput(array $input): void
    {
        $_SESSION['_old_input'] = $input;
    }

    public function old(string $key, mixed $default = null): mixed
    {
        if ($key === '*') {
            $value = $_SESSION['_old_input'] ?? $default;
            unset($_SESSION['_old_input']);

            return $value;
        }

        $value = $_SESSION['_old_input'][$key] ?? $default;
        unset($_SESSION['_old_input'][$key]);

        if (isset($_SESSION['_old_input']) && $_SESSION['_old_input'] === []) {
            unset($_SESSION['_old_input']);
        }

        return $value;
    }
}
