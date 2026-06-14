<?php

declare(strict_types=1);

use App\Core\Database;

define('BASE_PATH', __DIR__);

require BASE_PATH . '/vendor/autoload.php';

$app = require BASE_PATH . '/bootstrap/app.php';
$options = cli_options($argv ?? []);
$email = trim((string) ($options['email'] ?? 'ercodingpl@gmail.com'));
$name = trim((string) ($options['name'] ?? 'Administrator'));
$password = (string) ($options['password'] ?? getenv('ADMIN_RESET_PASSWORD') ?: '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, 'Invalid email address.' . PHP_EOL);
    exit(1);
}

if ($password === '') {
    $password = prompt_password();
}

if (strlen($password) < 8) {
    fwrite(STDERR, 'Password must have at least 8 characters.' . PHP_EOL);
    exit(1);
}

$database = $app->make(Database::class);
$existing = $database->fetch('SELECT id FROM users WHERE email = :email LIMIT 1', [
    'email' => $email,
]);

$payload = [
    'name' => $name !== '' ? $name : 'Administrator',
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => 'superadmin',
];

if ($existing !== null) {
    $database->statement(
        'UPDATE users SET name = :name, password_hash = :password_hash, role = :role, updated_at = NOW() WHERE email = :email',
        $payload
    );
    $action = 'updated';
} else {
    $database->statement(
        'INSERT INTO users (name, email, password_hash, role, created_at, updated_at)
         VALUES (:name, :email, :password_hash, :role, NOW(), NOW())',
        $payload
    );
    $action = 'created';
}

clear_rate_limits(BASE_PATH . '/storage/cache/rate-limits');
$database->clearPendingMutations();

echo sprintf('Admin account %s: %s', $action, $email) . PHP_EOL;
echo 'Rate limits cleared.' . PHP_EOL;

function cli_options(array $argv): array
{
    $options = [];

    foreach (array_slice($argv, 1) as $argument) {
        if (!str_starts_with($argument, '--')) {
            continue;
        }

        $parts = explode('=', substr($argument, 2), 2);
        $options[$parts[0]] = $parts[1] ?? true;
    }

    return $options;
}

function prompt_password(): string
{
    fwrite(STDOUT, 'New admin password: ');
    $password = fgets(STDIN);
    fwrite(STDOUT, 'Repeat password: ');
    $confirmation = fgets(STDIN);

    $password = is_string($password) ? rtrim($password, "\r\n") : '';
    $confirmation = is_string($confirmation) ? rtrim($confirmation, "\r\n") : '';

    if ($password === '' || !hash_equals($password, $confirmation)) {
        fwrite(STDERR, 'Passwords do not match.' . PHP_EOL);
        exit(1);
    }

    return $password;
}

function clear_rate_limits(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }

    $items = scandir($directory);

    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === '.gitkeep') {
            continue;
        }

        $path = $directory . '/' . $item;

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
