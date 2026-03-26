<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Services\PostService;
use App\Services\SeoService;
use App\Services\SettingsService;

class BlogPostController extends Controller
{
    public function show(Request $request, string $slug): \App\Core\Response
    {
        $post = $this->app->make(PostService::class)->findPublishedBySlug($slug);

        if ($post === null) {
            throw new HttpException(404, 'Nie znaleziono wpisu.');
        }

        $siteSettings = $this->app->make(SettingsService::class)->values();

        return $this->render('blog/show', [
            'post' => $post,
            'meta' => $this->app->make(SeoService::class)->forPost($post, $siteSettings),
        ]);
    }
}
