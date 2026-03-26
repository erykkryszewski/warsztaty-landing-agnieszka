<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserModel;

class UserService
{
    public function __construct(private readonly UserModel $users)
    {
    }

    public function all(): array
    {
        return $this->users->all();
    }

    public function find(int $id): ?array
    {
        return $this->users->findById($id);
    }

    public function save(array $input, ?int $id = null): array
    {
        $errors = [];

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $role = in_array(($input['role'] ?? 'editor'), ['superadmin', 'editor'], true) ? $input['role'] : 'editor';
        $password = (string) ($input['password'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Podaj nazwę użytkownika.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Podaj poprawny adres e-mail.';
        }

        if ($this->users->emailExists($email, $id)) {
            $errors['email'] = 'Ten adres e-mail jest już używany.';
        }

        if ($id === null && trim($password) === '') {
            $errors['password'] = 'Podaj hasło.';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $payload = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password_hash' => trim($password) !== '' ? password_hash($password, PASSWORD_DEFAULT) : null,
        ];

        if ($id === null) {
            $id = $this->users->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'role' => $payload['role'],
                'password_hash' => (string) $payload['password_hash'],
            ]);
        } else {
            $this->users->update($id, $payload);
        }

        return ['errors' => [], 'id' => $id];
    }
}
