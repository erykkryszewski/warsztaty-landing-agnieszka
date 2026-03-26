<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class UserModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function all(): array
    {
        return $this->database->fetchAll('SELECT * FROM users ORDER BY created_at DESC');
    }

    public function findById(int $id): ?array
    {
        return $this->database->fetch('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->database->fetch('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $row = $this->database->fetch(
            'SELECT id FROM users WHERE email = :email' . ($ignoreId !== null ? ' AND id != :ignore_id' : '') . ' LIMIT 1',
            array_filter([
                'email' => $email,
                'ignore_id' => $ignoreId,
            ], static fn (mixed $value): bool => $value !== null)
        );

        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->database->statement(
            'INSERT INTO users (name, email, password_hash, role, created_at, updated_at)
             VALUES (:name, :email, :password_hash, :role, NOW(), NOW())',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
                'role' => $data['role'],
            ]
        );

        return (int) $this->database->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE users SET name = :name, email = :email, role = :role, updated_at = NOW()';
        $bindings = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password_hash'])) {
            $sql .= ', password_hash = :password_hash';
            $bindings['password_hash'] = $data['password_hash'];
        }

        $sql .= ' WHERE id = :id';

        $this->database->statement($sql, $bindings);
    }
}
