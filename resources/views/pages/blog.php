<?php $main = page_group($page, 'main'); ?>
<main>
    <section class="hero hero--inner">
        <div class="container">
            <p class="eyebrow"><?= e($main['page_title'] ?? '') ?></p>
            <h1><?= e($main['hero_title'] ?? '') ?></h1>
            <p class="hero__lead"><?= e($main['intro_text'] ?? '') ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container post-list">
            <?php foreach ($posts as $post): ?>
                <?php $postUrl = !empty($post['external_url']) ? $post['external_url'] : url('blog/' . $post['slug'] . '/'); ?>
                <article class="post-card">
                    <div>
                        <h2><a href="<?= e($postUrl) ?>" <?= !empty($post['external_url']) ? 'target="_blank" rel="noreferrer"' : '' ?>><?= e($post['title']) ?></a></h2>
                        <p><?= e($post['excerpt']) ?></p>
                    </div>
                    <a class="button button--ghost button--small" href="<?= e($postUrl) ?>" <?= !empty($post['external_url']) ? 'target="_blank" rel="noreferrer"' : '' ?>>Czytaj więcej</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
