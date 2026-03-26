<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class GuestMiddleware
{
    public function __construct(private readonly Application $app)
    {
    }

    public function handle(Request $request): ?Response
    {
        if ($this->app->make(AuthService::class)->check()) {
            return Response::redirect(url('admin/'));
        }

        return null;
    }
}
