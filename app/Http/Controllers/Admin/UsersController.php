<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;

class UsersController extends Controller
{
    public function index(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/users/index', [
            'users' => $this->app->make(UserService::class)->all(),
        ]);
    }

    public function create(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/users/form', [
            'user' => [
                'id' => null,
                'name' => old('name', ''),
                'email' => old('email', ''),
                'role' => old('role', 'editor'),
            ],
            'formAction' => '/admin/users/create/',
            'formTitle' => 'Dodaj użytkownika',
        ]);
    }

    public function store(Request $request): \App\Core\Response
    {
        $result = $this->app->make(UserService::class)->save($request->all());

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/users/create/');
        }

        return $this->redirect('/admin/users/', 'Dodano użytkownika.');
    }

    public function edit(Request $request, string $id): \App\Core\Response
    {
        $user = $this->app->make(UserService::class)->find((int) $id);

        if ($user === null) {
            return $this->redirect('/admin/users/', null, 'Nie znaleziono użytkownika.');
        }

        return $this->renderAdmin('admin/users/form', [
            'user' => [
                'id' => $user['id'],
                'name' => old('name', $user['name']),
                'email' => old('email', $user['email']),
                'role' => old('role', $user['role']),
            ],
            'formAction' => '/admin/users/' . $user['id'] . '/edit/',
            'formTitle' => 'Edytuj użytkownika',
        ]);
    }

    public function update(Request $request, string $id): \App\Core\Response
    {
        $result = $this->app->make(UserService::class)->save($request->all(), (int) $id);

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/users/' . $id . '/edit/');
        }

        return $this->redirect('/admin/users/', 'Zapisano użytkownika.');
    }
}
