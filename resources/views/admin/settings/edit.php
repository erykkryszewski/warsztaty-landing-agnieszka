<section class="admin-page-header">
    <h1><i class="fa-solid fa-sliders"></i> Ustawienia strony</h1>
    <p>Edytuj dane globalne używane przez cały serwis.</p>
</section>

<form class="admin-form admin-form--stacked" method="post" action="<?= e(url('admin/settings/')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php foreach ($sections as $section): ?>
        <section class="admin-panel">
            <header class="admin-panel__header">
                <h2><?= e($section['label']) ?></h2>
            </header>
            <div class="admin-panel__body">
                <?php foreach ($section['fields'] as $field): ?>
                    <?php
                    $value = $section['content'][$field['name']] ?? null;
                    $namePrefix = 'settings[' . $section['key'] . ']';
                    $filePrefix = 'settings_files[' . $section['key'] . ']';
                    $removePrefix = 'settings_remove[' . $section['key'] . ']';
                    $errorPrefix = $section['key'];
                    include __DIR__ . '/../partials/field.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <button class="button admin-form__submit" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Zapisz
    </button>
</form>
