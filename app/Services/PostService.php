<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlogPostModel;

class PostService
{
    public function __construct(
        private readonly BlogPostModel $posts,
        private readonly UploadService $uploads
    ) {
    }

    public function adminList(): array
    {
        return $this->posts->allForAdmin();
    }

    public function publishedList(): array
    {
        return $this->posts->allPublished();
    }

    public function findById(int $id): ?array
    {
        return $this->posts->findById($id);
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->posts->findPublishedBySlug($slug);
    }

    public function save(array $input, ?array $file, ?int $id = null): array
    {
        $existing = $id !== null ? $this->posts->findById($id) : null;
        $errors = [];

        $title = trim((string) ($input['title'] ?? ''));
        $slug = $this->uniqueSlug(trim((string) ($input['slug'] ?? '')), $title);
        $excerpt = trim((string) ($input['excerpt'] ?? ''));
        $content = trim((string) ($input['content'] ?? ''));
        $status = in_array(($input['status'] ?? 'draft'), ['draft', 'published'], true) ? $input['status'] : 'draft';
        $publishedAt = trim((string) ($input['published_at'] ?? ''));
        $seoTitle = trim((string) ($input['seo_title'] ?? ''));
        $seoDescription = trim((string) ($input['seo_description'] ?? ''));
        $externalUrl = trim((string) ($input['external_url'] ?? ''));

        if ($title === '') {
            $errors['title'] = 'Podaj tytuł wpisu.';
        }

        if ($content === '') {
            $errors['content'] = 'Podaj treść wpisu.';
        }

        if ($this->posts->slugExists($slug, $id)) {
            $errors['slug'] = 'Ten slug jest już zajęty.';
        }

        $thumbnailPath = $existing['thumbnail_path'] ?? '';
        $removeThumbnail = in_array((string) ($input['thumbnail_remove'] ?? ''), ['1', 'true', 'on', 'yes'], true);

        if ($removeThumbnail) {
            $this->uploads->removeImage($thumbnailPath !== '' ? $thumbnailPath : null);
            $thumbnailPath = '';
        }

        if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            try {
                $thumbnailPath = $this->uploads->storeImage($file, 'blog', $thumbnailPath !== '' ? $thumbnailPath : null);
            } catch (\Throwable $exception) {
                $errors['thumbnail'] = 'Nie udało się zapisać pliku.';
            }
        }

        if ($publishedAt !== '' && strtotime($publishedAt) === false) {
            $errors['published_at'] = 'Podaj poprawną datę publikacji.';
        }

        if ($status === 'published' && $publishedAt === '') {
            $publishedAt = date('Y-m-d H:i:s');
        } elseif ($publishedAt !== '') {
            $publishedAt = date('Y-m-d H:i:s', strtotime($publishedAt));
        } else {
            $publishedAt = null;
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $payload = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'thumbnail_path' => $thumbnailPath,
            'status' => $status,
            'published_at' => $publishedAt,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'external_url' => $externalUrl,
        ];

        if ($id === null) {
            $id = $this->posts->create($payload);
        } else {
            $this->posts->update($id, $payload);
        }

        return ['errors' => [], 'id' => $id];
    }

    public function delete(int $id): void
    {
        $post = $this->posts->findById($id);

        if ($post !== null) {
            $this->uploads->removeImage($post['thumbnail_path'] ?? null);
        }

        $this->posts->delete($id);
    }

    private function uniqueSlug(string $slug, string $fallback): string
    {
        $source = $slug !== '' ? $slug : $fallback;
        $normalized = $this->slugify($source);

        return $normalized !== '' ? $normalized : 'wpis';
    }

    private function slugify(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;

        return trim($value, '-');
    }
}
