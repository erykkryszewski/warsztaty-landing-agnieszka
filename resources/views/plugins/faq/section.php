<?php $items = $pluginContent['items'] ?? []; ?>
<?php if ($items !== []): ?>
    <section class="section section--faq">
        <div class="container">
            <h2><?= e($pluginContent['section_title'] ?? '') ?></h2>
            <div class="faq-list">
                <?php foreach ($items as $item): ?>
                    <article class="faq-item">
                        <h3><?= e($item['question'] ?? '') ?></h3>
                        <p><?= e($item['answer'] ?? '') ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
