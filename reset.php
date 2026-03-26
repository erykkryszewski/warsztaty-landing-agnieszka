<?php

/**
 * Reset script for Ercoding CMS.
 *
 * Returns the CMS to a clean starter state, ready for a new project.
 * After reset the site has working pages with default/lorem content,
 * a single admin user (admin@example.pl / pass), default settings,
 * sample blog posts, and all plugins re-initialized.
 *
 * Usage:
 *   php reset.php              — interactive, asks for confirmation
 *   php reset.php --confirm    — non-interactive, runs immediately
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Helpers/helpers.php';

use App\Core\Env;

Env::load(__DIR__ . '/.env');

// Bootstrap database
$dbConfig = require __DIR__ . '/config/database.php';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
    $dbConfig['host'], $dbConfig['port'], $dbConfig['name'], $dbConfig['charset']
);
$pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$autoConfirm = in_array('--confirm', $argv ?? [], true);

echo "\n";
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║           ERCODING CMS — FULL RESET                 ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║  This will:                                         ║\n";
echo "║  • Reset all page content to config defaults        ║\n";
echo "║  • Reset all settings to config defaults            ║\n";
echo "║  • Delete all blog posts and re-seed sample ones    ║\n";
echo "║  • Delete all contact messages                      ║\n";
echo "║  • Delete all uploaded files                        ║\n";
echo "║  • Reset admin user to admin@example.pl / pass      ║\n";
echo "║  • Re-initialize all plugin states                  ║\n";
echo "║                                                     ║\n";
echo "║  After reset the site is a working starter with     ║\n";
echo "║  default content, ready for a new project.          ║\n";
echo "╚══════════════════════════════════════════════════════╝\n";
echo "\n";

if (!$autoConfirm) {
    echo "Type 'yes' to continue: ";
    $handle = fopen('php://stdin', 'r');
    $input = trim(fgets($handle));
    fclose($handle);

    if ($input !== 'yes') {
        echo "Aborted.\n";
        exit(1);
    }
}

// ── 1. Reset pages to config defaults ──

$pages = require __DIR__ . '/config/pages.php';

/**
 * Build defaults from field definitions, respecting the 'default' key.
 */
function buildFieldDefaults(array $fields): array
{
    $defaults = [];
    foreach ($fields as $field) {
        if ($field['type'] === 'repeater') {
            $defaults[$field['name']] = $field['default'] ?? [];
        } else {
            $defaults[$field['name']] = $field['default'] ?? '';
        }
    }
    return $defaults;
}

