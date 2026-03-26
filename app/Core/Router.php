<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct(private readonly Application $app)
    {
    }

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $parameters = $this->match($route['path'], $request->path());

            if ($parameters === null) {
                continue;
            }

            foreach ($route['middleware'] as $middlewareClass) {
                $middleware = new $middlewareClass($this->app);
                $response = $middleware->handle($request);

                if ($response instanceof Response) {
                    return $response;
                }
            }

            $result = $this->resolveHandler($route['handler'], $request, $parameters);

            if ($result instanceof Response) {
                return $result;
            }

            return Response::html((string) $result);
        }

        throw new HttpException(404, 'Nie znaleziono strony.');
    }

    private function match(string $routePath, string $requestPath): ?array
    {
        $normalizedRoute = $routePath === '/' ? '/' : trim($routePath, '/');
        $normalizedRequest = $requestPath === '/' ? '/' : trim($requestPath, '/');

        if ($normalizedRoute === '/' && $normalizedRequest === '/') {
            return [];
        }

        $routeSegments = $normalizedRoute === '' ? [] : explode('/', $normalizedRoute);
        $requestSegments = $normalizedRequest === '' ? [] : explode('/', $normalizedRequest);

        if (count($routeSegments) !== count($requestSegments)) {
            return null;
        }

        $parameters = [];

        foreach ($routeSegments as $index => $segment) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $matches) === 1) {
                $parameters[$matches[1]] = $requestSegments[$index];
                continue;
            }

            if ($segment !== $requestSegments[$index]) {
                return null;
            }
        }

        return $parameters;
    }

    private function resolveHandler(callable|array $handler, Request $request, array $parameters): mixed
    {
        if (is_callable($handler) && !is_array($handler)) {
            return $handler($request, ...array_values($parameters));
        }

        [$className, $method] = $handler;
        $controller = new $className($this->app);

        return $controller->{$method}($request, ...array_values($parameters));
    }
}
