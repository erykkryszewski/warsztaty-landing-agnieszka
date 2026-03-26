<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Services\PageService;
use App\Services\PostService;
use App\Services\SeoService;
use App\Services\SettingsService;

class PublicPageController extends Controller
{
    public function show(Request $request, string $pageKey): \App\Core\Response
    {
        $page = $this->app->make(PageService::class)->find($pageKey);

        if ($page === null) {
            throw new \App\Core\HttpException(404, 'Nie znaleziono strony.');
        }

        $siteSettings = $this->app->make(SettingsService::class)->values();
        $meta = $this->app->make(SeoService::class)->forPage($page, $siteSettings);
        $data = [
            'page' => $page,
            'meta' => $meta,
        ];

        if ($pageKey === 'blog') {
            $data['posts'] = $this->app->make(PostService::class)->publishedList();
        }

        return $this->render($page['view'], $data);
    }
}
