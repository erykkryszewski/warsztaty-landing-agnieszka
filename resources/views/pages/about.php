<?php $main = page_group($page, 'main'); ?>
<main>
    <section class="hero hero--inner">
        <div class="container">
            <p class="eyebrow"><?= e($main['page_title'] ?? '') ?></p>
            <h1><?= e($main['hero_title'] ?? '') ?></h1>
            <p class="hero__lead"><?= e($main['hero_subtitle'] ?? '') ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container prose">
            <?= $main['body'] ?? '' ?>
        </div>
    </section>
</main>
