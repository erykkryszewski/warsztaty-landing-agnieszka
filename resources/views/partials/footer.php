<?php
$business = $siteSettings['business'] ?? [];
$logo = trim((string) ($business['logo'] ?? ''));
$logoUrl = content_image($logo);
$logoSize = content_image_dimensions($logo, 260, 90);
$isHome = ($page['key'] ?? '') === 'home';
$reservation = $isHome ? page_group($page, 'reservation') : [];
$footerCtaLabel = trim((string) ($reservation['button_label'] ?? ''));
$footerCtaUrl = content_link($reservation['button_url'] ?? '', '/');
?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <div class="site-footer__content">
            <a class="site-footer__brand" href="<?= e(url('/')) ?>">
                <?php if ($logoUrl !== ''): ?>
                    <img
                        class="site-footer__logo"
                        src="<?= e($logoUrl) ?>"
                        alt=""
                        width="<?= e((string) $logoSize['width']) ?>"
                        height="<?= e((string) $logoSize['height']) ?>"
                        loading="lazy"
                    >
                <?php else: ?>
                    <span><?= e($business['company_name'] ?? config('app.name')) ?></span>
                <?php endif; ?>
            </a>
            <span class="site-footer__name"><?= e($business['company_name'] ?? config('app.name')) ?></span>

            <?php if (!empty($business['email'])): ?>
                <a class="site-footer__contact" href="mailto:<?= e($business['email']) ?>"><?= e($business['email']) ?></a>
            <?php endif; ?>

            <?php if (!empty($business['phone'])): ?>
                <a class="site-footer__contact" href="tel:<?= e($business['phone']) ?>"><?= e($business['phone']) ?></a>
            <?php endif; ?>
        </div>

        <div class="site-footer__actions">
            <?php if ($isHome && $footerCtaLabel !== ''): ?>
                <a class="button button--secondary" href="<?= e($footerCtaUrl) ?>"><?= e($footerCtaLabel) ?></a>
            <?php endif; ?>
            <a class="site-footer__link" href="<?= e(url('polityka-prywatnosci/')) ?>">Polityka prywatności</a>
        </div>
    </div>
</footer>
