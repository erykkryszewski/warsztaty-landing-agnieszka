<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class PluginStateModel
{
    public function __construct(private readonly Database $database)
    {
    }

    public function allIndexed(): array
    {
        $rows = $this->database->fetchAll('SELECT * FROM plugin_states ORDER BY plugin_key ASC');
        $indexed = [];

        foreach ($rows as $row) {
            $indexed[$row['plugin_key']] = $row;
        }

        return $indexed;
    }

    public function upsert(string $pluginKey, bool $enabled): void
    {
        $this->database->statement(
            'INSERT INTO plugin_states (plugin_key, is_enabled, created_at, updated_at)
             VALUES (:plugin_key, :is_enabled, NOW(), NOW())
             ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled), updated_at = NOW()',
            [
                'plugin_key' => $pluginKey,
                'is_enabled' => $enabled ? 1 : 0,
            ]
        );
    }
}
