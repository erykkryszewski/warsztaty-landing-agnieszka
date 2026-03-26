<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query = [],
        private readonly array $request = [],
        private readonly array $files = [],
        private readonly array $server = []
    ) {
    }

    public static function capture(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

        // Handle root .htaccess rewrite: SCRIPT_NAME has /public but REQUEST_URI does not
        if (str_ends_with($basePath, '/public') && !str_contains($path, '/public/') && !str_starts_with($path, $basePath)) {
            $basePath = substr($basePath, 0, -7);
        }

        if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            $_GET,
            $_POST,
            self::normalizeUploadedFiles($_FILES),
            $_SERVER
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function input(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->request;
        }

        return $this->request[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->request;
    }

    public function file(string $key = null): mixed
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    public function server(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    public function userAgent(): string
    {
        return (string) ($this->server['HTTP_USER_AGENT'] ?? '');
    }

    public static function normalizeUploadedFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $field => $data) {
            $normalized[$field] = self::normalizeUploadedFileBranch($data);
        }

        return $normalized;
    }

    private static function normalizeUploadedFileBranch(array $data): array
    {
        if (!is_array($data['name'])) {
            return $data;
        }

        $files = [];

        foreach ($data['name'] as $key => $value) {
            $files[$key] = self::walkUploadedFileNode([
                'name' => $data['name'][$key],
                'type' => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'error' => $data['error'][$key],
                'size' => $data['size'][$key],
            ]);
        }

        return $files;
    }

    private static function walkUploadedFileNode(array $node): array
    {
        if (!is_array($node['name'])) {
            return $node;
        }

        $files = [];

        foreach ($node['name'] as $key => $value) {
            $files[$key] = self::walkUploadedFileNode([
                'name' => $node['name'][$key],
                'type' => $node['type'][$key],
                'tmp_name' => $node['tmp_name'][$key],
                'error' => $node['error'][$key],
                'size' => $node['size'][$key],
            ]);
        }

        return $files;
    }
}