foreach ($pages as $page) {
    $defaults = [];
    foreach ($page['groups'] as $group) {
        $defaults[$group['key']] = buildFieldDefaults($group['fields']);
    }

    $json = json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM pages WHERE page_key = ?');
    $stmt->execute([$page['key']]);

    if ((int) $stmt->fetch()['cnt'] > 0) {
        $stmt = $pdo->prepare('UPDATE pages SET content_json = ?, meta_title = "", meta_description = "", meta_image = "" WHERE page_key = ?');
        $stmt->execute([$json, $page['key']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO pages (page_key, content_json, meta_title, meta_description, meta_image) VALUES (?, ?, "", "", "")');
        $stmt->execute([$page['key'], $json]);
    }
    echo "  ✓ Page '{$page['key']}' reset to defaults\n";
}

// ── 2. Reset settings to config defaults ──

$settings = require __DIR__ . '/config/settings.php';
foreach ($settings as $section) {
    $defaults = buildFieldDefaults($section['fields']);

    $json = json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM settings WHERE section_key = ?');
    $stmt->execute([$section['key']]);

    if ((int) $stmt->fetch()['cnt'] > 0) {
        $stmt = $pdo->prepare('UPDATE settings SET content_json = ? WHERE section_key = ?');
        $stmt->execute([$json, $section['key']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO settings (section_key, content_json) VALUES (?, ?)');
        $stmt->execute([$section['key'], $json]);
    }
    echo "  ✓ Settings '{$section['key']}' reset to defaults\n";
}

// ── 2b. Reset plugin-managed settings sections ──

$pluginClasses = require __DIR__ . '/config/plugins.php';
foreach ($pluginClasses as $pluginClass) {
    $instance = new $pluginClass();
    // Plugins that register settings sections via register() have a method we can inspect
    // For now, delete any settings rows not in core config
}

$coreKeys = array_column($settings, 'key');
$inPlaceholders = implode(',', array_fill(0, count($coreKeys), '?'));
$pdo->prepare("DELETE FROM settings WHERE section_key NOT IN ($inPlaceholders)")->execute($coreKeys);
echo "  ✓ Plugin settings sections cleared (will be re-seeded on next boot)\n";

// ── 3. Delete blog posts and re-seed sample ones ──

$pdo->exec('DELETE FROM blog_posts');
echo "  ✓ Blog posts deleted\n";

$samplePosts = [
    [
        'title' => 'Przykładowy wpis blogowy',
        'slug' => 'przykladowy-wpis-blogowy',
        'excerpt' => 'To jest przykładowy wpis blogowy. Zmień go lub usuń w panelu administracyjnym.',
        'content' => '<p>Treść przykładowego wpisu blogowego. Edytuj w panelu admina.</p>',
        'thumbnail_path' => '',
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'seo_title' => 'Przykładowy wpis blogowy',
        'seo_description' => 'Przykładowy wpis blogowy do zastąpienia.',
        'external_url' => '',
    ],
];

$postStmt = $pdo->prepare(
    'INSERT INTO blog_posts (title, slug, excerpt, content, thumbnail_path, status, published_at, seo_title, seo_description, external_url, created_at, updated_at)
     VALUES (:title, :slug, :excerpt, :content, :thumbnail_path, :status, :published_at, :seo_title, :seo_description, :external_url, NOW(), NOW())'
);

foreach ($samplePosts as $post) {
    $postStmt->execute($post);
}
echo "  ✓ Sample blog posts seeded\n";

// ── 4. Delete contact messages ──

$pdo->exec('DELETE FROM contact_messages');
echo "  ✓ Contact messages deleted\n";

// ── 5. Clean uploads ──

$uploadsDir = __DIR__ . '/public/uploads';
if (is_dir($uploadsDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    $count = 0;
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            @rmdir($item->getRealPath());
        } else {
            @unlink($item->getRealPath());
            $count++;
        }
    }
    echo "  ✓ Uploads cleaned ({$count} files)\n";
}

// ── 6. Reset admin user ──

$adminEmail = 'admin@example.pl';
$adminPassword = (string) env('ADMIN_SEED_PASSWORD', 'pass');
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email = ?');
$stmt->execute([$adminEmail]);

if ((int) $stmt->fetch()['cnt'] > 0) {
    $pdo->prepare('UPDATE users SET password_hash = ?, name = "Administrator", role = "superadmin" WHERE email = ?')
        ->execute([$adminHash, $adminEmail]);
} else {
    $pdo->prepare('INSERT INTO users (name, email, password_hash, role, created_at, updated_at) VALUES ("Administrator", ?, ?, "superadmin", NOW(), NOW())')
        ->execute([$adminEmail, $adminHash]);
}

// Delete any extra users
$pdo->prepare('DELETE FROM users WHERE email != ?')->execute([$adminEmail]);
echo "  ✓ Admin user reset ({$adminEmail} / {$adminPassword})\n";

// ── 7. Re-initialize plugin states ──

$pdo->exec('DELETE FROM plugin_states');

$pluginClasses = require __DIR__ . '/config/plugins.php';
foreach ($pluginClasses as $pluginClass) {
    $instance = new $pluginClass();
    $def = $instance->definition();
    $enabled = (bool) ($def['enabled_by_default'] ?? false);
    $required = (bool) ($def['required'] ?? false);

    if (!$required) {
        $pdo->prepare('INSERT INTO plugin_states (plugin_key, is_enabled) VALUES (?, ?)')
            ->execute([$def['key'], (int) $enabled]);
    }
    echo "  ✓ Plugin '{$def['key']}' initialized" . ($enabled ? ' (enabled)' : ' (disabled)') . "\n";
}

echo "\n";
echo "Done! CMS is a clean starter with default content.\n";
echo "Admin login: {$adminEmail} / {$adminPassword}\n";
echo "Ready for a new project.\n";
echo "\n";
