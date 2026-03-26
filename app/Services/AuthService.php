<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\UserModel;

class AuthService
{
    private ?array $user = null;

    public function __construct(
        private readonly UserModel $users,
        private readonly Session $session
    ) {
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail(trim($email));

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        $this->session->put('auth_user_id', (int) $user['id']);
        $this->session->regenerate();
        $this->user = $user;

        return true;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function user(): ?array
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get('auth_user_id');

        if (!is_int($userId) && !ctype_digit((string) $userId)) {
            return null;
        }

        $this->user = $this->users->findById((int) $userId);

        return $this->user;
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->forget('auth_user_id');
        $this->session->regenerate();
    }
}
