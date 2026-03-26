<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

class VerifyCsrfMiddleware
{
    public function __construct(private readonly Application $app)
    {
    }

    public function handle(Request $request): ?Response
    {
        $token = $request->input('_token');

        if (!$this->app->make(Csrf::class)->verify(is_string($token) ? $token : null)) {
            session()->flash('error', 'Sesja wygasła. Spróbuj ponownie.');

            return Response::redirect((string) $request->server('HTTP_REFERER', url('/')));
        }

        return null;
    }
}
