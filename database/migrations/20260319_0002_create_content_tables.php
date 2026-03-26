<?php

declare(strict_types=1);

return [
    'name' => '20260319_0002_create_content_tables',
    'sql' => [
        "CREATE TABLE IF NOT EXISTS blog_posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            excerpt TEXT NULL,
            content LONGTEXT NOT NULL,
            thumbnail_path VARCHAR(255) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            published_at DATETIME NULL,
            seo_title VARCHAR(255) NOT NULL DEFAULT '',
            seo_description TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_blog_status_published (status, published_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(80) NOT NULL DEFAULT '',
            subject VARCHAR(255) NOT NULL DEFAULT '',
            message LONGTEXT NOT NULL,
            ip_address VARCHAR(64) NOT NULL DEFAULT '',
            user_agent VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ],
];
