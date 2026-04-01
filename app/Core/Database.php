<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private ?PDO $pdo = null;

    private bool $pendingMutations = false;

    public function __construct(private readonly array $config)
    {
    }

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config['host'],
            $this->config['port'],
            $this->config['name'],
            $this->config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $this->config['user'], $this->config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new \RuntimeException('Database connection failed: ' . $exception->getMessage(), 0, $exception);
        }

        return $this->pdo;
    }

    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($bindings);
        $this->markMutationIfNeeded($sql);

        return $statement;
    }

    public function fetch(string $sql, array $bindings = []): ?array
    {
        $result = $this->query($sql, $bindings)->fetch();

        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    public function statement(string $sql, array $bindings = []): bool
    {
        return $this->query($sql, $bindings) !== false;
    }

    public function lastInsertId(): string
    {
        return $this->pdo()->lastInsertId();
    }

    public function hasPendingMutations(): bool
    {
        return $this->pendingMutations;
    }

    public function clearPendingMutations(): void
    {
        $this->pendingMutations = false;
    }

    private function markMutationIfNeeded(string $sql): void
    {
        $operation = strtoupper((string) preg_replace('/\s+.*/s', '', ltrim($sql)));

        if (in_array($operation, ['INSERT', 'UPDATE', 'DELETE', 'REPLACE', 'ALTER', 'CREATE', 'DROP', 'TRUNCATE', 'RENAME'], true)) {
            $this->pendingMutations = true;
        }
    }
}
