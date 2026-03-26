<?php $main = page_group($page, 'main'); ?>
<main id="main-content" class="legal-page">
    <section class="legal-page__hero">
        <div class="container legal-page__hero-inner" data-reveal>
            <p class="legal-page__eyebrow">Informacje prawne</p>
            <h1 class="legal-page__title"><?= e($main['page_title'] ?? '') ?></h1>
        </div>
    </section>

    <section class="legal-page__content">
        <div class="container legal-page__prose" data-reveal>
            <?= $main['body'] ?? '' ?>
        </div>
    </section>
</main>
