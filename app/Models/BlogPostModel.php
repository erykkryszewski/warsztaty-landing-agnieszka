<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class BlogPostModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function allForAdmin(): array
    {
        return $this->database->fetchAll('SELECT * FROM blog_posts ORDER BY created_at DESC');
    }

    public function allPublished(): array
    {
        return $this->database->fetchAll(
            "SELECT * FROM blog_posts WHERE status = 'published' AND published_at IS NOT NULL ORDER BY published_at DESC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->database->fetch('SELECT * FROM blog_posts WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->database->fetch(
            "SELECT * FROM blog_posts WHERE slug = :slug AND status = 'published' LIMIT 1",
            ['slug' => $slug]
        );
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $row = $this->database->fetch(
            'SELECT id FROM blog_posts WHERE slug = :slug' . ($ignoreId !== null ? ' AND id != :ignore_id' : '') . ' LIMIT 1',
            array_filter([
                'slug' => $slug,
                'ignore_id' => $ignoreId,
            ], static fn (mixed $value): bool => $value !== null)
        );

        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->database->statement(
            'INSERT INTO blog_posts
                (title, slug, excerpt, content, thumbnail_path, status, published_at, seo_title, seo_description, external_url, created_at, updated_at)
             VALUES
                (:title, :slug, :excerpt, :content, :thumbnail_path, :status, :published_at, :seo_title, :seo_description, :external_url, NOW(), NOW())',
            $data
        );

        return (int) $this->database->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;

        $this->database->statement(
            'UPDATE blog_posts SET
                title = :title,
                slug = :slug,
                excerpt = :excerpt,
                content = :content,
                thumbnail_path = :thumbnail_path,
                status = :status,
                published_at = :published_at,
                seo_title = :seo_title,
                seo_description = :seo_description,
                external_url = :external_url,
                updated_at = NOW()
             WHERE id = :id',
            $data
        );
    }

    public function delete(int $id): void
    {
        $this->database->statement('DELETE FROM blog_posts WHERE id = :id', ['id' => $id]);
    }
}
