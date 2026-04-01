<?php

declare(strict_types=1);

namespace App\Deployment;

class DeployPackageManager
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function build(string $reason = 'manual'): array
    {
        $startedAt = date(DATE_ATOM);

        try {
            $result = (new DeployPackageBuilder($this->basePath))->build();
            $status = [
                'status' => 'success',
                'reason' => $reason,
                'build_started_at' => $startedAt,
                'build_finished_at' => date(DATE_ATOM),
                'generated_at' => $result['generated_at'] ?? '',
                'install_key' => $result['install_key'] ?? '',
                'path' => $result['path'] ?? ($this->basePath . '/deploy'),
                'error' => '',
            ];
        } catch (\Throwable $exception) {
            $status = [
                'status' => 'error',
                'reason' => $reason,
                'build_started_at' => $startedAt,
                'build_finished_at' => date(DATE_ATOM),
                'generated_at' => '',
                'install_key' => '',
                'path' => $this->basePath . '/deploy',
                'error' => $exception->getMessage(),
            ];

            $this->writeStatus($status);

            throw $exception;
        }

        $this->writeStatus($status);

        return $status;
    }

    public function latestStatus(): array
    {
        $path = $this->statusPath();

        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function writeStatus(array $status): void
    {
        $path = $this->statusPath();
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($concurrentDirectory = $directory, 0777, true) && !is_dir($concurrentDirectory)) {
            return;
        }

        $json = json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (!is_string($json)) {
            return;
        }

        file_put_contents($path, $json . PHP_EOL);
    }

    private function statusPath(): string
    {
        return $this->basePath . '/storage/app/deploy-build-status.json';
    }
}
