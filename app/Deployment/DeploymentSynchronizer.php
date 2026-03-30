<?php

declare(strict_types=1);

namespace App\Deployment;

use App\Core\Env;
use App\Database\MigrationRunner;
use App\Database\SeedRunner;
use PDO;
use RuntimeException;

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

        if ($generatedAt === '' || !$this->needsSync($generatedAt)) {
            return;
        }

        Env::load($this->basePath . '/.env');

        $pdo = $this->connectToDatabase();
        $snapshot = $this->snapshot();

        (new SnapshotRestorer())->restore($pdo, $snapshot);

        $app = require $this->basePath . '/bootstrap/app.php';
        (new MigrationRunner())->run($app);
        (new SeedRunner())->run($app);

        $this->writeDeployedLock($generatedAt);
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
        /** @var array $config */
        $config = require $this->basePath . '/storage/app/deployment.php';

        return $config;
    }

    private function snapshot(): array
    {
        /** @var array $snapshot */
        $snapshot = require $this->basePath . '/database/deploy-snapshot.php';

        return $snapshot;
    }

    private function needsSync(string $generatedAt): bool
    {
        $lockPath = $this->basePath . '/storage/app/deployed.lock';

        if (!is_file($lockPath)) {
            return true;
        }

        $state = json_decode((string) file_get_contents($lockPath), true);
        $appliedAt = is_array($state) ? (string) ($state['generated_at'] ?? '') : '';

        return $appliedAt !== $generatedAt;
    }

    private function writeDeployedLock(string $generatedAt): void
    {
        $path = $this->basePath . '/storage/app/deployed.lock';
        $contents = json_encode([
            'generated_at' => $generatedAt,
            'synced_at' => date(DATE_ATOM),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($path, $contents . PHP_EOL);
    }

    private function isDeployPackage(): bool
    {
        return is_file($this->basePath . '/storage/app/deployment.php') && is_file($this->basePath . '/database/deploy-snapshot.php');
    }

    private function isInstalled(): bool
    {
        return is_file($this->basePath . '/storage/app/install.lock') && is_file($this->basePath . '/.env');
    }
}
