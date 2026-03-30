<?php
$business = $siteSettings['business'] ?? [];
$logo = trim((string) ($business['logo'] ?? ''));
$logoUrl = content_image($logo);
$logoSize = content_image_dimensions($logo, 180, 48);
$isHome = ($page['key'] ?? '') === 'home';
$hero = $isHome ? page_group($page, 'hero') : [];
$ctaLabel = trim((string) ($hero['primary_cta_label'] ?? ''));
$ctaUrl = content_link($hero['primary_cta_url'] ?? '', '#rezerwacja');
$ctaExternal = opens_in_new_tab($hero['primary_cta_url'] ?? '');
?>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="site-brand" href="<?= e(url('/')) ?>" aria-label="<?= e($business['company_name'] ?? config('app.name')) ?>">
            <?php if ($logoUrl !== ''): ?>
                <img
                    class="site-brand__logo"
                    src="<?= e($logoUrl) ?>"
                    alt=""
                    width="<?= e((string) $logoSize['width']) ?>"
                    height="<?= e((string) $logoSize['height']) ?>"
                >
            <?php else: ?>
                <span class="site-brand__name"><?= e($business['company_name'] ?? config('app.name')) ?></span>
            <?php endif; ?>
        </a>

        <?php if ($isHome && $ctaLabel !== ''): ?>
            <a class="button site-header__cta" href="<?= e($ctaUrl) ?>"<?= $ctaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= e($ctaLabel) ?></a>
        <?php endif; ?>
    </div>
</header>
