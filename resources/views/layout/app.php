<?php
$meta = $meta ?? [];
$title = $meta['title'] ?? config('app.name');
$description = $meta['description'] ?? '';
$canonical = $meta['canonical'] ?? current_url();
$image = $meta['image'] ?? '';
$imageUrl = $image !== '' && !str_starts_with($image, 'http') ? url(ltrim($image, '/')) : $image;

$business = $siteSettings['business'] ?? [];
$favicon = trim((string) ($business['favicon'] ?? '/assets/images/logo-placeholder.svg'));
$faviconUrl = $favicon !== '' && !str_starts_with($favicon, 'http') ? asset(ltrim($favicon, '/')) : $favicon;
$faviconPath = parse_url($faviconUrl, PHP_URL_PATH) ?: '';
$faviconExtension = strtolower(pathinfo($faviconPath, PATHINFO_EXTENSION));
$faviconType = match ($faviconExtension) {
    'svg' => 'image/svg+xml',
    'png' => 'image/png',
    'webp' => 'image/webp',
    'ico' => 'image/x-icon',
    default => 'image/svg+xml',
};

$theme = $siteSettings['theme'] ?? [];
$fontPrimary = $theme['font_primary'] ?? 'Manrope';
$fontDecorated = $theme['font_decorated'] ?? 'Fraunces';
$colorMain = $theme['color_main'] ?? '#231939';
$colorAccent = $theme['color_accent'] ?? '#db2d7e';
$colorText = $theme['color_text'] ?? '#1a1a2e';
$pageKey = $page['key'] ?? 'default';

$fontsQuery = urlencode($fontPrimary) . ':wght@400;500;600;700&family=' . urlencode($fontDecorated) . ':wght@400;600;700';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if ($imageUrl !== ''): ?>
        <meta property="og:image" content="<?= e($imageUrl) ?>">
    <?php endif; ?>
    <meta name="theme-color" content="<?= e($colorMain) ?>">
    <link rel="icon" href="<?= e($faviconUrl) ?>" type="<?= e($faviconType) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=<?= $fontsQuery ?>&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/build/css/site.css')) ?>">
    <style>
        :root {
            --font-body: '<?= e($fontPrimary) ?>', 'Segoe UI', sans-serif;
            --font-heading: '<?= e($fontDecorated) ?>', Georgia, serif;
            --color-brand: <?= e($colorMain) ?>;
            --color-brand-strong: color-mix(in srgb, <?= e($colorMain) ?> 76%, black 24%);
            --color-accent: <?= e($colorAccent) ?>;
            --color-text: <?= e($colorText) ?>;
        }
    </style>
</head>
<body class="page page--<?= e($pageKey) ?>">
    <a class="skip-link" href="#main-content">Przejdź do treści</a>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <?= $content ?>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
    <div class="cookie-banner" id="cookieBanner" style="display:none;">
        <div class="cookie-banner__inner">
            <p class="cookie-banner__text">Ta strona korzysta z plik&oacute;w cookies w celu zapewnienia najlepszej jakości usług. <a href="<?= e(url('polityka-prywatnosci/')) ?>">Polityka prywatności</a></p>
            <button class="button button--small cookie-banner__accept" type="button" id="cookieAccept">Rozumiem</button>
        </div>
    </div>
    <script type="module" src="<?= e(asset('assets/build/js/site.js')) ?>"></script>
</body>
</html>
