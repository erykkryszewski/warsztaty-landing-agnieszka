<?php

declare(strict_types=1);

return [
    'name' => '20260324_0003_add_external_url_to_blog_posts',
    'sql' => [
        "ALTER TABLE blog_posts ADD COLUMN external_url VARCHAR(500) NOT NULL DEFAULT '' AFTER seo_description",
    ],
];
