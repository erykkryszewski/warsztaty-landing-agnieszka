<?php
$fieldName = $field['name'];
$fieldType = $field['type'];
$label = $field['label'] ?? $fieldName;
$inputName = field_name($namePrefix, $fieldName);
$fileInputName = field_name($filePrefix, $fieldName);
$removePrefix = $removePrefix ?? '';
$removeInputName = $removePrefix !== '' ? field_name($removePrefix, $fieldName) : '';
$errorKey = field_error_key($errorPrefix, $fieldName);
$fieldValue = $value ?? '';
$fieldValueString = string_value($fieldValue);
?>
<div class="admin-field admin-field--<?= e($fieldType) ?>">
    <label class="admin-field__label" for="<?= e(str_replace(['[', ']'], '_', $inputName)) ?>"><?= e($label) ?></label>

    <?php if ($fieldType === 'text' || $fieldType === 'email' || $fieldType === 'url' || $fieldType === 'phone'): ?>
        <input
            id="<?= e(str_replace(['[', ']'], '_', $inputName)) ?>"
            type="<?= ($fieldType === 'phone' || $fieldType === 'url') ? 'text' : e($fieldType) ?>"
            name="<?= e($inputName) ?>"
            value="<?= e($fieldValueString) ?>"
        >
    <?php elseif ($fieldType === 'textarea' || $fieldType === 'richtext'): ?>
        <textarea
            id="<?= e(str_replace(['[', ']'], '_', $inputName)) ?>"
            name="<?= e($inputName) ?>"
            rows="<?= $fieldType === 'richtext' ? '8' : '4' ?>"
        ><?= e($fieldValueString) ?></textarea>
    <?php elseif ($fieldType === 'image'): ?>
        <div class="admin-media-field" data-media-field>
            <?php if ($removeInputName !== ''): ?>
                <input type="hidden" name="<?= e($removeInputName) ?>" value="0" data-media-remove-input>
            <?php endif; ?>

            <?php if ($fieldValueString !== ''): ?>
                <?php $previewUrl = str_starts_with($fieldValueString, 'http') ? $fieldValueString : asset(ltrim($fieldValueString, '/')); ?>
                <div class="admin-media-card" data-media-card>
                    <img src="<?= e($previewUrl) ?>" alt="<?= e($label) ?>">
                    <?php if ($removeInputName !== ''): ?>
                        <button type="button" class="admin-media-card__remove" data-media-remove aria-label="Usuń obrazek">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <span class="admin-media-card__badge">Do usunięcia po zapisie</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <input
                id="<?= e(str_replace(['[', ']'], '_', $fileInputName)) ?>"
                type="file"
                name="<?= e($fileInputName) ?>"
                accept=".jpg,.jpeg,.png,.webp,.svg,.ico"
                data-media-file
            >
        </div>
    <?php elseif ($fieldType === 'repeater'): ?>
        <?php
        $repeaterField = $field;
        $repeaterLabel = $label;
        $repeaterInputName = $inputName;
        $repeaterErrorKey = $errorKey;
        $repeaterButtonLabel = $repeaterField['button_label'] ?? 'Dodaj element';
        $repeaterFields = is_array($repeaterField['fields'] ?? null) ? $repeaterField['fields'] : [];
        $items = is_array($fieldValue) ? $fieldValue : [];
        $minimumItems = max(0, (int) ($field['min_items'] ?? 0));

        while (count($items) < $minimumItems) {
            $items[] = [];
        }

        $childFilePrefixBase = field_name($filePrefix, $fieldName);
        $childRemovePrefixBase = $removePrefix !== '' ? field_name($removePrefix, $fieldName) : '';
        $imageFieldNames = [];

        foreach ($repeaterFields as $childFieldDefinition) {
            if (($childFieldDefinition['type'] ?? '') === 'image') {
                $imageFieldNames[] = (string) ($childFieldDefinition['name'] ?? '');
            }
        }

        $supportsBulkImages = count($imageFieldNames) === 1 && $imageFieldNames[0] !== '';
        ?>
        <div class="admin-repeater" data-repeater data-repeater-path="<?= e($repeaterInputName) ?>">
            <div class="admin-repeater__items" data-repeater-items>
                <?php foreach ($items as $index => $item): ?>
                    <div class="admin-repeater__item" data-repeater-item data-repeater-index="<?= e((string) $index) ?>">
                        <div class="admin-repeater__toolbar">
                            <strong data-repeater-item-title data-repeater-item-label="<?= e($repeaterLabel) ?>"><?= e($repeaterLabel) ?> #<?= e((string) ($index + 1)) ?></strong>
                            <button type="button" class="button button--danger button--small" data-repeater-remove><i class="fa-solid fa-trash"></i> Usuń</button>
                        </div>
                        <?php
                        $parentField = $field;
                        $parentFieldName = $fieldName;
                        $parentFieldType = $fieldType;
                        $parentLabel = $label;
                        $parentInputName = $inputName;
                        $parentFileInputName = $fileInputName;
                        $parentRemoveInputName = $removeInputName;
                        $parentErrorKey = $errorKey;
                        $parentFieldValue = $fieldValue;
                        $parentFieldValueString = $fieldValueString;
                        $parentValue = $value;
                        $parentNamePrefix = $namePrefix;
                        $parentFilePrefix = $filePrefix;
                        $parentRemovePrefix = $removePrefix;
                        $parentErrorPrefix = $errorPrefix;

                        foreach ($repeaterFields as $childField) {
                            $field = $childField;
                            $value = $item[$childField['name']] ?? null;
                            $namePrefix = $repeaterInputName . '[' . $index . ']';
                            $filePrefix = $childFilePrefixBase . '[' . $index . ']';
                            $removePrefix = $childRemovePrefixBase !== '' ? $childRemovePrefixBase . '[' . $index . ']' : '';
                            $errorPrefix = $repeaterErrorKey . '.' . $index;
                            include __DIR__ . '/field.php';
                        }

                        $field = $parentField;
                        $fieldName = $parentFieldName;
                        $fieldType = $parentFieldType;
                        $label = $parentLabel;
                        $inputName = $parentInputName;
                        $fileInputName = $parentFileInputName;
                        $removeInputName = $parentRemoveInputName;
                        $errorKey = $parentErrorKey;
                        $fieldValue = $parentFieldValue;
                        $fieldValueString = $parentFieldValueString;
                        $value = $parentValue;
                        $namePrefix = $parentNamePrefix;
                        $filePrefix = $parentFilePrefix;
                        $removePrefix = $parentRemovePrefix;
                        $errorPrefix = $parentErrorPrefix;
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <template data-repeater-template>
                <div class="admin-repeater__item" data-repeater-item data-repeater-index="__INDEX__">
                    <div class="admin-repeater__toolbar">
                        <strong data-repeater-item-title data-repeater-item-label="<?= e($repeaterLabel) ?>"><?= e($repeaterLabel) ?></strong>
                        <button type="button" class="button button--danger button--small" data-repeater-remove><i class="fa-solid fa-trash"></i> Usuń</button>
                    </div>
                    <?php
                    $parentField = $field;
                    $parentFieldName = $fieldName;
                    $parentFieldType = $fieldType;
                    $parentLabel = $label;
                    $parentInputName = $inputName;
                    $parentFileInputName = $fileInputName;
                    $parentRemoveInputName = $removeInputName;
                    $parentErrorKey = $errorKey;
                    $parentFieldValue = $fieldValue;
                    $parentFieldValueString = $fieldValueString;
                    $parentValue = $value;
                    $parentNamePrefix = $namePrefix;
                    $parentFilePrefix = $filePrefix;
                    $parentRemovePrefix = $removePrefix;
                    $parentErrorPrefix = $errorPrefix;

                    foreach ($repeaterFields as $childField) {
                        $field = $childField;
                        $value = '';
                        $namePrefix = $repeaterInputName . '[__INDEX__]';
                        $filePrefix = $childFilePrefixBase . '[__INDEX__]';
                        $removePrefix = $childRemovePrefixBase !== '' ? $childRemovePrefixBase . '[__INDEX__]' : '';
                        $errorPrefix = $repeaterErrorKey . '.__INDEX__';
                        include __DIR__ . '/field.php';
                    }

                    $field = $parentField;
                    $fieldName = $parentFieldName;
                    $fieldType = $parentFieldType;
                    $label = $parentLabel;
                    $inputName = $parentInputName;
                    $fileInputName = $parentFileInputName;
                    $removeInputName = $parentRemoveInputName;
                    $errorKey = $parentErrorKey;
                    $fieldValue = $parentFieldValue;
                    $fieldValueString = $parentFieldValueString;
                    $value = $parentValue;
                    $namePrefix = $parentNamePrefix;
                    $filePrefix = $parentFilePrefix;
                    $removePrefix = $parentRemovePrefix;
                    $errorPrefix = $parentErrorPrefix;
                    ?>
                </div>
            </template>

            <?php if ($supportsBulkImages): ?>
                <label class="admin-repeater__bulk-upload">
                    <span class="admin-field__label">Dodaj kilka obrazkow naraz</span>
                    <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg,.ico" multiple data-repeater-bulk-upload>
                    <small class="admin-field__help">Kazdy wybrany plik utworzy osobny element na liscie.</small>
                </label>
            <?php endif; ?>

            <button type="button" class="button button--small admin-repeater__add" data-repeater-add><i class="fa-solid fa-plus"></i> <?= e($repeaterButtonLabel) ?></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($field['help'])): ?>
        <p class="admin-field__help"><?= e($field['help']) ?></p>
    <?php endif; ?>

    <?php if (has_error($errorKey)): ?>
        <p class="admin-field__error"><?= e(error_message($errorKey)) ?></p>
    <?php endif; ?>
</div>
