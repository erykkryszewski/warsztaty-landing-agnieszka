<?php
$hero = page_group($page, 'hero');
$audience = page_group($page, 'audience');
$retreat = page_group($page, 'retreat');
$pillars = page_group($page, 'pillars');
$benefits = page_group($page, 'benefits');
$methods = page_group($page, 'methods');
$emotion = page_group($page, 'emotion');
$video = page_group($page, 'video');
$team = page_group($page, 'team');
$proof = page_group($page, 'proof');
$included = page_group($page, 'included');
$pricing = page_group($page, 'pricing');
$reservation = page_group($page, 'reservation');
$final = page_group($page, 'final');
$business = $siteSettings['business'] ?? [];

$heroImage = content_image($hero['image'] ?? '');
$heroImageSize = content_image_dimensions($hero['image'] ?? '', 960, 1200);
$emotionImage = content_image($emotion['image'] ?? '');
$emotionImageSize = content_image_dimensions($emotion['image'] ?? '', 960, 1200);
$reservationEmail = trim((string) ($business['email'] ?? ''));

$galleryItems = array_values(array_filter($proof['gallery_items'] ?? [], static fn (array $item): bool => trim((string) ($item['image'] ?? '')) !== ''));
$videoItems = array_values(array_filter($proof['videos'] ?? [], static fn (array $item): bool => trim((string) ($item['url'] ?? '')) !== ''));
$quoteItems = array_values(array_filter($proof['quotes'] ?? [], static fn (array $item): bool => trim((string) ($item['quote'] ?? '')) !== ''));

$galleryDisplayItems = [];
$primaryCtaExternal = opens_in_new_tab($hero['primary_cta_url'] ?? '');
$secondaryCtaExternal = opens_in_new_tab($hero['secondary_cta_url'] ?? '');
$pricingCtaExternal = opens_in_new_tab($pricing['cta_url'] ?? '');
$reservationCtaExternal = opens_in_new_tab($reservation['button_url'] ?? '');
$finalCtaExternal = opens_in_new_tab($final['button_url'] ?? '');

