<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SettingModel;

class SettingsService
{
    private ?array $sectionsCache = null;

    private ?array $valuesCache = null;

    public function __construct(
        private readonly SettingModel $settings,
        private readonly SettingsRegistry $registry,
        private readonly ContentFieldService $fields,
        private readonly UploadService $uploads
    ) {
    }

    public function sections(): array
    {
        if ($this->sectionsCache !== null) {
            return $this->sectionsCache;
        }

        $stored = $this->settings->allIndexed();
        $sections = [];

        foreach ($this->registry->all() as $section) {
            $defaults = $this->fields->defaultsFromFields($section['fields']);
            $payload = $this->fields->normalizeFields(
                $section['fields'],
                $this->decode($stored[$section['key']]['content_json'] ?? null)
            );
            $sections[] = array_merge($section, [
                'content' => $this->mergeDefaults($section['fields'], $this->repairArrayStrings($payload, $defaults)),
            ]);
        }

        $this->sectionsCache = $sections;

        return $this->sectionsCache;
    }

    public function values(): array
    {
        if ($this->valuesCache !== null) {
            return $this->valuesCache;
        }

        $values = [];

        foreach ($this->sections() as $section) {
            $values[$section['key']] = $section['content'];
        }

        $this->valuesCache = $values;

        return $this->valuesCache;
    }

    public function update(array $input, array $files, array $removals = []): array
    {
        $sections = $this->registry->all();
        $currentValues = $this->values();

        $result = $this->fields->fillGroups(
            array_map(static fn (array $section): array => [
                'key' => $section['key'],
                'fields' => $section['fields'],
            ], $sections),
            $input,
            $files,
            $removals,
            $currentValues,
            fn (array $file, array $field, mixed $current): string => $this->uploads->storeImage($file, 'settings', is_string($current) ? $current : null),
            fn (mixed $current) => $this->uploads->removeImage(is_string($current) ? $current : null)
        );

        if ($result['errors'] !== []) {
            return $result;
        }

        foreach ($sections as $section) {
            $defaults = $this->fields->defaultsFromFields($section['fields']);
            $normalized = $this->repairArrayStrings(
                $this->fields->normalizeFields(
                    $section['fields'],
                    is_array($result['data'][$section['key']] ?? null) ? $result['data'][$section['key']] : []
                ),
                $defaults
            );
            $this->settings->upsert($section['key'], json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $this->sectionsCache = null;
        $this->valuesCache = null;

        return [
            'data' => $this->values(),
            'errors' => [],
        ];
    }

    public function repairStoredContent(): void
    {
        $existing = $this->settings->allIndexed();

        foreach ($this->registry->all() as $section) {
            $sectionKey = (string) ($section['key'] ?? '');

            if ($sectionKey === '') {
                continue;
            }

            $row = $existing[$sectionKey] ?? null;

            if (!is_array($row)) {
                continue;
            }

            $decoded = $this->decode($row['content_json'] ?? null);
            $defaults = $this->fields->defaultsFromFields($section['fields']);
            $normalized = $this->repairArrayStrings(
                $this->fields->normalizeFields($section['fields'], $decoded),
                $defaults
            );
            $encoded = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (!is_string($encoded) || $encoded === (string) ($row['content_json'] ?? '')) {
                continue;
            }

            $this->settings->upsert($sectionKey, $encoded);
        }

        $this->sectionsCache = null;
        $this->valuesCache = null;
    }

    private function mergeDefaults(array $fields, array $payload): array
    {
        return array_replace($this->fields->defaultsFromFields($fields), $payload);
    }

    private function repairArrayStrings(mixed $value, mixed $defaults): mixed
    {
        if (is_array($value)) {
            $repaired = [];

            foreach ($value as $key => $item) {
                $repaired[$key] = $this->repairArrayStrings(
                    $item,
                    is_array($defaults) ? ($defaults[$key] ?? null) : null
                );
            }

            return $repaired;
        }

        if (is_string($value) && trim($value) === 'Array') {
            if (is_scalar($defaults)) {
                return (string) $defaults;
            }

            return '';
        }

        return $value;
    }

    private function decode(?string $json): array
    {
        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
