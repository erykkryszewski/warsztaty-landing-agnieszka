<section class="admin-page-header">
    <h1><i class="fa-solid fa-pen-to-square"></i> <?= e($page['admin_label']) ?></h1>
    <p>Adres publiczny: <a href="<?= e(url(ltrim($page['slug'], '/'))) ?>" target="_blank" rel="noreferrer"><?= e($page['slug']) ?> <i class="fa-solid fa-arrow-up-right-from-square"></i></a></p>
</section>

<form class="admin-form admin-form--stacked" method="post" action="<?= e(url('admin/pages/' . $page['key'] . '/')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php foreach ($page['groups'] as $group): ?>
        <section class="admin-panel">
            <header class="admin-panel__header">
                <h2><?= e($group['label']) ?></h2>
            </header>
            <div class="admin-panel__body">
                <?php foreach ($group['fields'] as $field): ?>
                    <?php
                    $value = $page['content'][$group['key']][$field['name']] ?? null;
                    $namePrefix = 'content[' . $group['key'] . ']';
                    $filePrefix = 'content_files[' . $group['key'] . ']';
                    $removePrefix = 'content_remove[' . $group['key'] . ']';
                    $errorPrefix = $group['key'];
                    include __DIR__ . '/../partials/field.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="admin-panel">
        <header class="admin-panel__header">
            <h2><i class="fa-solid fa-magnifying-glass"></i> SEO</h2>
        </header>
        <div class="admin-panel__body">
            <label class="admin-field">
                <span class="admin-field__label">Tytuł SEO</span>
                <input type="text" name="seo[meta_title]" value="<?= e($page['meta_title']) ?>">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Opis SEO</span>
                <textarea name="seo[meta_description]" rows="4"><?= e($page['meta_description']) ?></textarea>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Obraz OG</span>
                <div class="admin-media-field" data-media-field>
                    <input type="hidden" name="seo[meta_image_remove]" value="0" data-media-remove-input>
                    <?php if ($page['meta_image'] !== ''): ?>
                        <?php $metaPreviewUrl = str_starts_with($page['meta_image'], 'http') ? $page['meta_image'] : asset(ltrim($page['meta_image'], '/')); ?>
                        <div class="admin-media-card" data-media-card>
                            <img src="<?= e($metaPreviewUrl) ?>" alt="Obraz OG">
                            <button type="button" class="admin-media-card__remove" data-media-remove aria-label="Usuń obraz OG">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <span class="admin-media-card__badge">Do usunięcia po zapisie</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="content_files[seo][meta_image]" accept=".jpg,.jpeg,.png,.webp,.svg,.ico" data-media-file>
                </div>
                <?php if (has_error('seo.meta_image')): ?><p class="admin-field__error"><?= e(error_message('seo.meta_image')) ?></p><?php endif; ?>
            </label>
        </div>
    </section>

    <button class="button admin-form__submit" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Zapisz
    </button>
</form>
