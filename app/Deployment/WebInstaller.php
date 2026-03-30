<?php

declare(strict_types=1);

namespace App\Deployment;

use App\Database\MigrationRunner;
use App\Database\SeedRunner;
use PDO;
use RuntimeException;
use Throwable;

class WebInstaller
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function shouldHandleRequest(): bool
    {
        if (!$this->isDeployPackage()) {
            return false;
        }

        return !$this->isInstalled();
    }

    public function handle(): void
    {
        $errors = [];
        $old = [
            'install_key' => '',
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_name' => '',
            'db_user' => '',
        ];

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $old = [
                'install_key' => trim((string) ($_POST['install_key'] ?? '')),
                'db_host' => trim((string) ($_POST['db_host'] ?? '127.0.0.1')),
                'db_port' => trim((string) ($_POST['db_port'] ?? '3306')),
                'db_name' => trim((string) ($_POST['db_name'] ?? '')),
                'db_user' => trim((string) ($_POST['db_user'] ?? '')),
            ];

            try {
                $this->install([
                    'install_key' => $old['install_key'],
                    'db_host' => $old['db_host'],
                    'db_port' => $old['db_port'],
                    'db_name' => $old['db_name'],
                    'db_user' => $old['db_user'],
                    'db_pass' => (string) ($_POST['db_pass'] ?? ''),
                ]);

                header('Location: ' . $this->detectedAppUrl());
                exit;
            } catch (Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        http_response_code(200);
        header('Content-Type: text/html; charset=UTF-8');
        echo $this->render($errors, $old);
        exit;
    }

    private function install(array $input): void
    {
        $config = $this->deploymentConfig();
        $expectedKey = (string) ($config['install_key'] ?? '');

        if ($expectedKey === '' || !hash_equals($expectedKey, (string) $input['install_key'])) {
            throw new RuntimeException('Nieprawidłowy install key.');
        }

        $host = trim((string) $input['db_host']);
        $port = (int) $input['db_port'];
        $database = trim((string) $input['db_name']);
        $user = trim((string) $input['db_user']);
        $password = (string) $input['db_pass'];
        $charset = (string) (($config['env_template']['DB_CHARSET'] ?? 'utf8mb4'));

        if ($host === '' || $port <= 0 || $database === '' || $user === '') {
            throw new RuntimeException('Uzupełnij host, port, nazwę bazy i użytkownika.');
        }

        $serverPdo = $this->connectToServer($host, $port, $user, $password, $charset);
        $serverPdo->exec(
            'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $database) . '` CHARACTER SET ' . $charset . ' COLLATE ' . $charset . '_unicode_ci'
        );

        $pdo = $this->connectToDatabase($host, $port, $database, $user, $password, $charset);
        (new SnapshotRestorer())->restore($pdo, $this->snapshot());

        $this->writeEnvFile([
            'APP_URL' => $this->detectedAppUrl(),
            'DB_HOST' => $host,
            'DB_PORT' => (string) $port,
            'DB_NAME' => $database,
            'DB_USER' => $user,
            'DB_PASS' => $password,
        ]);

        $app = require $this->basePath . '/bootstrap/app.php';
        (new MigrationRunner())->run($app);
        (new SeedRunner())->run($app);

        $this->writeInstallLock();
        $this->writeDeployedLock((string) ($config['generated_at'] ?? ''));
    }

    private function connectToServer(string $host, int $port, string $user, string $password, string $charset): PDO
    {
        return new PDO(
            sprintf('mysql:host=%s;port=%d;charset=%s', $host, $port, $charset),
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    private function connectToDatabase(string $host, int $port, string $database, string $user, string $password, string $charset): PDO
    {
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

    private function writeEnvFile(array $databaseOverrides): void
    {
        $config = $this->deploymentConfig();
        $template = $config['env_template'] ?? [];
        $values = array_merge($template, $databaseOverrides);
        $lines = [];

        foreach ($values as $key => $value) {
            if ($key === 'APP_DEBUG') {
                $value = $value ? 'true' : 'false';
            }

            $stringValue = (string) $value;
            $needsQuotes = preg_match('/\s/', $stringValue) === 1 || $stringValue === '';

            if ($needsQuotes) {
                $stringValue = '"' . addcslashes($stringValue, "\\\"") . '"';
            }

            $lines[] = $key . '=' . $stringValue;
        }

        $contents = implode(PHP_EOL, $lines) . PHP_EOL;

        if (file_put_contents($this->basePath . '/.env', $contents) === false) {
            throw new RuntimeException('Nie udało się zapisać pliku .env.');
        }
    }

    private function writeInstallLock(): void
    {
        $path = $this->basePath . '/storage/app/install.lock';
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($concurrentDirectory = $directory, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException('Nie udało się utworzyć katalogu instalacyjnego.');
        }

        $contents = json_encode([
            'installed_at' => date(DATE_ATOM),
            'app_url' => $this->detectedAppUrl(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (file_put_contents($path, $contents . PHP_EOL) === false) {
            throw new RuntimeException('Nie udało się zapisać pliku blokady instalacji.');
        }
    }

    private function writeDeployedLock(string $generatedAt): void
    {
        if ($generatedAt === '') {
            return;
        }

        $path = $this->basePath . '/storage/app/deployed.lock';
        $contents = json_encode([
            'generated_at' => $generatedAt,
            'synced_at' => date(DATE_ATOM),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($path, $contents . PHP_EOL);
    }

    private function render(array $errors, array $old): string
    {
        $appUrl = htmlspecialchars($this->detectedAppUrl(), ENT_QUOTES, 'UTF-8');
        $errorHtml = '';

        foreach ($errors as $error) {
            $errorHtml .= '<div class="notice notice--error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        $value = static fn (string $key): string => htmlspecialchars((string) ($old[$key] ?? ''), ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalator strony</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f1ea;
            --panel: #fffdf9;
            --text: #1f2933;
            --muted: #6b7280;
            --accent: #7b4b2a;
            --border: #e4d8c9;
            --error-bg: #fef2f2;
            --error-text: #991b1b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            background: radial-gradient(circle at top, #fff8ee 0%, var(--bg) 55%, #efe5d9 100%);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .card {
            width: min(100%, 560px);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(31, 41, 51, 0.10);
            padding: 28px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 32px;
            line-height: 1.1;
        }
        p {
            margin: 0 0 16px;
            color: var(--muted);
            line-height: 1.5;
        }
        form {
            display: grid;
            gap: 14px;
        }
        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        label {
            display: grid;
            gap: 6px;
            font-size: 14px;
        }
        input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            color: var(--text);
            background: #fff;
        }
        button {
            border: 0;
            border-radius: 999px;
            padding: 14px 18px;
            font: inherit;
            cursor: pointer;
            background: var(--accent);
            color: #fff;
        }
        .notice {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
            font-size: 14px;
        }
        .notice--error {
            background: var(--error-bg);
            color: var(--error-text);
        }
        .meta {
            font-size: 13px;
            color: var(--muted);
        }
        @media (max-width: 640px) {
            .card { padding: 22px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Instalator strony</h1>
        <p>Po zapisaniu formularza instalator utworzy konfigurację, odtworzy zapakowaną bazę i uruchomi stronę pod tym adresem: <strong>{$appUrl}</strong>.</p>
        {$errorHtml}
        <form method="post">
            <label>
                Install key
                <input type="text" name="install_key" value="{$value('install_key')}" required>
            </label>
            <div class="grid">
                <label>
                    DB host
                    <input type="text" name="db_host" value="{$value('db_host')}" required>
                </label>
                <label>
                    DB port
                    <input type="text" name="db_port" value="{$value('db_port')}" required>
                </label>
            </div>
            <label>
                DB name
                <input type="text" name="db_name" value="{$value('db_name')}" required>
            </label>
            <label>
                DB user
                <input type="text" name="db_user" value="{$value('db_user')}" required>
            </label>
            <label>
                DB password
                <input type="password" name="db_pass" value="">
            </label>
            <button type="submit">Zainstaluj stronę</button>
        </form>
        <p class="meta">Po zakończeniu instalator wyłączy się automatycznie, a `/admin/` zacznie działać normalnie.</p>
    </main>
</body>
</html>
HTML;
    }

    private function deploymentConfig(): array
    {
        /** @var array $config */
        $config = require $this->basePath . '/storage/app/deployment.php';

        return $config;
    }

    private function snapshot(): array
    {
        $path = $this->basePath . '/database/deploy-snapshot.php';

        if (!is_file($path)) {
            throw new RuntimeException('Brakuje snapshotu bazy w paczce deploy.');
        }

        /** @var array $snapshot */
        $snapshot = require $path;

        return $snapshot;
    }

    private function isDeployPackage(): bool
    {
        return is_file($this->basePath . '/storage/app/deployment.php');
    }

    private function isInstalled(): bool
    {
        return is_file($this->basePath . '/storage/app/install.lock') && is_file($this->basePath . '/.env');
    }

    private function detectedAppUrl(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
            || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
        $scheme = $https ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

        if (str_ends_with($basePath, '/public')) {
            $basePath = substr($basePath, 0, -7);
        }

        return rtrim($scheme . '://' . $host . $basePath, '/');
    }
}
