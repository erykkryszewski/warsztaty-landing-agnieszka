<?php

declare(strict_types=1);

namespace App\Deployment;

use App\Core\Env;
use App\Database\MigrationRunner;
use App\Database\SeedRunner;
use App\Services\PageService;
use App\Services\SettingsService;
use PDO;
use RuntimeException;
use Throwable;

class DeploymentSynchronizer
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function runIfNeeded(): void
    {
        if (!$this->isDeployPackage() || !$this->isInstalled()) {
            return;
        }

        $deployment = $this->deploymentConfig();
        $generatedAt = (string) ($deployment['generated_at'] ?? '');

        if ($generatedAt === '' || !$this->needsSync($deployment)) {
            return;
        }

        $packageId = $this->deploymentIdentifier($deployment);

        try {
            $this->log('START', [
                'package' => $packageId,
                'generated_at' => $generatedAt,
            ]);

            Env::load($this->basePath . '/.env');

            $pdo = $this->connectToDatabase();
            $snapshot = $this->snapshot();

            (new SnapshotRestorer())->restore($pdo, $snapshot);

            $app = require $this->basePath . '/bootstrap/app.php';
            (new MigrationRunner())->run($app);
            (new SeedRunner())->run($app);
            $app->make(SettingsService::class)->repairStoredContent();
            $app->make(PageService::class)->repairStoredContent();

            $this->writeDeployedLock($deployment);
            $this->log('DONE', [
                'package' => $packageId,
                'generated_at' => $generatedAt,
            ]);
        } catch (Throwable $exception) {
            $this->log('ERROR', [
                'package' => $packageId,
                'generated_at' => $generatedAt,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function connectToDatabase(): PDO
    {
        $host = (string) Env::get('DB_HOST', '127.0.0.1');
        $port = (int) Env::get('DB_PORT', 3306);
        $database = (string) Env::get('DB_NAME', '');
        $user = (string) Env::get('DB_USER', '');
        $password = (string) Env::get('DB_PASS', '');
        $charset = (string) Env::get('DB_CHARSET', 'utf8mb4');

        if ($database === '') {
            throw new RuntimeException('DB_NAME is empty. Deployment sync cannot continue.');
        }

        return new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $database, $charset),
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    private function deploymentConfig(): array
    {
        return $this->readPayload(
            $this->basePath . '/storage/app/deployment.json',
            $this->basePath . '/storage/app/deployment.php',
            'Missing deployment metadata.'
        );
    }

    private function snapshot(): array
    {
        return $this->readPayload(
            $this->basePath . '/database/deploy-snapshot.json',
            $this->basePath . '/database/deploy-snapshot.php',
            'Missing deployment snapshot.'
        );
    }

    private function needsSync(array $deployment): bool
    {
        $lockPath = $this->basePath . '/storage/app/deployed.lock';
        $packageHash = trim((string) ($deployment['package_hash'] ?? ''));
        $generatedAt = trim((string) ($deployment['generated_at'] ?? ''));

        if (!is_file($lockPath)) {
            return true;
        }

        $state = json_decode((string) file_get_contents($lockPath), true);
        $appliedHash = is_array($state) ? trim((string) ($state['package_hash'] ?? '')) : '';
        $appliedAt = is_array($state) ? trim((string) ($state['generated_at'] ?? '')) : '';

        if ($packageHash !== '') {
            return $appliedHash !== $packageHash;
        }

        return $appliedAt !== $generatedAt;
    }

    private function writeDeployedLock(array $deployment): void
    {
        $path = $this->basePath . '/storage/app/deployed.lock';
        $contents = json_encode([
            'generated_at' => (string) ($deployment['generated_at'] ?? ''),
            'package_hash' => (string) ($deployment['package_hash'] ?? ''),
            'snapshot_hash' => (string) ($deployment['snapshot_hash'] ?? ''),
            'synced_at' => date(DATE_ATOM),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($path, $contents . PHP_EOL);
    }

    private function isDeployPackage(): bool
    {
        return $this->hasDeploymentMetadata() && $this->hasSnapshot();
    }

    private function isInstalled(): bool
    {
        return is_file($this->basePath . '/.env');
    }

    private function hasDeploymentMetadata(): bool
    {
        return is_file($this->basePath . '/storage/app/deployment.json')
            || is_file($this->basePath . '/storage/app/deployment.php');
    }

    private function hasSnapshot(): bool
    {
        return is_file($this->basePath . '/database/deploy-snapshot.json')
            || is_file($this->basePath . '/database/deploy-snapshot.php');
    }

    private function readPayload(string $jsonPath, string $phpPath, string $missingMessage): array
    {
        if (is_file($jsonPath)) {
            $decoded = json_decode((string) file_get_contents($jsonPath), true);

            if (is_array($decoded)) {
                return $decoded;
            }

            throw new RuntimeException(sprintf('Invalid JSON payload [%s].', $jsonPath));
        }

        if (is_file($phpPath)) {
            /** @var mixed $payload */
            $payload = require $phpPath;

            if (is_array($payload)) {
                return $payload;
            }

            throw new RuntimeException(sprintf('Invalid PHP payload [%s].', $phpPath));
        }

        throw new RuntimeException($missingMessage);
    }

    private function deploymentIdentifier(array $deployment): string
    {
        $packageHash = trim((string) ($deployment['package_hash'] ?? ''));

        if ($packageHash !== '') {
            return $packageHash;
        }

        return trim((string) ($deployment['generated_at'] ?? '')) ?: 'unknown';
    }

    private function log(string $status, array $context = []): void
    {
        $directory = $this->basePath . '/storage/logs';

        if (!is_dir($directory) && !mkdir($concurrentDirectory = $directory, 0777, true) && !is_dir($concurrentDirectory)) {
            return;
        }

        $parts = [];

        foreach ($context as $key => $value) {
            if ($value === '') {
                continue;
            }

            $parts[] = $key . '=' . str_replace(["\r", "\n"], ' ', (string) $value);
        }

        $line = sprintf(
            "[%s] %s%s",
            date(DATE_ATOM),
            $status,
            $parts === [] ? '' : ' ' . implode(' ', $parts)
        );

        file_put_contents($directory . '/deploy-sync.log', $line . PHP_EOL, FILE_APPEND);
    }
}