if ($galleryItems !== []) {
    foreach (array_slice($galleryItems, 0, 6) as $item) {
        $imagePath = (string) ($item['image'] ?? '');
        $galleryDisplayItems[] = [
            'item' => $item,
            'image' => content_image($imagePath),
            'size' => content_image_dimensions($imagePath, 900, 1200),
        ];
    }
}
?>
<main id="main-content" class="landing">
    <section class="landing-hero">
        <div class="container landing-hero__grid">
            <div class="landing-hero__content" data-reveal>
                <?php if (!empty($hero['eyebrow'])): ?>
                    <p class="landing-hero__eyebrow"><?= e($hero['eyebrow']) ?></p>
                <?php endif; ?>

                <h1 class="landing-hero__title"><?= e($hero['title'] ?? '') ?></h1>

                <?php if (!empty($hero['lead'])): ?>
                    <p class="landing-hero__lead"><?= e($hero['lead']) ?></p>
                <?php endif; ?>

                <div class="landing-hero__actions">
                    <?php if (!empty($hero['primary_cta_label'])): ?>
                        <a class="button" href="<?= e(content_link($hero['primary_cta_url'] ?? '', '#rezerwacja')) ?>"<?= $primaryCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                            <?= e($hero['primary_cta_label']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($hero['secondary_cta_label'])): ?>
                        <a class="button button--outline" href="<?= e(content_link($hero['secondary_cta_url'] ?? '', '#co-zawiera')) ?>"<?= $secondaryCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                            <?= e($hero['secondary_cta_label']) ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (($hero['info_chips'] ?? []) !== []): ?>
                    <ul class="landing-hero__chips" aria-label="Najważniejsze informacje">
                        <?php foreach (($hero['info_chips'] ?? []) as $index => $item): ?>
                            <?php if (!empty($item['text'])): ?>
                                <li class="landing-chip" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;"><?= e($item['text']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($hero['microcopy'])): ?>
                    <p class="landing-hero__microcopy"><?= e($hero['microcopy']) ?></p>
                <?php endif; ?>
            </div>

            <div class="landing-hero__media" data-reveal>
                <?php if ($heroImage !== ''): ?>
                    <img
                        class="landing-hero__image"
                        src="<?= e($heroImage) ?>"
                        alt="<?= e($hero['image_alt'] ?? '') ?>"
                        width="<?= e((string) $heroImageSize['width']) ?>"
                        height="<?= e((string) $heroImageSize['height']) ?>"
                        fetchpriority="high"
                    >
                <?php else: ?>
                    <div class="landing-hero__placeholder">
                        <span class="landing-hero__placeholder-label"><?= e($hero['eyebrow'] ?? '') ?></span>
                        <p class="landing-hero__placeholder-text"><?= e($hero['lead'] ?? '') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (section_has_content($audience, ['title', 'items'])): ?>
    <section id="dla-kogo" class="landing-section landing-section--soft">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($audience['title'] ?? '') ?></h2>
            </div>

            <ul class="landing-list landing-list--checks">
                <?php foreach (($audience['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['text'])): ?>
                        <li class="landing-list__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <span class="landing-list__icon" aria-hidden="true"><i class="fa-solid fa-heart"></i></span>
                            <span><?= e($item['text']) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <?php if (!empty($audience['closing'])): ?>
                <h3 class="landing-section__highlight" data-reveal><?= e($audience['closing']) ?></h3>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($retreat, ['title', 'items'])): ?>
    <section class="landing-section">
        <div class="container landing-split" data-reveal>
            <div class="landing-split__intro">
                <h2 class="landing-section__title"><?= e($retreat['title'] ?? '') ?></h2>
                <?php if (!empty($retreat['lead'])): ?>
                    <p class="landing-split__lead"><?= e($retreat['lead']) ?></p>
                <?php endif; ?>
            </div>

            <ul class="landing-list">
                <?php foreach (($retreat['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['text'])): ?>
                        <li class="landing-list__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <span class="landing-list__icon" aria-hidden="true"><i class="fa-solid fa-leaf"></i></span>
                            <span><?= e($item['text']) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($pillars, ['title', 'items'])): ?>
    <section class="landing-section landing-section--accent">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($pillars['title'] ?? '') ?></h2>
            </div>

            <div class="landing-card-grid">
                <?php foreach (($pillars['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['title']) || !empty($item['text'])): ?>
                        <article class="landing-card" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <p class="landing-card__index"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></p>
                            <h3 class="landing-card__title"><?= e($item['title'] ?? '') ?></h3>
                            <p class="landing-card__text"><?= e($item['text'] ?? '') ?></p>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($benefits, ['title', 'items'])): ?>
    <section class="landing-section">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($benefits['title'] ?? '') ?></h2>
            </div>

            <?php
            $benefitIcons = [
                'fa-dove',
                'fa-heart',
                'fa-lightbulb',
                'fa-hand-holding-heart',
                'fa-list-check',
                'fa-toolbox',
                'fa-brain',
                'fa-people-group',
                'fa-seedling',
            ];
            ?>
            <ul class="landing-list landing-list--columns">
                <?php foreach (($benefits['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['text'])): ?>
                        <li class="landing-list__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <span class="landing-list__icon" aria-hidden="true"><i class="fa-solid <?= e($benefitIcons[$index] ?? 'fa-star') ?>"></i></span>
                            <span><?= e($item['text']) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($methods, ['title', 'items'])): ?>
    <section class="landing-section landing-section--soft">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($methods['title'] ?? '') ?></h2>
            </div>

            <div class="landing-methods-flow">
                <?php foreach (($methods['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['text'])): ?>
                        <div class="landing-methods-flow__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <span class="landing-methods-flow__marker" aria-hidden="true"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                            <p class="landing-methods-flow__text"><?= e($item['text']) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($emotion, ['title', 'body'])): ?>
    <section class="landing-section">
        <div class="container landing-emotion">
            <div class="landing-emotion__content" data-reveal>
                <h2 class="landing-section__title"><?= e($emotion['title'] ?? '') ?></h2>
                <div class="landing-emotion__text">
                    <?= nl2p($emotion['body'] ?? '') ?>
                </div>
            </div>

            <div class="landing-emotion__media" data-reveal>
                <?php if ($emotionImage !== ''): ?>
                    <img
                        class="landing-emotion__image"
                        src="<?= e($emotionImage) ?>"
                        alt="<?= e($emotion['image_alt'] ?? '') ?>"
                        width="<?= e((string) $emotionImageSize['width']) ?>"
                        height="<?= e((string) $emotionImageSize['height']) ?>"
                        loading="lazy"
                    >
                <?php else: ?>
                    <div class="landing-emotion__placeholder"></div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    $videoPoster = content_image($video['poster'] ?? '');
    $videoPosterSize = content_image_dimensions($video['poster'] ?? '', 1180, 664);
    $videoSrc = trim((string) ($video['video_url'] ?? ''));
    ?>
    <?php if ($videoPoster !== '' && $videoSrc !== ''): ?>
    <section class="landing-section">
        <div class="container">
            <?php if (!empty($video['title'])): ?>
                <div class="landing-section__intro" data-reveal>
                    <h2 class="landing-section__title"><?= e($video['title']) ?></h2>
                </div>
            <?php endif; ?>

            <div class="landing-video-player" data-reveal>
                <button class="landing-video-player__trigger" type="button" aria-label="Odtwórz film" data-video-open>
                    <img
                        class="landing-video-player__poster"
                        src="<?= e($videoPoster) ?>"
                        alt="<?= e($video['poster_alt'] ?? '') ?>"
                        width="<?= e((string) $videoPosterSize['width']) ?>"
                        height="<?= e((string) $videoPosterSize['height']) ?>"
                        loading="lazy"
                    >
                    <span class="landing-video-player__play" aria-hidden="true">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="32" fill="rgba(31,44,37,0.55)"/><path d="M26 20l18 12-18 12V20z" fill="#fff"/></svg>
                    </span>
                </button>
            </div>

            <div class="video-modal" id="videoModal" aria-hidden="true">
                <div class="video-modal__backdrop" data-video-close></div>
                <div class="video-modal__content">
                    <button class="video-modal__close" type="button" data-video-close aria-label="Zamknij film">&times;</button>
                    <video class="video-modal__video" id="videoPlayer" preload="none" controls playsinline>
                        <source src="<?= e(content_image($videoSrc)) ?>" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($team, ['title', 'items'])): ?>
    <section class="landing-section landing-section--soft">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($team['title'] ?? '') ?></h2>
            </div>

            <div class="landing-team">
                <?php foreach (($team['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['name']) || !empty($item['bio'])): ?>
                        <?php
                        $photo = content_image($item['photo'] ?? '');
                        $photoSize = content_image_dimensions($item['photo'] ?? '', 720, 900);
                        ?>
                        <article class="landing-team__card" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <?php if ($photo !== ''): ?>
                                <img
                                    class="landing-team__photo"
                                    src="<?= e($photo) ?>"
                                    alt="<?= e($item['photo_alt'] ?? '') ?>"
                                    width="<?= e((string) $photoSize['width']) ?>"
                                    height="<?= e((string) $photoSize['height']) ?>"
                                    loading="lazy"
                                >
                            <?php endif; ?>
                            <div class="landing-team__body">
                                <h3 class="landing-team__name"><?= e($item['name'] ?? '') ?></h3>
                                <?php if (!empty($item['role'])): ?>
                                    <p class="landing-team__role"><?= e($item['role']) ?></p>
                                <?php endif; ?>
                                <div class="landing-team__bio"><?= nl2p($item['bio'] ?? '') ?></div>
                            </div>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($team['summary'])): ?>
                <h3 class="landing-team__summary" data-reveal><?= e($team['summary']) ?></h3>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($proof, ['title', 'gallery_items', 'videos', 'quotes'])): ?>
    <section class="landing-section">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($proof['title'] ?? '') ?></h2>
                <?php if (!empty($proof['intro'])): ?>
                    <p class="landing-section__lead"><?= e($proof['intro']) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($galleryItems !== []): ?>
                <div class="landing-proof__group">
                    <h3 class="landing-proof__title" data-reveal><?= e($proof['gallery_title'] ?? '') ?></h3>
                    <div class="landing-masonry">
                        <?php foreach ($galleryDisplayItems as $index => $entry): ?>
                            <?php
                            $item = $entry['item'];
                            $image = $entry['image'];
                            $imageSize = $entry['size'];
                            ?>
                            <figure class="landing-masonry__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                                <img
                                    src="<?= e($image) ?>"
                                    alt="<?= e($item['alt'] ?? '') ?>"
                                    width="<?= e((string) $imageSize['width']) ?>"
                                    height="<?= e((string) $imageSize['height']) ?>"
                                    loading="lazy"
                                >
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($videoItems !== []): ?>
                <div class="landing-proof__group">
                    <h3 class="landing-proof__title" data-reveal><?= e($proof['videos_title'] ?? '') ?></h3>
                    <div class="landing-video-list">
                        <?php foreach ($videoItems as $index => $item): ?>
                            <a
                                class="landing-video-card"
                                href="<?= e(content_link($item['url'] ?? '', '#')) ?>"
                                target="_blank"
                                rel="noreferrer"
                                data-reveal
                                style="--reveal-order: <?= e((string) ($index + 1)) ?>;"
                            >
                                <span class="landing-video-card__eyebrow">Video testimonial</span>
                                <strong class="landing-video-card__title"><?= e($item['title'] ?? $item['url']) ?></strong>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($quoteItems !== []): ?>
                <div class="landing-proof__group">
                    <h3 class="landing-proof__title" data-reveal><?= e($proof['quotes_title'] ?? '') ?></h3>
                    <div class="landing-quote-list">
                        <?php foreach ($quoteItems as $index => $item): ?>
                            <blockquote class="landing-quote" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                                <i class="fa-solid fa-quote-left landing-quote__icon" aria-hidden="true"></i>
                                <p class="landing-quote__text">"<?= e($item['quote'] ?? '') ?>"</p>
                                <?php if (!empty($item['author'])): ?>
                                    <footer class="landing-quote__author"><?= e($item['author']) ?></footer>
                                <?php endif; ?>
                            </blockquote>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($included, ['title', 'items'])): ?>
    <section id="co-zawiera" class="landing-section landing-section--accent">
        <div class="container">
            <div class="landing-section__intro" data-reveal>
                <h2 class="landing-section__title"><?= e($included['title'] ?? '') ?></h2>
            </div>

            <div class="landing-offer">
                <?php foreach (($included['items'] ?? []) as $index => $item): ?>
                    <?php if (!empty($item['text'])): ?>
                        <div class="landing-offer__item" data-reveal style="--reveal-order: <?= e((string) ($index + 1)) ?>;">
                            <span class="landing-list__icon" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                            <span><?= e($item['text']) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($pricing, ['title', 'price']) || section_has_content($reservation, ['title', 'body'])): ?>
    <section class="landing-section">
        <div class="container landing-booking">
            <div class="landing-pricing" data-reveal>
                <h2 class="landing-section__title"><?= e($pricing['title'] ?? '') ?></h2>
                <div class="landing-pricing__rows">
                    <div class="landing-pricing__row">
                        <span class="landing-pricing__label"><?= e($pricing['price_label'] ?? '') ?></span>
                        <strong class="landing-pricing__value"><?= e($pricing['price'] ?? '') ?></strong>
                    </div>
                    <div class="landing-pricing__row">
                        <span class="landing-pricing__label"><?= e($pricing['deposit_label'] ?? '') ?></span>
                        <strong class="landing-pricing__value landing-pricing__value--small"><?= e($pricing['deposit'] ?? '') ?></strong>
                    </div>
                    <div class="landing-pricing__row">
                        <span class="landing-pricing__label"><?= e($pricing['payment_label'] ?? '') ?></span>
                        <strong class="landing-pricing__value landing-pricing__value--small"><?= e($pricing['payment_note'] ?? '') ?></strong>
                    </div>
                </div>

                <?php if (!empty($pricing['cta_label'])): ?>
                    <a class="button landing-pricing__button" href="<?= e(content_link($pricing['cta_url'] ?? '', '#rezerwacja')) ?>"<?= $pricingCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                        <?= e($pricing['cta_label']) ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($pricing['microcopy'])): ?>
                    <p class="landing-pricing__microcopy"><?= e($pricing['microcopy']) ?></p>
                <?php endif; ?>
            </div>

            <div id="rezerwacja" class="landing-reservation" data-reveal>
                <h2 class="landing-section__title"><?= e($reservation['title'] ?? '') ?></h2>
                <?php if (!empty($reservation['body'])): ?>
                    <div class="landing-reservation__body"><?= nl2p($reservation['body']) ?></div>
                <?php endif; ?>

                <div class="landing-reservation__actions">
                    <?php if (!empty($reservation['button_label'])): ?>
                        <a class="button" href="<?= e(content_link($reservation['button_url'] ?? '', '#')) ?>"<?= $reservationCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                            <?= e($reservation['button_label']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($reservationEmail !== ''): ?>
                        <a class="landing-reservation__mail" href="mailto:<?= e($reservationEmail) ?>"><?= e($reservationEmail) ?></a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($reservation['note'])): ?>
                    <p class="landing-reservation__note"><?= e($reservation['note']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (section_has_content($final, ['title', 'body'])): ?>
    <section class="landing-section">
        <div class="container">
            <div class="landing-final" data-reveal>
                <div class="landing-final__content">
                    <h2 class="landing-section__title"><?= e($final['title'] ?? '') ?></h2>
                    <?php if (!empty($final['body'])): ?>
                        <p class="landing-final__text"><?= e($final['body']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($final['button_label'])): ?>
                    <a class="button button--white" href="<?= e(content_link($final['button_url'] ?? '', '#rezerwacja')) ?>"<?= $finalCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                        <?= e($final['button_label']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($hero['primary_cta_label'])): ?>
        <div class="sticky-cta">
            <a class="button sticky-cta__button" href="<?= e(content_link($hero['primary_cta_url'] ?? '', '#rezerwacja')) ?>"<?= $primaryCtaExternal ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                <?= e($hero['primary_cta_label']) ?>
            </a>
        </div>
    <?php endif; ?>
</main>
