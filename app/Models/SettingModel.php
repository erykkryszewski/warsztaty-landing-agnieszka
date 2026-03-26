<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SettingModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function allIndexed(): array
    {
        $rows = $this->database->fetchAll('SELECT * FROM settings ORDER BY section_key ASC');
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[$row['section_key']] = $row;
        }

        return $indexed;
    }

    public function upsert(string $sectionKey, string $contentJson): void
    {
        $this->database->statement(
            'INSERT INTO settings (section_key, content_json, created_at, updated_at)
             VALUES (:section_key, :content_json, NOW(), NOW())
             ON DUPLICATE KEY UPDATE content_json = VALUES(content_json), updated_at = NOW()',
            [
                'section_key' => $sectionKey,
                'content_json' => $contentJson,
            ]
        );
    }
}
