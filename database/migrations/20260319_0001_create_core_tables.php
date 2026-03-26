<?php

declare(strict_types=1);

return [
    'name' => '20260319_0001_create_core_tables',
    'sql' => [
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(30) NOT NULL DEFAULT 'editor',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            section_key VARCHAR(120) NOT NULL UNIQUE,
            content_json LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS pages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_key VARCHAR(120) NOT NULL UNIQUE,
            content_json LONGTEXT NOT NULL,
            meta_title VARCHAR(255) NOT NULL DEFAULT '',
            meta_description TEXT NULL,
            meta_image VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS plugin_states (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            plugin_key VARCHAR(120) NOT NULL UNIQUE,
            is_enabled TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ],
];
