<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\RateLimiterService;

class AuthController extends Controller
{
    public function loginForm(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/auth/login', [
            'layoutTitle' => 'Logowanie',
        ], 'layout/admin-auth');
    }

    public function login(Request $request): \App\Core\Response
    {
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $rateLimiter = $this->app->make(RateLimiterService::class);
        $ipKey = 'admin-login:ip:' . $request->ip();
        $identityKey = 'admin-login:identity:' . $request->ip() . '|' . strtolower($email);

        if ($rateLimiter->tooManyAttempts($ipKey, 10, 600) || $rateLimiter->tooManyAttempts($identityKey, 5, 600)) {
            session()->flashInput(['email' => $email]);

            return $this->redirect('/admin/login/', null, 'Za dużo prób logowania. Spróbuj ponownie za 10 minut.');
        }

        if (!$this->app->make(AuthService::class)->attempt($email, $password)) {
            $rateLimiter->hit($ipKey, 600);
            $rateLimiter->hit($identityKey, 600);
            session()->flashInput(['email' => $email]);

            return $this->redirect('/admin/login/', null, 'Nieprawidłowy e-mail lub hasło.');
        }

        $rateLimiter->clear($ipKey);
        $rateLimiter->clear($identityKey);

        return $this->redirect('/admin/', 'Zalogowano pomyślnie.');
    }

    public function logout(Request $request): \App\Core\Response
    {
        $this->app->make(AuthService::class)->logout();

        return $this->redirect('/admin/login/', 'Wylogowano.');
    }
}
