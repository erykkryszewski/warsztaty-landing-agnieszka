<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class RateLimiterService
{
    public function __construct(private readonly Application $app)
    {
    }

    public function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $attempts = $this->loadAttempts($key, $decaySeconds);

        return count($attempts) >= $maxAttempts;
    }

    public function hit(string $key, int $decaySeconds): int
    {
        $attempts = $this->loadAttempts($key, $decaySeconds);
        $attempts[] = time();
        $this->storeAttempts($key, $attempts);

        return count($attempts);
    }

    public function clear(string $key): void
    {
        $path = $this->pathForKey($key);

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function loadAttempts(string $key, int $decaySeconds): array
    {
        $path = $this->pathForKey($key);

        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        $attempts = is_array($decoded['attempts'] ?? null) ? $decoded['attempts'] : [];
        $threshold = time() - $decaySeconds;

        return array_values(array_filter($attempts, static fn (mixed $timestamp): bool => is_int($timestamp) && $timestamp >= $threshold));
    }

    private function storeAttempts(string $key, array $attempts): void
    {
        $directory = $this->app->basePath('storage/cache/rate-limits');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->pathForKey($key), json_encode([
            'attempts' => array_values($attempts),
        ], JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    private function pathForKey(string $key): string
    {
        return $this->app->basePath('storage/cache/rate-limits/' . hash('sha256', $key) . '.json');
    }
}
