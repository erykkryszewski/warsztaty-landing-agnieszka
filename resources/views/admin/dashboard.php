<section class="admin-page-header">
    <h1><i class="fa-solid fa-gauge"></i> Dashboard</h1>
    <p>Podgląd najważniejszych obszarów CMS.</p>
</section>

<section class="admin-stats">
    <?php
    $icons = [
        'strony' => 'fa-solid fa-file-lines',
        'wpisy' => 'fa-solid fa-pen-to-square',
        'wtyczki' => 'fa-solid fa-puzzle-piece',
        'użytkownicy' => 'fa-solid fa-users',
    ];
    ?>
    <?php foreach ($stats as $label => $value): ?>
        <article class="admin-stat-card">
            <strong><?= e((string) $value) ?></strong>
            <span>
                <i class="<?= e($icons[mb_strtolower($label)] ?? 'fa-solid fa-circle') ?>"></i>
                <?= e(ucfirst($label)) ?>
            </span>
        </article>
    <?php endforeach; ?>
</section>
