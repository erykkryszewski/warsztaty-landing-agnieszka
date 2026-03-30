<?php

declare(strict_types=1);

namespace App\Deployment;

use PDO;
use RuntimeException;

class DeployPackageBuilder
{
    private string $deployPath;

    public function __construct(private readonly string $basePath)
    {
        $this->deployPath = $this->basePath . '/deploy';
    }

    public function build(): array
    {
        $this->recreateDeployDirectory();
        $this->copyRuntimeFiles();
        $snapshot = $this->createDatabaseSnapshot();
        $installKey = bin2hex(random_bytes(16));

        $this->writePhpFile(
            $this->deployPath . '/database/deploy-snapshot.php',
            $snapshot
        );

        $this->writePhpFile(
            $this->deployPath . '/storage/app/deployment.php',
            [
                'install_key' => $installKey,
                'generated_at' => date(DATE_ATOM),
                'env_template' => $this->envTemplate(),
            ]
        );

        $this->writeInstructions($installKey);
        $this->prepareRuntimeDirectories();

        return [
            'path' => $this->deployPath,
            'install_key' => $installKey,
            'generated_at' => date(DATE_ATOM),
        ];
    }

    private function recreateDeployDirectory(): void
    {
        if (is_dir($this->deployPath)) {
            $this->deleteDirectory($this->deployPath);
        }

        if (!is_dir($this->deployPath) && !mkdir($concurrentDirectory = $this->deployPath, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException('Unable to create deploy directory.');
        }
    }

    private function copyRuntimeFiles(): void
    {
        foreach ($this->sourceItems() as $item) {
            $source = $this->basePath . '/' . $item;
            $target = $this->deployPath . '/' . $item;

            if (is_dir($source)) {
                $this->copyDirectory($source, $target);
                continue;
            }

            if (!is_file($source)) {
                continue;
            }

            $parent = dirname($target);

            if (!is_dir($parent) && !mkdir($concurrentDirectory = $parent, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Unable to create directory [%s].', $parent));
            }

            if (!copy($source, $target)) {
                throw new RuntimeException(sprintf('Unable to copy [%s].', $item));
            }
        }
    }

    private function sourceItems(): array
    {
        return [
            '.htaccess',
            'app',
            'bootstrap',
            'composer.json',
            'composer.lock',
            'config',
            'database',
            'public',
            'resources',
            'routes',
            'storage',
            'vendor',
        ];
    }

    private function createDatabaseSnapshot(): array
    {
        $pdo = $this->sourcePdo();
        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        if (!is_array($tables)) {
            throw new RuntimeException('Unable to read source database tables.');
        }

        $snapshot = [
            'database' => (string) env('DB_NAME', ''),
            'generated_at' => date(DATE_ATOM),
            'tables' => [],
        ];

        foreach ($tables as $table) {
            $tableName = (string) $table;
            $createRow = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', $tableName) . '`')->fetch(PDO::FETCH_ASSOC);

            if (!is_array($createRow)) {
                throw new RuntimeException(sprintf('Unable to read CREATE TABLE for [%s].', $tableName));
            }

            $createSql = (string) ($createRow['Create Table'] ?? array_values($createRow)[1] ?? '');

            if ($createSql === '') {
                throw new RuntimeException(sprintf('Empty CREATE TABLE statement for [%s].', $tableName));
            }

            $rows = $pdo->query('SELECT * FROM `' . str_replace('`', '``', $tableName) . '`')->fetchAll(PDO::FETCH_ASSOC);

            $snapshot['tables'][] = [
                'name' => $tableName,
                'create_sql' => $createSql,
                'rows' => is_array($rows) ? $rows : [],
            ];
        }

        return $snapshot;
    }

    private function sourcePdo(): PDO
    {
        $host = (string) env('DB_HOST', '127.0.0.1');
        $port = (int) env('DB_PORT', 3306);
        $database = (string) env('DB_NAME', '');
        $user = (string) env('DB_USER', '');
        $password = (string) env('DB_PASS', '');
        $charset = (string) env('DB_CHARSET', 'utf8mb4');

        if ($database === '') {
            throw new RuntimeException('DB_NAME is empty. Cannot create deploy snapshot.');
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

    private function envTemplate(): array
    {
        return [
            'APP_NAME' => (string) env('APP_NAME', 'ER Coding Mini CMS'),
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
            'APP_TIMEZONE' => (string) env('APP_TIMEZONE', 'Europe/Warsaw'),
            'APP_LOCALE' => (string) env('APP_LOCALE', 'pl_PL'),
            'DB_CHARSET' => (string) env('DB_CHARSET', 'utf8mb4'),
            'MAIL_TO' => (string) env('MAIL_TO', 'kontakt@example.pl'),
            'MAIL_FROM' => (string) env('MAIL_FROM', 'powiadomienia@example.pl'),
            'MAIL_FROM_NAME' => (string) env('MAIL_FROM_NAME', 'ER Coding Mini CMS'),
            'ADMIN_SEED_NAME' => (string) env('ADMIN_SEED_NAME', 'Administrator'),
            'ADMIN_SEED_EMAIL' => (string) env('ADMIN_SEED_EMAIL', 'admin@example.pl'),
            'ADMIN_SEED_PASSWORD' => (string) env('ADMIN_SEED_PASSWORD', 'pass'),
        ];
    }

    private function writeInstructions(string $installKey): void
    {
        $contents = implode(PHP_EOL, [
            'DEPLOY PACKAGE',
            '',
            '1. Upload the full contents of this deploy directory to the target FTP document root.',
            '2. Open the target domain in the browser.',
            '3. In the installer, enter:',
            '   - install key: ' . $installKey,
            '   - DB host, port, database, user, password',
            '4. Installer restores the packaged database snapshot and finishes setup automatically.',
            '',
            'Generated at: ' . date(DATE_ATOM),
        ]) . PHP_EOL;

        file_put_contents($this->deployPath . '/INSTALL_INSTRUCTIONS.txt', $contents);
    }

    private function prepareRuntimeDirectories(): void
    {
        $directories = [
            $this->deployPath . '/storage/app',
            $this->deployPath . '/storage/cache',
            $this->deployPath . '/storage/logs',
            $this->deployPath . '/public/uploads',
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory) && !mkdir($concurrentDirectory = $directory, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Unable to create runtime directory [%s].', $directory));
            }
        }

        $this->emptyDirectory($this->deployPath . '/storage/cache', ['.gitkeep']);
        $this->emptyDirectory($this->deployPath . '/storage/logs', ['.gitkeep']);
        @unlink($this->deployPath . '/storage/app/install.lock');
        @unlink($this->deployPath . '/.env');
    }

    private function writePhpFile(string $path, array $payload): void
    {
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($concurrentDirectory = $directory, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Unable to create directory [%s].', $directory));
        }

        $contents = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($payload, true) . ";\n";

        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException(sprintf('Unable to write [%s].', $path));
        }
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($target) && !mkdir($concurrentDirectory = $target, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Unable to create directory [%s].', $target));
        }

        $items = scandir($source);

        if ($items === false) {
            throw new RuntimeException(sprintf('Unable to read directory [%s].', $source));
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $item;
            $targetPath = $target . '/' . $item;

            if ($this->shouldSkip($sourcePath)) {
                continue;
            }

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $targetPath);
                continue;
            }

            if (!copy($sourcePath, $targetPath)) {
                throw new RuntimeException(sprintf('Unable to copy [%s].', $sourcePath));
            }
        }
    }

    private function shouldSkip(string $path): bool
    {
        $normalized = str_replace('\\', '/', $path);
        $relative = ltrim(substr($normalized, strlen(str_replace('\\', '/', $this->basePath))), '/');

        $skippedPrefixes = [
            'storage/cache/',
            'storage/logs/',
        ];

        foreach ($skippedPrefixes as $prefix) {
            if (str_starts_with($relative, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function emptyDirectory(string $path, array $keep = []): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || in_array($item, $keep, true)) {
                continue;
            }

            $itemPath = $path . '/' . $item;

            if (is_dir($itemPath)) {
                $this->deleteDirectory($itemPath);
                continue;
            }

            @unlink($itemPath);
        }
    }

    private function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . '/' . $item;

            if (is_dir($itemPath)) {
                $this->deleteDirectory($itemPath);
                continue;
            }

            @unlink($itemPath);
        }

        @rmdir($path);
    }
}
