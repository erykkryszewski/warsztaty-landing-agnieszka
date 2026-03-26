<section class="admin-page-header">
    <h1><i class="fa-solid fa-pen-to-square"></i> <?= e($formTitle) ?></h1>
</section>

<form class="admin-form admin-form--stacked" method="post" action="<?= e(url(ltrim($formAction, '/'))) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <section class="admin-panel">
        <header class="admin-panel__header">
            <h2>Treść</h2>
        </header>
        <div class="admin-panel__body">
            <div class="admin-field">
                <label class="admin-field__label" for="post-title">Tytuł</label>
                <input id="post-title" type="text" name="title" value="<?= e($post['title']) ?>">
                <?php if (has_error('title')): ?><p class="admin-field__error"><?= e(error_message('title')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-slug">Slug</label>
                <input id="post-slug" type="text" name="slug" value="<?= e($post['slug']) ?>">
                <?php if (has_error('slug')): ?><p class="admin-field__error"><?= e(error_message('slug')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-excerpt">Lead / zajawka</label>
                <textarea id="post-excerpt" name="excerpt" rows="4"><?= e($post['excerpt']) ?></textarea>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-content">Treść</label>
                <textarea id="post-content" name="content" rows="12"><?= e($post['content']) ?></textarea>
                <?php if (has_error('content')): ?><p class="admin-field__error"><?= e(error_message('content')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-external-url">Zewnętrzny URL</label>
                <input id="post-external-url" type="text" name="external_url" value="<?= e($post['external_url'] ?? '') ?>" placeholder="https://example.com/artykul">
                <p class="admin-field__help">Jeśli podany, wpis na liście linkuje do tego URL zamiast do wewnętrznej strony.</p>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <header class="admin-panel__header">
            <h2>Media i ustawienia</h2>
        </header>
        <div class="admin-panel__body">
            <div class="admin-field">
                <label class="admin-field__label" for="post-thumbnail">Miniatura</label>
                <div class="admin-media-field" data-media-field>
                    <input type="hidden" name="thumbnail_remove" value="0" data-media-remove-input>
                    <?php if (!empty($post['thumbnail_path'])): ?>
                        <?php $thumbnailPreviewUrl = str_starts_with($post['thumbnail_path'], 'http') ? $post['thumbnail_path'] : asset(ltrim($post['thumbnail_path'], '/')); ?>
                        <div class="admin-media-card" data-media-card>
                            <img src="<?= e($thumbnailPreviewUrl) ?>" alt="Miniatura wpisu">
                            <button type="button" class="admin-media-card__remove" data-media-remove aria-label="Usuń miniaturę">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <span class="admin-media-card__badge">Do usunięcia po zapisie</span>
                        </div>
                    <?php endif; ?>
                    <input id="post-thumbnail" type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,.svg,.ico" data-media-file>
                </div>
                <?php if (has_error('thumbnail')): ?><p class="admin-field__error"><?= e(error_message('thumbnail')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-status">Status</label>
                <select id="post-status" name="status">
                    <option value="draft" <?= selected($post['status'], 'draft') ?>>Szkic</option>
                    <option value="published" <?= selected($post['status'], 'published') ?>>Opublikowany</option>
                </select>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-published-at">Data publikacji</label>
                <input id="post-published-at" type="datetime-local" name="published_at" value="<?= e($post['published_at']) ?>">
                <?php if (has_error('published_at')): ?><p class="admin-field__error"><?= e(error_message('published_at')) ?></p><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <header class="admin-panel__header">
            <h2><i class="fa-solid fa-magnifying-glass"></i> SEO</h2>
        </header>
        <div class="admin-panel__body">
            <div class="admin-field">
                <label class="admin-field__label" for="post-seo-title">Tytuł SEO</label>
                <input id="post-seo-title" type="text" name="seo_title" value="<?= e($post['seo_title']) ?>">
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="post-seo-desc">Opis SEO</label>
                <textarea id="post-seo-desc" name="seo_description" rows="4"><?= e($post['seo_description']) ?></textarea>
            </div>
        </div>
    </section>

    <button class="button admin-form__submit" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Zapisz
    </button>
</form>
