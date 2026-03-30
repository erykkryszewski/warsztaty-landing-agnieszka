<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;
use DOMDocument;
use DOMElement;
use DOMNode;

class ContentFieldService
{
    public function __construct(private readonly Application $app)
    {
    }

    public function defaultsFromGroups(array $groups): array
    {
        $content = [];

        foreach ($groups as $group) {
            $content[$group['key']] = $this->defaultsFromFields($group['fields']);
        }

        return $content;
    }

    public function defaultsFromFields(array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field['name']] = $this->defaultValue($field);
        }

        return $values;
    }

    public function normalizeGroups(array $groups, array $input): array
    {
        $content = [];

        foreach ($groups as $group) {
            $groupKey = (string) ($group['key'] ?? '');

            if ($groupKey === '') {
                continue;
            }

            $content[$groupKey] = $this->normalizeFields(
                is_array($group['fields'] ?? null) ? $group['fields'] : [],
                is_array($input[$groupKey] ?? null) ? $input[$groupKey] : []
            );
        }

        return $content;
    }

    public function normalizeFields(array $fields, array $input): array
    {
        $values = [];

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');

            if ($name === '') {
                continue;
            }

            $values[$name] = $this->normalizeFieldValue($field, $input[$name] ?? null);
        }

        return $values;
    }

    public function fillGroups(
        array $groups,
        array $input,
        array $files,
        array $removals,
        array $current,
        callable $imageUploader,
        callable $imageRemover
    ): array {
        $data = [];
        $errors = [];

        foreach ($groups as $group) {
            $groupKey = $group['key'];
            $result = $this->fillFields(
                $group['fields'],
                is_array($input[$groupKey] ?? null) ? $input[$groupKey] : [],
                is_array($files[$groupKey] ?? null) ? $files[$groupKey] : [],
                is_array($removals[$groupKey] ?? null) ? $removals[$groupKey] : [],
                is_array($current[$groupKey] ?? null) ? $current[$groupKey] : [],
                $imageUploader,
                $imageRemover,
                $groupKey
            );

            $data[$groupKey] = $result['data'];
            $errors = array_merge($errors, $result['errors']);
        }

        return ['data' => $data, 'errors' => $errors];
    }

    public function fillFields(
        array $fields,
        array $input,
        array $files,
        array $removals,
        array $current,
        callable $imageUploader,
        callable $imageRemover,
        string $pathPrefix = ''
    ): array {
        $data = [];
        $errors = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $path = ltrim($pathPrefix . '.' . $name, '.');
            $value = $input[$name] ?? null;
            $file = $files[$name] ?? null;
            $currentValue = $current[$name] ?? $this->defaultValue($field);

            if ($field['type'] === 'repeater') {
                $items = [];
                $rows = is_array($value) ? array_values($value) : [];
                $rowFiles = is_array($file) ? array_values($file) : [];
                $rowRemovals = is_array($removals[$name] ?? null) ? array_values($removals[$name]) : [];
                $currentRows = is_array($currentValue) ? array_values($currentValue) : [];

                foreach ($rows as $index => $row) {
                    $rowResult = $this->fillFields(
                        $field['fields'] ?? [],
                        is_array($row) ? $row : [],
                        is_array($rowFiles[$index] ?? null) ? $rowFiles[$index] : [],
                        is_array($rowRemovals[$index] ?? null) ? $rowRemovals[$index] : [],
                        is_array($currentRows[$index] ?? null) ? $currentRows[$index] : [],
                        $imageUploader,
                        $imageRemover,
                        $path . '.' . $index
                    );

                    $items[] = $rowResult['data'];
                    $errors = array_merge($errors, $rowResult['errors']);
                }

                $data[$name] = $items;
                continue;
            }

            if ($field['type'] === 'image') {
                $data[$name] = $currentValue;
                $removeRequested = $this->shouldRemoveValue($removals[$name] ?? null);

                if ($removeRequested) {
                    try {
                        $imageRemover($currentValue);
                        $data[$name] = $this->defaultValue($field);
                    } catch (\Throwable $exception) {
                        $errors[$path] = 'Nie udało się usunąć pliku.';
                        continue;
                    }
                }

                if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    try {
                        $data[$name] = $imageUploader($file, $field, is_string($data[$name]) ? $data[$name] : null);
                    } catch (\Throwable $exception) {
                        $errors[$path] = 'Nie udało się zapisać pliku.';
                    }
                }

                continue;
            }

            $normalized = $this->normalizeValue($field, $value, $currentValue);
            $data[$name] = $normalized;

            if (($field['required'] ?? false) && $this->isEmpty($normalized)) {
                $errors[$path] = 'To pole jest wymagane.';
                continue;
            }

            if ($field['type'] === 'email' && $normalized !== '' && !filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
                $errors[$path] = 'Podaj poprawny adres e-mail.';
            }

            if ($field['type'] === 'url' && $normalized !== '' && !$this->isValidUrl($normalized)) {
                $errors[$path] = 'Podaj poprawny adres URL.';
            }
        }

        return ['data' => $data, 'errors' => $errors];
    }

    private function defaultValue(array $field): mixed
    {
        if (array_key_exists('default', $field)) {
            return $field['default'];
        }

        return $field['type'] === 'repeater' ? [] : '';
    }

    private function normalizeFieldValue(array $field, mixed $value): mixed
    {
        if (($field['type'] ?? '') === 'repeater') {
            return $this->normalizeRepeaterValue($field, $value);
        }

        if ($value === null) {
            return $this->defaultValue($field);
        }

        $scalar = $this->flattenScalarValue($value, (string) ($field['name'] ?? ''));

        return match ($field['type'] ?? '') {
            'richtext' => $this->sanitizeRichText(trim($scalar)),
            'textarea' => $scalar,
            'image', 'text', 'email', 'url', 'phone' => trim($scalar),
            default => trim($scalar),
        };
    }

    private function normalizeValue(array $field, mixed $value, mixed $currentValue): mixed
    {
        if ($value === null) {
            return is_string($currentValue) || is_array($currentValue) ? $currentValue : $this->defaultValue($field);
        }

        $value = is_string($value) ? trim($value) : $value;

        return match ($field['type']) {
            'richtext' => $this->sanitizeRichText((string) $value),
            'textarea' => (string) $value,
            'text', 'email', 'url', 'phone' => (string) $value,
            default => is_scalar($value) ? (string) $value : '',
        };
    }

    private function sanitizeRichText(string $html): string
    {
        if (!class_exists(DOMDocument::class)) {
            $html = preg_replace('#<(script|style)[^>]*>.*?</\1>#si', '', $html) ?? $html;

            return strip_tags($html, '<p><br><strong><em><ul><ol><li><a><h2><h3><blockquote>');
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        if ($loaded === false) {
            return '';
        }

        $root = $document->getElementsByTagName('div')->item(0);

        if (!$root instanceof DOMElement) {
            return '';
        }

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $this->sanitizeRichTextNode($child);
        }

        return trim($output);
    }

    private function isEmpty(mixed $value): bool
    {
        if (is_array($value)) {
            return $value === [];
        }

        return trim((string) $value) === '';
    }

    private function isValidUrl(string $value): bool
    {
        if (preg_match('/\s/', $value)) {
            return false;
        }

        if (str_starts_with($value, '//')) {
            return false;
        }

        if (
            str_starts_with($value, '/')
            || str_starts_with($value, '#')
            || str_starts_with($value, '?')
            || str_starts_with($value, './')
            || str_starts_with($value, '../')
        ) {
            return true;
        }

        $parts = parse_url($value);

        if ($parts === false) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));

        if ($scheme !== '') {
            return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
        }

        return isset($parts['path']) && $parts['path'] !== '';
    }

    private function shouldRemoveValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    private function sanitizeRichTextNode(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return htmlspecialchars($node->textContent ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        if ($node->nodeType !== XML_ELEMENT_NODE || !$node instanceof DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);
        $allowedTags = ['p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'h2', 'h3', 'blockquote'];
        $children = '';

        foreach ($node->childNodes as $child) {
            $children .= $this->sanitizeRichTextNode($child);
        }

        if (!in_array($tag, $allowedTags, true)) {
            return $children;
        }

        if ($tag === 'br') {
            return '<br>';
        }

        $attributes = '';

        if ($tag === 'a') {
            $href = trim($node->getAttribute('href'));

            if ($href !== '' && $this->isValidUrl($href)) {
                $attributes = ' href="' . htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
            }
        }

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, $children);
    }

    private function normalizeRepeaterValue(array $field, mixed $value): array
    {
        if (!is_array($value)) {
            return is_array($this->defaultValue($field)) ? $this->defaultValue($field) : [];
        }

        $childFields = is_array($field['fields'] ?? null) ? $field['fields'] : [];
        $childNames = array_values(array_filter(array_map(
            static fn (array $childField): string => (string) ($childField['name'] ?? ''),
            $childFields
        )));

        $rows = $this->looksLikeRepeaterRow($value, $childNames) ? [$value] : array_values($value);
        $normalized = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                $row = count($childNames) === 1 ? [$childNames[0] => $row] : [];
            }

            $normalized[] = $this->normalizeFields($childFields, $row);
        }

        return $normalized;
    }

    private function looksLikeRepeaterRow(array $value, array $childNames): bool
    {
        if ($value === [] || $childNames === [] || array_is_list($value)) {
            return false;
        }

        foreach ($childNames as $childName) {
            if (array_key_exists($childName, $value)) {
                return true;
            }
        }

        return false;
    }

    private function flattenScalarValue(mixed $value, string $fieldName): string
    {
        if (!is_array($value)) {
            return is_scalar($value) ? (string) $value : '';
        }

        if ($fieldName !== '' && array_key_exists($fieldName, $value)) {
            return $this->flattenScalarValue($value[$fieldName], $fieldName);
        }

        if ($value === []) {
            return '';
        }

        if (array_is_list($value)) {
            return $this->flattenScalarValue($value[0], $fieldName);
        }

        return $this->flattenScalarValue(reset($value), $fieldName);
    }
}
