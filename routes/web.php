<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\Router;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SeoController;

$router = $app->make(Router::class);

$router->get('/robots.txt', [SeoController::class, 'robots']);
$router->get('/sitemap.xml', [SeoController::class, 'sitemap']);

foreach ($app->config('pages', []) as $pageDefinition) {
    $pageKey = $pageDefinition['key'];
    $slug = $pageDefinition['slug'];

    $router->get($slug, static fn (Request $request) => (new PublicPageController(app()))->show($request, $pageKey));
}
