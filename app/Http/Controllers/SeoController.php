<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\PageService;
use App\Services\PostService;

class SeoController extends Controller
{
    public function robots(Request $request): Response
    {
        $body = "User-agent: *\nAllow: /\nSitemap: " . url('sitemap.xml') . "\n";

        return Response::text($body);
    }

    public function sitemap(Request $request): Response
    {
        $entries = [];

        foreach ($this->app->make(PageService::class)->all() as $page) {
            $entries[] = [
                'loc' => url(ltrim($page['slug'], '/')),
                'lastmod' => date('c'),
            ];
        }

        foreach ($this->app->make(PostService::class)->publishedList() as $post) {
            $entries[] = [
                'loc' => url('blog/' . $post['slug'] . '/'),
                'lastmod' => date('c', strtotime((string) ($post['updated_at'] ?? $post['published_at']))),
            ];
        }

        $xml = $this->app->make(\App\Core\View::class)->partial('partials/sitemap', [
            'entries' => $entries,
        ]);

        return Response::xml($xml);
    }
}
