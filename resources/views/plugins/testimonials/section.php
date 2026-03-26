<?php $items = $pluginContent['items'] ?? []; ?>
<?php if ($items !== []): ?>
    <section class="section section--testimonials">
        <div class="container">
            <h2><?= e($pluginContent['section_title'] ?? '') ?></h2>
            <div class="testimonial-grid">
                <?php foreach ($items as $item): ?>
                    <article class="testimonial-card">
                        <p class="testimonial-card__quote"><?= e($item['quote'] ?? '') ?></p>
                        <strong><?= e($item['author'] ?? '') ?></strong>
                        <span><?= e($item['role'] ?? '') ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
