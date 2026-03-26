<main>
    <section class="hero hero--inner">
        <div class="container">
            <p class="eyebrow">Blog</p>
            <h1><?= e($post['title']) ?></h1>
            <p class="hero__lead"><?= e(date('d.m.Y', strtotime((string) $post['published_at']))) ?></p>
        </div>
    </section>

    <article class="section">
        <div class="container prose">
            <?php if (!empty($post['thumbnail_path'])): ?>
                <img class="post-cover" src="<?= e(asset(ltrim($post['thumbnail_path'], '/'))) ?>" alt="<?= e($post['title']) ?>">
            <?php endif; ?>
            <?= nl2p($post['content']) ?>
        </div>
    </article>
</main>
