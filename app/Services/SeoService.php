<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class SeoService
{
    public function __construct(private readonly Application $app)
    {
    }

    public function forPage(array $page, array $siteSettings): array
    {
        $main = $page['content']['main'] ?? [];
        $defaultSeo = $siteSettings['seo_defaults'] ?? [];
        $pageTitle = trim((string) ($main['page_title'] ?? ''));

        if ($pageTitle === '') {
            foreach (($page['content'] ?? []) as $group) {
                $candidate = trim((string) ($group['page_title'] ?? ''));

                if ($candidate !== '') {
                    $pageTitle = $candidate;
                    break;
                }
            }
        }

        return [
            'title' => $page['meta_title'] !== '' ? $page['meta_title'] : ($pageTitle !== '' ? $pageTitle : ($defaultSeo['default_title'] ?? $this->app->config('app.name'))),
            'description' => $page['meta_description'] !== '' ? $page['meta_description'] : ($defaultSeo['default_description'] ?? ''),
            'image' => $page['meta_image'] !== '' ? $page['meta_image'] : ($defaultSeo['default_og_image'] ?? ''),
            'canonical' => url(ltrim($page['slug'], '/')),
        ];
    }

    public function forPost(array $post, array $siteSettings): array
    {
        $defaultSeo = $siteSettings['seo_defaults'] ?? [];

        return [
            'title' => trim((string) ($post['seo_title'] ?: $post['title'])),
            'description' => trim((string) ($post['seo_description'] ?: $post['excerpt'] ?: $defaultSeo['default_description'] ?? '')),
            'image' => trim((string) ($post['thumbnail_path'] ?: $defaultSeo['default_og_image'] ?? '')),
            'canonical' => url('blog/' . $post['slug'] . '/'),
        ];
    }
}
