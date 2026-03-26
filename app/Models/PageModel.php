<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PageModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function allIndexed(): array
    {
        $rows = $this->database->fetchAll('SELECT * FROM pages ORDER BY page_key ASC');
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[$row['page_key']] = $row;
        }

        return $indexed;
    }

    public function findByKey(string $pageKey): ?array
    {
        return $this->database->fetch('SELECT * FROM pages WHERE page_key = :page_key LIMIT 1', [
            'page_key' => $pageKey,
        ]);
    }

    public function upsert(string $pageKey, string $contentJson, array $meta): void
    {
        $this->database->statement(
            'INSERT INTO pages (page_key, content_json, meta_title, meta_description, meta_image, created_at, updated_at)
             VALUES (:page_key, :content_json, :meta_title, :meta_description, :meta_image, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                content_json = VALUES(content_json),
                meta_title = VALUES(meta_title),
                meta_description = VALUES(meta_description),
                meta_image = VALUES(meta_image),
                updated_at = NOW()',
            [
                'page_key' => $pageKey,
                'content_json' => $contentJson,
                'meta_title' => $meta['meta_title'] ?? '',
                'meta_description' => $meta['meta_description'] ?? '',
                'meta_image' => $meta['meta_image'] ?? '',
            ]
        );
    }
}
