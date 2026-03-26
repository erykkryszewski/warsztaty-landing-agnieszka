<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'ER Coding Mini CMS'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Warsaw'),
    'locale' => env('APP_LOCALE', 'pl_PL'),
];
