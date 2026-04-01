<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\PostService;

class BlogController extends Controller
{
    public function index(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/blog/index', [
            'posts' => $this->app->make(PostService::class)->adminList(),
        ]);
    }

    public function create(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/blog/form', [
            'post' => [
                'id' => null,
                'title' => old('title', ''),
                'slug' => old('slug', ''),
                'excerpt' => old('excerpt', ''),
                'content' => old('content', ''),
                'status' => old('status', 'draft'),
                'published_at' => old('published_at', ''),
                'seo_title' => old('seo_title', ''),
                'seo_description' => old('seo_description', ''),
                'external_url' => old('external_url', ''),
                'thumbnail_path' => '',
            ],
            'formAction' => '/admin/blog/create/',
            'formTitle' => 'Dodaj wpis',
        ]);
    }

    public function store(Request $request): \App\Core\Response
    {
        $result = $this->app->make(PostService::class)->save(
            $request->all(),
            $request->file('thumbnail')
        );

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/blog/create/');
        }

        $this->rebuildDeployPackage();

        return $this->redirect('/admin/blog/', 'Dodano wpis.');
    }

    public function edit(Request $request, string $id): \App\Core\Response
    {
        $post = $this->app->make(PostService::class)->findById((int) $id);

        if ($post === null) {
            return $this->redirect('/admin/blog/', null, 'Nie znaleziono wpisu.');
        }

        return $this->renderAdmin('admin/blog/form', [
            'post' => [
                'id' => $post['id'],
                'title' => old('title', $post['title']),
                'slug' => old('slug', $post['slug']),
                'excerpt' => old('excerpt', $post['excerpt']),
                'content' => old('content', $post['content']),
                'status' => old('status', $post['status']),
                'published_at' => old('published_at', $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : ''),
                'seo_title' => old('seo_title', $post['seo_title']),
                'seo_description' => old('seo_description', $post['seo_description']),
                'external_url' => old('external_url', $post['external_url'] ?? ''),
                'thumbnail_path' => in_array((string) old('thumbnail_remove', '0'), ['1', 'true', 'on', 'yes'], true) ? '' : $post['thumbnail_path'],
            ],
            'formAction' => '/admin/blog/' . $post['id'] . '/edit/',
            'formTitle' => 'Edytuj wpis',
        ]);
    }

    public function update(Request $request, string $id): \App\Core\Response
    {
        $result = $this->app->make(PostService::class)->save(
            $request->all(),
            $request->file('thumbnail'),
            (int) $id
        );

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/blog/' . $id . '/edit/');
        }

        $this->rebuildDeployPackage();

        return $this->redirect('/admin/blog/', 'Zapisano wpis.');
    }

    public function delete(Request $request, string $id): \App\Core\Response
    {
        $this->app->make(PostService::class)->delete((int) $id);

        $this->rebuildDeployPackage();

        return $this->redirect('/admin/blog/', 'Usunięto wpis.');
    }
}
