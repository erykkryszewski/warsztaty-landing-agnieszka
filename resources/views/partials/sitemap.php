<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($entries as $entry): ?>
    <url>
        <loc><?= e($entry['loc']) ?></loc>
        <lastmod><?= e($entry['lastmod']) ?></lastmod>
    </url>
<?php endforeach; ?>
</urlset>
